<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\CatalogSettings;
use App\Models\KategoriHarga;

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
        $budget = $request->query('budget');
        $sortOptions = [
            'terbaru' => 'Produk Terbaru',
            'termurah' => 'Harga Termurah',
            'termahal' => 'Harga Termahal',
            'rekomendasi' => 'Rekomendasi',
        ];
        $budgetRanges = [
            '500rb' => ['label' => '500 Ribuan', 'min' => 500000, 'max' => 999999],
            '1jt' => ['label' => '1 Jutaan', 'min' => 1000000, 'max' => 1999999],
            '2jt' => ['label' => '2 Jutaan', 'min' => 2000000, 'max' => 2999999],
            '3jt' => ['label' => '3 Jutaan', 'min' => 3000000, 'max' => 3999999],
            '4-5jt' => ['label' => '4–5 Juta', 'min' => 4000000, 'max' => 5999999],
            '6-10jt' => ['label' => '6–10 Juta', 'min' => 6000000, 'max' => 10999999],
            'diatas-10jt' => ['label' => 'Di Atas 10 Juta', 'min' => 11000000, 'max' => null],
        ];
        $budgetRanges = KategoriHarga::active()->get()->mapWithKeys(fn ($range) => [
            $range->slug => [
                'label' => $range->nama,
                'min' => $range->harga_minimum,
                'max' => $range->harga_maksimum,
            ],
        ])->all();

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

        if ($budget && isset($budgetRanges[$budget])) {
            $range = $budgetRanges[$budget];
            if ($range['max'] === null) {
                $query->where('harga_jual', '>=', $range['min']);
            } else {
                $query->whereBetween('harga_jual', [$range['min'], $range['max']]);
            }
        } else {
            $budget = null;
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

        $products = $query->paginate(12)->withQueryString();

        return view('product', compact('products', 'kategoris', 'search', 'kategoriFilter', 'sort', 'sortOptions', 'budget', 'budgetRanges'));
        }
}
