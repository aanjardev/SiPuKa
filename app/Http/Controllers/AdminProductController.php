<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductPhotoUploadRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Helpers\ImageUpload;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\GambarProduk;
use App\Jobs\CleanupProductAssets;
use App\Services\ProductService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdminProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function index(Request $request)
    {
        $query = Produk::with(['gambar', 'gambarUtama', 'kategori'])
            ->where('is_archived', false)
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->input('kategori') !== 'all') {
            $query->where('id_kategori', $request->input('kategori'));
        }

        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'nama') {
            $query->orderBy('nama_produk', $sortOrder);
        } elseif ($sortBy === 'nama_desc') {
            $query->orderBy('nama_produk', 'desc');
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $products = $query->paginate(10)->withQueryString();

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.dataProduk', [
            'products' => $products,
            'semua_kategori' => $kategori,
            'search_term' => $request->input('search', ''),
            'selected_kategori' => $request->input('kategori', 'all'),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function show(Produk $product)
    {
        $product->loadMissing(['kategori', 'gambar', 'gambarUtama']);

        $images = $product->gambar
            ->sortByDesc(fn ($img) => (int) ($img->is_main ?? 0))
            ->values();

        $mainImage = $product->gambarUtama ?: $images->first();

        return view('admin.showProduk', [
            'product' => $product,
            'images' => $images,
            'mainImage' => $mainImage,
        ]);
    }

    /**
     * Tampilkan daftar produk yang belum memiliki gambar (perlu diupload foto)
     */
    public function photos(Request $request)
    {
        $query = Produk::with('kategori')
            ->whereDoesntHave('gambar')
            ->where('is_archived', false);

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('nama_produk', 'like', "%{$s}%")
                    ->orWhere('kode_sku', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->input('kategori'));
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            $table_html = view('admin.partials.photo_product_rows', ['products' => $products])->render();
            $pagination_html = $products->hasPages() ? $products->appends($request->query())->links('pagination::bootstrap-5')->render() : '';
            return response()->json([
                'table_html' => $table_html,
                'pagination_html' => $pagination_html,
            ]);
        }

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.dataFotoProduk', [
            'products' => $products,
            'semua_kategori' => $kategori,
            'search_term' => $request->input('search', ''),
            'selected_kategori' => $request->input('kategori', ''),
        ]);
    }

    /**
     * Show upload form for product photos
     */
    public function photosUpload($id)
    {

        $product = Produk::with('photos')->findOrFail($id);
        return view('admin.uploadProductPhotos', [
            'product' => $product,
        ]);
    }

    public function photosUploadStore(ProductPhotoUploadRequest $request, $id)
    {
        $product = Produk::findOrFail($id);

        try {
            $images = collect($request->file('images', []))
                ->flatten()
                ->filter()
                ->all();
            
            // DEBUG: Log berapa file yang diterima
            \Log::info('photosUploadStore - Files received', [
                'product_id' => $product->id,
                'product_name' => $product->nama_produk,
                'file_count' => count($images),
                'files' => array_map(fn($f) => [
                    'name' => $f->getClientOriginalName(),
                    'size' => $f->getSize(),
                    'type' => $f->getMimeType(),
                ], $images),
                'main_image' => $request->input('main_image'),
            ]);
            
            $this->productService->uploadAdditionalPhotos(
                $product,
                $images,
                $request->input('main_image'),
                Auth::id()
            );

            // Setelah submit dari halaman uploadProductPhotos,

            return redirect()
                ->route('admin.products.photos')
                ->with('success', 'Foto berhasil diunggah.');

        } catch (\Throwable $th) {
            \Log::error('photosUploadStore failed', [
                'error' => $th->getMessage(),
                'product_id' => $id,
            ]);
            return back()->withInput()->withErrors(['error' => 'Gagal mengunggah gambar: ' . $th->getMessage()]);
        }
    }

    /**
     * Single AJAX upload for one image. Returns JSON with image data.
     */
    public function photosUploadSingle(Request $request, $id)
    {
        $product = Produk::findOrFail($id);

        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

            DB::beginTransaction();
            try {

                $image = $request->file('image');

                $paths = ImageUpload::upload($image, "product/{$product->id}");
                $path = $paths['path'];

            $hasMain = $product->gambarUtama()->exists();

            $gambar = GambarProduk::create([
                'id_produk' => $product->id,
                'path_gambar' => $path,
                'is_main' => !$hasMain,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'image' => [
                    'id' => $gambar->id,
                    'url' => $gambar->url,
                    'is_main' => $gambar->is_main,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Set a given image as the main image for a product.
     */
    public function setMainPhoto(Request $request, $productId, $imageId)
    {
        $product = Produk::findOrFail($productId);
        $gambar = GambarProduk::where('id_produk', $product->id)->where('id', $imageId)->firstOrFail();

        DB::beginTransaction();
        try {

            GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
            $gambar->is_main = true;
            $gambar->save();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    /**
     * Delete a product image
     */
    public function deletePhoto(Request $request, $productId, $imageId)
    {
        $product = Produk::findOrFail($productId);
        $gambar = GambarProduk::where('id_produk', $product->id)->where('id', $imageId)->firstOrFail();

        DB::beginTransaction();
        try {
            $gambar->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }

    public function create()
    {

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.inputDataProduk', [
            'semua_kategori' => $kategori,
            'product' => null,
        ]);
    }

    public function store(ProductStoreRequest $request)
    {
        try {

            $images = collect($request->file('images', []))
                ->flatten()
                ->filter()
                ->all();

            $product = $this->productService->createProduct(
                $request->validated(),
                $images,
                $request->input('main_image'),
                Auth::id()
            );

            $message = $request->filled('images')
                ? 'Produk berhasil ditambahkan. Foto sedang diproses di latar belakang.'
                : 'Produk berhasil ditambahkan.';

            return redirect()
                ->route('admin.products.index')
                ->with('success', $message);
        } catch (\Throwable $th) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan produk: ' . $th->getMessage()]);
        }
    }


    public function edit(Produk $product)
    {
        if ($product->is_archived) {
            return redirect()
                ->route('admin.products.show', $product->id)
                ->withErrors(['error' => 'Produk diarsipkan. Kembalikan terlebih dahulu untuk mengedit.']);
        }

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        $product->load(['gambar', 'gambarUtama']);

        return view('admin.inputDataProduk', [
            'semua_kategori' => $kategori,
            'product' => $product,
        ]);
    }

    public function update(ProductUpdateRequest $request, Produk $product)
    {
        if ($product->is_archived) {
            return back()->withErrors(['error' => 'Produk diarsipkan. Kembalikan terlebih dahulu untuk mengedit.']);
        }
        try {
            $images = collect($request->file('images', []))
                ->flatten()
                ->filter()
                ->all();

            $this->productService->updateProduct(
                $product,
                $request->validated(),
                $images,
                $request->input('main_image'),
                $request->input('remove_images', []),
                Auth::id()
            );

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Throwable $th) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $th->getMessage()]);
        }
    }

    public function destroy(Produk $product)
    {
        $pathsForCleanup = $product->gambar()->pluck('path_gambar')->filter()->all();

        DB::beginTransaction();

        try {
            $product->gambar()->delete();
            $product->delete();

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            report($e);

            if ($this->isForeignKeyConstraintViolation($e)) {
                return back()->withErrors([
                    'error' => 'Produk tidak dapat dihapus karena sudah digunakan pada transaksi penjualan.',
                ]);
            }

            return back()->withErrors(['error' => 'Gagal menghapus produk. Silakan coba lagi.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);
            return back()->withErrors(['error' => 'Gagal menghapus produk. Silakan coba lagi.']);
        }

        if (!empty($pathsForCleanup)) {
            CleanupProductAssets::dispatch($pathsForCleanup);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function archive(Produk $product)
    {
        $product->update([
            'is_archived' => true,
            'is_visible' => false,
        ]);

        return back()->with('success', 'Produk berhasil diarsipkan. Produk disembunyikan dari katalog.');
    }

    public function archivedIndex(Request $request)
    {
        $query = Produk::with(['gambarUtama', 'kategori'])
            ->where('is_archived', true)
            ->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->input('kategori') !== 'all') {
            $query->where('id_kategori', $request->input('kategori'));
        }

        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'nama') {
            $query->orderBy('nama_produk', $sortOrder);
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $products = $query->paginate(10)->withQueryString();
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.dataProdukArchived', [
            'products' => $products,
            'semua_kategori' => $kategori,
            'search_term' => $request->input('search', ''),
            'selected_kategori' => $request->input('kategori', 'all'),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function restore($id)
    {
        $product = Produk::findOrFail($id);
        $product->update([
            'is_archived' => false,
            'is_visible' => true,
        ]);

        return redirect()
            ->route('admin.products.archived')
            ->with('success', 'Produk berhasil di-restore.');
    }

    private function isForeignKeyConstraintViolation(QueryException $e): bool
    {
        $sqlState = (string)($e->errorInfo[0] ?? '');
        $driverErrorCode = (int)($e->errorInfo[1] ?? 0);

        if ($driverErrorCode === 1451) {
            return true;
        }

        if ($sqlState === '23503') {
            return true;
        }

        return $sqlState === '23000' && str_contains(strtolower($e->getMessage()), 'foreign key');
    }
}
