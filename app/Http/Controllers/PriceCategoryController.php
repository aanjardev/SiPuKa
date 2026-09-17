<?php

namespace App\Http\Controllers;

use App\Models\KategoriHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PriceCategoryController extends Controller
{
    public function index() { return view('admin.price-categories.index', ['categories'=>KategoriHarga::orderBy('urutan')->orderBy('harga_minimum')->get()]); }
    public function create() { return view('admin.price-categories.form'); }
    public function store(Request $request) { KategoriHarga::create($this->validated($request)); return redirect()->route('admin.price-categories.index')->with('success', 'Kategori harga berhasil ditambahkan.'); }
    public function edit(KategoriHarga $priceCategory) { return view('admin.price-categories.form', compact('priceCategory')); }
    public function update(Request $request, KategoriHarga $priceCategory) { $priceCategory->update($this->validated($request, $priceCategory)); return redirect()->route('admin.price-categories.index')->with('success', 'Kategori harga berhasil diperbarui.'); }
    public function destroy(KategoriHarga $priceCategory) { $priceCategory->delete(); return redirect()->route('admin.price-categories.index')->with('success', 'Kategori harga berhasil dihapus.'); }

    private function validated(Request $request, ?KategoriHarga $category = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('nama', ''))]);
        $data = $request->validate([
            'nama'=>['required','string','max:80'],
            'slug'=>['nullable','string','max:80',Rule::unique('kategori_harga','slug')->ignore($category?->id)],
            'harga_minimum'=>['required','integer','min:0'],
            'harga_maksimum'=>['nullable','integer','gt:harga_minimum'],
            'urutan'=>['required','integer','min:0'],
            'is_active'=>['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
