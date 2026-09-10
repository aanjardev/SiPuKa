<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Helpers\ImageUpload;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Categories::query()->withCount('products as produk_count');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_kategori', 'like', "%{$search}%");
        }

        $sortBy = $request->input('sort_by', 'nama');
        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama_kategori', 'desc');
                break;
            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;
            case 'nama':
            default:
                $query->orderBy('nama_kategori', 'asc');
                break;
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('admin.dataKategori', [
            'categories' => $categories,
            'search_term' => $request->input('search', ''),
            'sort_by' => $sortBy,
        ]);
    }

    public function create()
    {
        return view('admin.inputDataKategori');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|regex:/^[A-Za-z0-9_\\s\\.,\\-]+$/',
            'gambar' => 'nullable|image|max:5120',
        ], [
            'nama_kategori.regex' => 'Nama kategori hanya boleh mengandung huruf, angka, spasi, titik, koma, dan tanda hubung.',
        ]);

        $category = Categories::create([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        if ($request->hasFile('gambar')) {
            $uploaded = ImageUpload::upload(
                $request->file('gambar')->getPathname(),
                "category/{$category->id}"
            );

            $category->update(['path_gambar' => $uploaded['path']]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $count = $category->usedCount();

        if ($count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori tidak dapat dihapus karena masih digunakan oleh {$count} produk.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    public function edit(Categories $category)
    {
        return view('admin.inputDataKategori', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|regex:/^[A-Za-z0-9_\\s\\.,\\-]+$/',
            'gambar' => 'nullable|image|max:5120',
        ], [
            'nama_kategori.regex' => 'Nama kategori hanya boleh mengandung huruf, angka, spasi, titik, koma, dan tanda hubung.',
        ]);

        $category = Categories::findOrFail($id);

        $data = ['nama_kategori' => $validated['nama_kategori']];

        if ($request->hasFile('gambar')) {
            $uploaded = ImageUpload::upload(
                $request->file('gambar')->getPathname(),
                "category/{$category->id}"
            );

            $data['path_gambar'] = $uploaded['path'];
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }
}
