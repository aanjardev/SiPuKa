<?php

namespace App\Http\Controllers;

use App\Models\GambarProduk;
use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $products = Produk::with(['kategori', 'gambarUtama'])->get();
        $kategori = Kategori::all();
        $product = $request->has('edit') ? Produk::with('gambar')->findOrFail($request->edit) : null;
        $totalProduk = Produk::count();
        $stokMenipis = Produk::where('stok_produk', '<', 5)->where('stok_produk', '>', 0)->count();
        $soldOutProducts = Produk::where('stok_produk', '=', 0)->count();
        return view('admin.index', compact('products', 'kategori', 'product', 'totalProduk', 'stokMenipis', 'soldOutProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $kategori = Kategori::all();
        return view('index', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'kode_sku' => 'string|max:100|unique:produk,kode_sku',
            'nama_produk' => 'required|string|max:225',
            'harga_jual' => 'required|numeric',
            'id_kategori' => 'required|exists:kategori,id',
            'stok_produk' => 'required|numeric',
            'deskripsi_produk' => 'required',
            'status' => 'required|in:Baru,Second',
            'grade' => 'required|in:Unggulan,Standar,Minus',
            'gambar_produk.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_image_new' => 'required_with:gambar_produk|integer|min:0',
        ]);

        $produk = Produk::create([
            'kode_sku' => $request->kode_sku,
            'id_kategori' => $request->id_kategori,
            'nama_produk' => $request->nama_produk,
            'harga_jual' => $request->harga_jual,
            'stok_produk' => $request->stok_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'status' => $request->status,
            'grade' => $request->grade,
        ]);

        if ($request->hasFile('gambar_produk')) {
            $mainImageIndex = $request->main_image_new ?? 0;
            foreach ($request->file('gambar_produk') as $index => $file) {
                $path = $file->store('gambar_produk', 'public');
                GambarProduk::create([
                    'id_produk' => $produk->id,
                    'path_gambar' => $path,
                    'is_main' => $index == $mainImageIndex,
                ]);
            }
        }

        return redirect()->route('index')->with('success', 'Produk Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        return redirect()->route('index', ['edit'=> $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


        $produk = Produk::findOrFail($id);
        $request->validate([
            'kode_sku' => 'required|string|max:100|unique:produk,kode_sku,' . $id,
            'nama_produk' => 'required|string|max:255',
            'harga_jual' => 'required|numeric',
            'id_kategori' => 'required|exists:kategori,id',
            'stok_produk' => 'required|integer',
            'deskripsi_produk' => 'required',
            'status' => 'required|in:Baru,Second',
            'grade' => 'required|in:Unggulan,Standar,Minus',
            'gambar_produk.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $produk->update([
            'kode_sku' => $request->kode_sku,
            'id_kategori' => $request->id_kategori,
            'nama_produk' => $request->nama_produk,
            'harga_jual' => $request->harga_jual,
            'stok_produk' => $request->stok_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'status' => $request->status,
            'grade' => $request->grade,
        ]);

        if ($request->has('hapus_gambar_lama')) {
            foreach ($request->hapus_gambar_lama as $idGambar) {
                $gambar = GambarProduk::find($idGambar);
                if ($gambar) {
                    Storage::disk('public')->delete($gambar->path_gambar);
                    $gambar->delete();
                }
            }
        }

        if ($request->filled('main_image_existing')) {
            GambarProduk::where('id_produk', $produk->id)->update(['is_main' => false]);
            GambarProduk::where('id', $request->main_image_existing)->update(['is_main' => true]);

            $mainImages = GambarProduk::where('id_produk', $produk->id)->where('is_main', true)->get();
            if ($mainImages->count() > 1) {
                $mainImages->skip(1)->each(function($img) { $img->update(['is_main' => false]); });
            }
        }

        if ($request->hasFile('gambar_produk')) {
            foreach ($request->file('gambar_produk') as $index => $file) {
                $path = $file->store('gambar_produk', 'public');
                GambarProduk::create([
                    'id_produk' => $produk->id,
                    'path_gambar' => $path,
                    'is_main' => $request->main_image_new == $index,
                ]);
            }

            $mainImages = GambarProduk::where('id_produk', $produk->id)->where('is_main', true)->get();
            if ($mainImages->count() > 1) {
                $mainImages->skip(1)->each(function($img) { $img->update(['is_main' => false]); });
            }
        }

        return redirect()->route('index')->with('success', 'Produk berhasil diperbarui');
    }

    public function deleteImage($id)
    {
        $gambar = GambarProduk::findOrFail($id);

        if (Storage::disk('public')->exists($gambar->path_gambar)) {
            Storage::disk('public')->delete($gambar->path_gambar);
        }

        $gambar->delete();

        return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus.']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $product = Produk::findOrFail($id);
        $gambar = GambarProduk::where('id_produk', $id)->get();
        foreach ($gambar as $g) {
            if (Storage::disk('public')->exists($g->path_gambar)) {
                Storage::disk('public')->delete($g->path_gambar);
                $product->delete();

                return redirect()->route('index')->with('success', 'Produk Berhasil Dihapus');
            }
        }
    }
}
