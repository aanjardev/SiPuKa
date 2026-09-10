<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\CatalogSettings;

use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function suggest(Request $request)
    {
        $search = trim($request->query('q', ''));

        if ($search === '' || mb_strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Produk::with('gambarUtama')
            ->where('is_visible', true)
            ->where('is_archived', false)
            ->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                    ->orWhere('kode_sku', 'LIKE', '%' . $search . '%');
            })
            ->orderByRaw(
                "CASE
                    WHEN nama_produk LIKE ? THEN 0
                    WHEN nama_produk LIKE ? THEN 1
                    ELSE 2
                END",
                [$search . '%', '%' . $search . '%']
            )
            ->orderBy('nama_produk')
            ->limit(5)
            ->get(['id', 'nama_produk', 'harga_jual']);

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->nama_produk,
                'price' => $product->harga_jual,
                'price_formatted' => 'Rp ' . number_format($product->harga_jual, 0, ',', '.'),
                'thumbnail' => $product->gambarUtama?->url ?? asset('images/placeholder.jpg'),
                'url' => route('product.show', $product->id),
            ];
        });

        return response()->json($results);
    }

    public function show($id)
    {

        $produk = Produk::with(['gambar', 'kategori'])
            ->where('is_visible', true)
            ->where('is_archived', false)
            ->findOrFail($id);
        $cat_setting = CatalogSettings::first();

        return view('product-detail', compact('produk', 'cat_setting'));
    }

    public function index(Request $request)
        {

        $kategoris = Kategori::all();

        $search = $request->query('search');
        $kategoriFilter = $request->query('kategori');
        $sort = $request->query('sort', 'terbaru'); // Default sort: terbaru

        $query = Produk::with(['gambarUtama', 'kategori'])
            ->where('is_visible', true)
            ->where('is_archived', false);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                  ->orWhere('kode_sku', 'LIKE', '%' . $search . '%')
                  ->orWhere('deskripsi_produk', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('kategori', function ($qKategori) use ($search) {
                      $qKategori->where('nama_kategori', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        if ($kategoriFilter && $kategoriFilter !== '') {
            $query->where('id_kategori', $kategoriFilter);
        }

        switch ($sort) {
            case 'termurah':
                $query->orderBy('harga_jual', 'asc');
                break;
            case 'termahal':
                $query->orderBy('harga_jual', 'desc');
                break;
            case 'rekomendasi':
                $query->where('grade', 'Unggulan')
                    ->orderBy('created_at', 'desc');
                break;
            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(16);

        return view('product', compact('products', 'kategoris', 'search', 'kategoriFilter', 'sort'));
        }
}
