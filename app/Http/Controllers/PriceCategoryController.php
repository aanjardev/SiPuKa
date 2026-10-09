<?php

namespace App\Http\Controllers;

use App\Models\KategoriHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PriceCategoryController extends Controller
{
    public function index() { return view('admin.price-categories.index', ['categories'=>KategoriHarga::orderBy('urutan')->orderBy('harga_minimum')->get()]); }
    public function create() { return view('admin.price-categories.form'); }
    public function store(Request $request) { $data = $this->validated($request); DB::transaction(function () use ($data) { $last = KategoriHarga::orderByDesc('urutan')->lockForUpdate()->first(); KategoriHarga::create($data + ['urutan' => ($last?->urutan ?? 0) + 1]); }); return redirect()->route('admin.price-categories.index')->with('success', 'Kategori harga berhasil ditambahkan.'); }
    public function edit(KategoriHarga $priceCategory) { return view('admin.price-categories.form', compact('priceCategory')); }
    public function update(Request $request, KategoriHarga $priceCategory) { $priceCategory->update($this->validated($request, $priceCategory)); return redirect()->route('admin.price-categories.index')->with('success', 'Kategori harga berhasil diperbarui.'); }
    public function destroy(KategoriHarga $priceCategory) { if ($priceCategory->produk()->exists() || \App\Models\ItemPembelian::where('id_kategori_harga', $priceCategory->id)->exists()) { return back()->with('error', 'Kategori harga masih digunakan produk atau item QC dan tidak dapat dihapus.'); } $priceCategory->delete(); return redirect()->route('admin.price-categories.index')->with('success', 'Kategori harga berhasil dihapus.'); }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);
        return DB::transaction(function () use ($data) {
            $categories = KategoriHarga::orderBy('id')->lockForUpdate()->get();
            $ids = array_map('intval', $data['ids']);
            $sortedIds = $ids;
            sort($sortedIds);
            if ($sortedIds !== $categories->pluck('id')->all()) {
                return response()->json(['message' => 'Daftar kategori berubah. Muat ulang halaman sebelum mengatur urutan.'], 409);
            }
            foreach ($ids as $index => $id) {
                $categories->firstWhere('id', $id)->update(['urutan' => $index + 1]);
            }
            return response()->json(['message' => 'Urutan kategori harga tersimpan.']);
        });
    }

    private function validated(Request $request, ?KategoriHarga $category = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('nama', ''))]);
        $data = $request->validate([
            'nama'=>['required','string','max:80'],
            'slug'=>['nullable','string','max:80',Rule::unique('kategori_harga','slug')->ignore($category?->id)],
            'harga_minimum'=>['required','integer','min:0'],
            'harga_maksimum'=>['nullable','integer','gt:harga_minimum'],
            'is_active'=>['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
