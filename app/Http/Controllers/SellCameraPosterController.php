<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellCameraPosterRequest;
use App\Models\SellCameraPoster;
use App\Services\SellCameraPosterService;
use Illuminate\Http\Request;

class SellCameraPosterController extends Controller
{
    public function index()
    {
        $posters = SellCameraPoster::orderBy('position')->orderBy('id')->get();

        return view('admin.sell-camera.index', compact('posters'));
    }

    public function store(SellCameraPosterRequest $request, SellCameraPosterService $service)
    {
        $created = $service->uploadBatch(array_values($request->file('images')), ($request->validated('alt_texts') ?? []), $request->validated('submission_token'));

        return redirect()->route('admin.sell-camera-posters.index')->with('success', $created ? 'Poster berhasil ditambahkan.' : 'Unggahan ini sudah disimpan sebelumnya.');
    }

    public function edit(SellCameraPoster $poster)
    {
        return view('admin.sell-camera.edit', compact('poster'));
    }

    public function update(SellCameraPosterRequest $request, SellCameraPoster $poster, SellCameraPosterService $service)
    {
        $service->update($poster, $request->validated(), $request->file('image'));

        return redirect()->route('admin.sell-camera-posters.index')->with('success', 'Poster berhasil diperbarui.');
    }

    public function destroy(SellCameraPoster $poster, SellCameraPosterService $service)
    {
        $service->delete($poster);

        return redirect()->route('admin.sell-camera-posters.index')->with('success', 'Poster berhasil dihapus.');
    }

    public function move(Request $request, SellCameraPoster $poster, SellCameraPosterService $service)
    {
        $data = $request->validate(['direction' => ['required', 'in:up,down'], 'expected_position' => ['required', 'integer', 'min:1']]);
        $service->move($poster, $data['direction'], (int) $data['expected_position']);

        return redirect()->route('admin.sell-camera-posters.index')->with('success', 'Urutan poster diperbarui.');
    }
}
