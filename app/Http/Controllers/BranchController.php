<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\JamOperasionalCabang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->input('sort_by', 'updated_at');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'nama') {
            $query->orderBy('nama', 'asc');
        } elseif ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } else {
            $query->orderBy('updated_at', $sortOrder);
        }

        $data_cabang = $query->with('jamOperasional')->paginate(10)->withQueryString();

        return view('admin.dataCabang', [
            'data_cabang' => $data_cabang,
            'search_term' => $request->input('search', ''),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function create()
    {
        $hariList = JamOperasionalCabang::getAllHari();
        return view('admin.inputDataCabang', [
            'hariList' => $hariList
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž0-9\s\.,\-]+$/'
            ],
            'alamat' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z0-9À-ž\s\.,\-\/]+$/'
            ],
            'nomor_telepon' => [
                'required',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'link_maps' => [
                'nullable',
                'url',
                'max:255',
                Rule::unique('perusahaan_cabang', 'link_maps'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_active' => [
                'boolean',
            ],
            'jam_buka' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],
            'jam_tutup' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],

            'jam_operasional' => ['required', 'array', 'size:7'],
            'jam_operasional.*.is_buka' => ['required', 'boolean'],
            'jam_operasional.*.jam_buka' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],
            'jam_operasional.*.jam_tutup' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],
            'jam_operasional.*.catatan' => ['nullable', 'string', 'max:200'],
        ],
        [
            'nama.regex' => 'Nama cabang hanya boleh mengandung huruf, angka, spasi, titik, koma, dan tanda hubung',
            'alamat.regex' => 'Alamat hanya boleh huruf, angka, spasi, titik, koma, garis miring, dan tanda hubung.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'link_maps.url' => 'Link Maps harus berupa URL yang valid.',
            'link_maps.unique' => 'Link Google Maps sudah terdaftar.',
            'jam_operasional.required' => 'Jam operasional harus diisi untuk semua hari.',
            'jam_operasional.size' => 'Jam operasional harus diisi untuk 7 hari.',
        ]);

        DB::beginTransaction();
        try {

            $branch = Branch::create($validated);

            foreach ($validated['jam_operasional'] as $hari => $jamData) {
                JamOperasionalCabang::create([
                    'perusahaan_cabang_id' => $branch->id,
                    'hari' => $hari,
                    'is_buka' => $jamData['is_buka'],
                    'jam_buka' => $jamData['is_buka'] ? ($jamData['jam_buka'] ?? null) : null,
                    'jam_tutup' => $jamData['is_buka'] ? ($jamData['jam_tutup'] ?? null) : null,
                    'catatan' => $jamData['catatan'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data cabang: ' . $e->getMessage());
        }

        return redirect()->route('admin.branches.index')
        ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function show(Branch $branch)
    {
        $branch->load('jamOperasional');
        
        return view('admin.showDataCabang', [
            'branch' => $branch,
        ]);
    }

    public function edit(Branch $branch)
    {
        $branch->load('jamOperasional');
        $hariList = JamOperasionalCabang::getAllHari();
        
        return view('admin.inputDataCabang', [
            'branch' => $branch,
            'isShow' => false,
            'hariList' => $hariList
        ]);
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        if (!$branch->is_active) {
            return redirect()->route('admin.branches.index')
                ->with('info', 'Cabang sudah dalam kondisi non-aktif.');
        }

        $branch->update(['is_active' => false]);

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil dinonaktifkan. Cabang non-aktif tidak dapat digunakan untuk transaksi baru.');
    }

    public function updateStatus(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $branch->update(['is_active' => $validated['is_active']]);

        $message = $validated['is_active']
            ? 'Cabang berhasil diaktifkan.'
            : 'Cabang berhasil dinonaktifkan. Cabang non-aktif tidak dapat digunakan untuk transaksi baru.';

        return redirect()->route('admin.branches.index')->with('success', $message);
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-zÀ-ž0-9\s\.,\-]+$/'
            ],
            'alamat' => [
                'required',
                'string',
                'max:150',
                'regex:/^[A-Za-z0-9À-ž\s\.,\-\/]+$/'
            ],
            'nomor_telepon' => [
                'required',
                'regex:/^(?:\+62|62|0)[0-9]{8,15}$/'
            ],
            'link_maps' => [
                'nullable',
                'url',
                'max:255',
                Rule::unique('perusahaan_cabang', 'link_maps')->ignore($branch->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_active' => [
                'boolean',
            ],
            'jam_buka' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],
            'jam_tutup' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],

            'jam_operasional' => ['required', 'array', 'size:7'],
            'jam_operasional.*.is_buka' => ['required', 'boolean'],
            'jam_operasional.*.jam_buka' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],
            'jam_operasional.*.jam_tutup' => ['nullable', 'regex:/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/'],
            'jam_operasional.*.catatan' => ['nullable', 'string', 'max:200'],
        ], [
            'nama.regex' => 'Nama cabang hanya boleh mengandung huruf, spasi, titik, koma, dan tanda hubung.',
            'alamat.regex' => 'Alamat hanya boleh huruf, angka, spasi, titik, koma, garis miring, dan tanda hubung.',
            'nomor_telepon.regex' => 'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.',
            'link_maps.url' => 'Link Maps harus berupa URL yang valid.',
            'link_maps.unique' => 'Link Google Maps sudah terdaftar.',
            'jam_operasional.required' => 'Jam operasional harus diisi untuk semua hari.',
            'jam_operasional.size' => 'Jam operasional harus diisi untuk 7 hari.',
        ]);

        DB::beginTransaction();
        try {

            $branch->update($validated);

            $branch->jamOperasional()->delete();

            foreach ($validated['jam_operasional'] as $hari => $jamData) {
                JamOperasionalCabang::create([
                    'perusahaan_cabang_id' => $branch->id,
                    'hari' => $hari,
                    'is_buka' => $jamData['is_buka'],
                    'jam_buka' => $jamData['is_buka'] ? ($jamData['jam_buka'] ?? null) : null,
                    'jam_tutup' => $jamData['is_buka'] ? ($jamData['jam_tutup'] ?? null) : null,
                    'catatan' => $jamData['catatan'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data cabang: ' . $e->getMessage());
        }

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }
}
