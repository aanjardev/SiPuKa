<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\ItemPembelian;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Exports\PurchasesMonthlyExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class PembelianController extends Controller
{
    public function index(Request $request)
    {

        $search = $request->query('search');
        $status = $request->query('status');
        $filterCabang = $request->query('cabang');
        $sort = $request->query('sort', 'terbaru');

        $query = Pembelian::with(['customer', 'perusahaan_cabang', 'user', 'item_pembelian_draft']);

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($qC) use ($search) {
                        $qC->where('nama', 'like', '%' . $search . '%');
                    });
            });
        }
        if (!empty($filterCabang)) {
            $query->where('perusahaan_cabang_id', $filterCabang);
        }

        if ($status && $status != 'semua') {
            $query->where('status_pembelian', $status);
        }

        if ($sort == 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $data_pembelian = $query->paginate(10)->withQueryString();

        $semua_cabang = Branch::orderBy('nama')->get();

        $data = [
            'data_pembelian' => $data_pembelian,
            'search_term' => $search,
            'status_filter' => $status,
            'sort_filter' => $sort,
            'semua_cabang' => $semua_cabang,
            'filter_cabang' => $filterCabang,

        ];

        if ($request->ajax()) {

            $tableHtml = view('admin.partials.purchase_table_content', ['data_pembelian' => $data_pembelian])->render();


            return response()->json([
                'table_html' => $tableHtml,
                'pagination_html' => $data_pembelian->links()->render(),
            ]);
        }

        return view('admin.dataPembelian', $data);
    }


    public function create()
    {

        $data_customer = Customer::orderBy('nama', 'asc')->get();
        $data_cabang = Branch::where('is_active', true)->orderBy('nama', 'asc')->get();
        if ($data_cabang->isEmpty()) {
            return redirect()->route('admin.purchases.index')
                ->with('error', 'Tidak ada cabang aktif. Aktifkan cabang terlebih dahulu sebelum membuat transaksi.');
        }
        $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.inputPembelian', [
            'semua_customer' => $data_customer,
            'semua_cabang' => $data_cabang,
            'semua_kategori' => $data_kategori
        ]);
    }

    /**
     * Menyimpan data pembelian baru dari form wizard.
     */
    public function store(Request $request)
    {
        $status = $request->input('status_pembelian', 'draft');
        $max_int = 2147483647; // Batasan maksimum untuk tipe data INTEGER (32-bit signed) MySQL

        $validator = Validator::make($request->all(), [

            'pembelian_id' => 'required|exists:pembelian,id',
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => [
                'required',
                Rule::exists('perusahaan_cabang', 'id')->where('is_active', true),
            ],
            'user_id' => 'required|exists:users,id',
            'status_pembelian' => 'required|in:draft,deal,tidak_deal',
            'harga_tawaran_customer' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_tawaran_toko' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_deal' => ($status == 'deal' ? 'required' : 'nullable') . '|numeric|min:0|max:' . $max_int,

        ], [
            'harga_deal.required' => 'Harga Deal wajib diisi jika status "Deal".',
            'harga_tawaran_customer.max' => 'Harga Tawaran Customer melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_tawaran_toko.max' => 'Harga Tawaran Toko melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_deal.max' => 'Harga Deal melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'pembelian_id.required' => 'Terjadi kesalahan. Coba muat ulang halaman. (ID Pembelian tidak ditemukan)',
            'perusahaan_cabang_id.exists' => 'Cabang tidak tersedia atau sedang non-aktif.',
        ]);

        if ($validator->fails()) {

            if ($request->pembelian_id) {
                try {

                    $pembelian = Pembelian::with('item_pembelian_draft.kategori')
                        ->findOrFail($request->pembelian_id);

                    $data_customer = Customer::orderBy('nama', 'asc')->get();
                    $data_cabang = Branch::orderBy('nama', 'asc')
                        ->where(function ($query) use ($pembelian) {
                            $query->where('is_active', true)
                                ->orWhere('id', $pembelian->perusahaan_cabang_id);
                        })
                        ->get();
                    $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get(); // Pastikan Kategori di-load

                    return view('admin.inputPembelian', [
                        'pembelian' => $pembelian, // Gantikan input default
                        'semua_customer' => $data_customer,
                        'semua_cabang' => $data_cabang,
                        'semua_kategori' => $data_kategori
                    ])->withErrors($validator)
                        ->withInput();
                } catch (\Exception $e) {

                }
            }
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {

            $pembelian = Pembelian::findOrFail($request->pembelian_id);

            $pembelian->customer_id = $request->customer_id; // Update jika customer diganti
            $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id; // Update jika cabang diganti
            $pembelian->harga_tawaran_customer = $request->harga_tawaran_customer;
            $pembelian->harga_tawaran_toko = $request->harga_tawaran_toko;
            $pembelian->harga_deal = $request->harga_deal;
            $pembelian->status_pembelian = $status;
            $pembelian->save();


            DB::commit();

            if ($status == 'draft') {
                return redirect()->route('admin.purchases.show', $pembelian->id)
                    ->with('success', 'Draft berhasil disimpan')
                    ->with('auto_copy_link', true);
            }

            return redirect()->route('admin.purchases.show', $pembelian->id)
                ->with('success', 'Transaksi pembelian telah difinalisasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Menampilkan detail 1 transaksi pembelian (Read-Only Link)
     */
    public function show($id)
    {
        $pembelian = Pembelian::with([
            'customer',
            'perusahaan_cabang', // Ganti ke 'branch' jika itu nama relasi Anda
            'user',
            'item_pembelian_draft.kategori'
        ])->findOrFail($id);

        return view('admin.reviewPembelian', ['pembelian' => $pembelian]);
    }

    /**
     * Print nota pembelian (Synchronous - Direct Download)
     * This method is kept for direct PDF download functionality
     */
    public function printNota($id)
    {

        $pembelian = Pembelian::with([
            'customer',
            'perusahaan_cabang',
            'user',
            'item_pembelian_draft.kategori'
        ])->findOrFail($id);

        if ($pembelian->status_pembelian !== 'deal') {
            return back()->with('error', 'Nota hanya bisa dicetak untuk transaksi yang statusnya "Deal".');
        }

        $data = [
            'pembelian' => $pembelian,
            'title' => 'Nota Pembelian #' . $pembelian->kode_transaksi
        ];


        $pdf = Pdf::loadView('admin.notaPembelian', $data);

        return $pdf->stream('Nota_Pembelian_' . $pembelian->kode_transaksi . '.pdf');
    }

    public function ajaxStoreItemDraft(Request $request)
    {


        $itemRules = [
            'nama_item' => 'required|string|max:200',
            'kategori_id' => 'required|exists:kategori,id',
            'serial_number' => 'nullable|string|max:50',
            'serial_lens' => 'nullable|string|max:50',
            'kondisi_fisik' => 'nullable|string|max:100',
            'kondisi_baut' => 'nullable|string|max:50',
            'kondisi_tutup_usb' => 'nullable|string|max:50',
            'kondisi_grip' => 'nullable|string|max:50',
            'kondisi_jamur_lensa' => 'nullable|string|max:100',
            'kondisi_view_finder' => 'nullable|string|max:50',
            'kondisi_mounting' => 'nullable|string|max:50',
            'kondisi_slot_memori' => 'nullable|string|max:50',
            'kondisi_jamur_sensor' => 'nullable|string|max:100',
            'kondisi_lcd' => 'nullable|string|max:100',
            'kondisi_tombol' => 'nullable|string|max:50',
            'kondisi_zoom_lensa' => 'nullable|string|max:50',
            'kondisi_af_lensa' => 'nullable|string|max:50',
            'kondisi_diafragma_lensa' => 'nullable|string|max:50',
            'kondisi_kalibrasi_fokus' => 'nullable|string|max:50',
            'kondisi_flash' => 'nullable|string|max:100',
            'kondisi_sound_mic' => 'nullable|string|max:50',
            'kondisi_lain_lain' => 'nullable|string|max:255',
            'kelengkapan_awal' => 'nullable|string', // Ini dari JS
        ];

        $request->merge(['kelengkapan' => $request->input('kelengkapan_awal')]);

        $parentRules = [];
        if (!$request->input('pembelian_id')) {

            $parentRules = [
                'customer_id' => 'required|exists:customer,id',
                'perusahaan_cabang_id' => [
                    'required',
                    Rule::exists('perusahaan_cabang', 'id')->where('is_active', true),
                ],
                'user_id' => 'required|exists:users,id',
            ];
        } else {

            $parentRules = ['pembelian_id' => 'required|exists:pembelian,id'];
        }

        $validator = Validator::make(
            $request->all(),
            array_merge($itemRules, $parentRules),
            [
                'perusahaan_cabang_id.exists' => 'Cabang tidak tersedia atau sedang non-aktif.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $pembelian = null;
            if (!$request->input('pembelian_id')) {

                $pembelian = new Pembelian();
                $pembelian->customer_id = $request->customer_id;
                $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id;
                $pembelian->user_id = $request->user_id;
                $pembelian->status_pembelian = 'draft'; // Status default
                $pembelian->save();
            } else {

                $pembelian = Pembelian::findOrFail($request->input('pembelian_id'));
            }

            $itemData = $request->only((new ItemPembelian)->getFillable());
            $itemData['qty'] = 1; // Sesuai migrasi
            $itemData['status'] = 'Second'; // Sesuai migrasi

            $item = $pembelian->item_pembelian_draft()->create($itemData);

            $item->load('kategori');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil disimpan!',
                'pembelian_id' => $pembelian->id, // Kirim ID pembelian (baru atau lama)
                'item' => $item // Kirim data item yg baru dibuat (lengkap dgn ID DB)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajaxUpdateItemDraft(Request $request, $item_id)
    {
        $itemRules = [
            'nama_item' => 'required|string|max:200',
            'kategori_id' => 'required|exists:kategori,id',
            'serial_number' => 'nullable|string|max:50',
            'serial_lens' => 'nullable|string|max:50',
            'kondisi_fisik' => 'nullable|string|max:100',
            'kondisi_baut' => 'nullable|string|max:50',
            'kondisi_tutup_usb' => 'nullable|string|max:50',
            'kondisi_grip' => 'nullable|string|max:50',
            'kondisi_jamur_lensa' => 'nullable|string|max:100',
            'kondisi_view_finder' => 'nullable|string|max:50',
            'kondisi_mounting' => 'nullable|string|max:50',
            'kondisi_slot_memori' => 'nullable|string|max:50',
            'kondisi_jamur_sensor' => 'nullable|string|max:100',
            'kondisi_lcd' => 'nullable|string|max:100',
            'kondisi_tombol' => 'nullable|string|max:50',
            'kondisi_zoom_lensa' => 'nullable|string|max:50',
            'kondisi_af_lensa' => 'nullable|string|max:50',
            'kondisi_diafragma_lensa' => 'nullable|string|max:50',
            'kondisi_kalibrasi_fokus' => 'nullable|string|max:50',
            'kondisi_flash' => 'nullable|string|max:100',
            'kondisi_sound_mic' => 'nullable|string|max:50',
            'kondisi_lain_lain' => 'nullable|string|max:255',
            'kelengkapan' => 'nullable|string',
        ];

        if ($request->has('kelengkapan_awal') && !$request->has('kelengkapan')) {
            $request->merge(['kelengkapan' => $request->input('kelengkapan_awal')]);
        }

        $validator = Validator::make($request->all(), $itemRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $item = ItemPembelian::findOrFail($item_id);
            $fields = array_keys($itemRules);
            $item->fill($request->only($fields));
            $item->save();
            $item->load('kategori');

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diperbarui.',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {

        $pembelian = Pembelian::with('item_pembelian_draft.kategori')->findOrFail($id);

        $data_customer = Customer::orderBy('nama', 'asc')->get();
        $data_cabang = Branch::orderBy('nama', 'asc')
            ->where(function ($query) use ($pembelian) {
                $query->where('is_active', true)
                    ->orWhere('id', $pembelian->perusahaan_cabang_id);
            })
            ->get();
        $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();


        return view('admin.inputPembelian', [
            'pembelian' => $pembelian, // Kirim data pembelian yang akan diedit
            'semua_customer' => $data_customer,
            'semua_cabang' => $data_cabang,
            'semua_kategori' => $data_kategori
        ]);
    }

    public function update(Request $request, $id)
    {
        $status = $request->input('status_pembelian', 'draft');
        $max_int = 2147483647; // Batasan maksimum untuk tipe data INTEGER (32-bit signed) MySQL
        $pembelianSaatIni = Pembelian::findOrFail($id);


        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => [
                'required',
                Rule::exists('perusahaan_cabang', 'id')->where(function ($query) use ($pembelianSaatIni) {
                    $query->where(function ($q) use ($pembelianSaatIni) {
                        $q->where('is_active', true)
                            ->orWhere('id', $pembelianSaatIni->perusahaan_cabang_id);
                    });
                }),
            ],
            'user_id' => 'required|exists:users,id',
            'status_pembelian' => 'required|in:draft,deal,tidak_deal',
            'harga_tawaran_customer' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_tawaran_toko' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_deal' => ($status == 'deal' ? 'required' : 'nullable') . '|numeric|min:0|max:' . $max_int,
        ], [
            'harga_deal.required' => 'Harga Deal wajib diisi jika status "Deal".',
            'harga_tawaran_customer.max' => 'Harga Tawaran Customer melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_tawaran_toko.max' => 'Harga Tawaran Toko melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_deal.max' => 'Harga Deal melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'perusahaan_cabang_id.exists' => 'Cabang tidak tersedia atau sedang non-aktif.',
        ]);

        if ($validator->fails()) {

            try {

                $pembelian = Pembelian::with('item_pembelian_draft.kategori')
                    ->findOrFail($id);

                $data_customer = Customer::orderBy('nama', 'asc')->get();
                $data_cabang = Branch::orderBy('nama', 'asc')
                    ->where(function ($query) use ($pembelian) {
                        $query->where('is_active', true)
                            ->orWhere('id', $pembelian->perusahaan_cabang_id);
                    })
                    ->get();
                $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

                return view('admin.inputPembelian', [
                    'pembelian' => $pembelian,
                    'semua_customer' => $data_customer,
                    'semua_cabang' => $data_cabang,
                    'semua_kategori' => $data_kategori
                ])->withErrors($validator)
                    ->withInput();
            } catch (\Exception $e) {
                return back()->withErrors($validator)->withInput();
            }
        }

        DB::beginTransaction();
        try {

            $pembelian = $pembelianSaatIni;

            $pembelian->customer_id = $request->customer_id;
            $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id;

            $pembelian->harga_tawaran_customer = $request->harga_tawaran_customer;
            $pembelian->harga_tawaran_toko = $request->harga_tawaran_toko;
            $pembelian->harga_deal = $request->harga_deal;
            $pembelian->status_pembelian = $status;
            $pembelian->save();


            DB::commit();

            if ($status == 'draft') {
                return redirect()->route('admin.purchases.show', $pembelian->id)
                    ->with('success', 'Draft berhasil diupdate')
                    ->with('auto_copy_link', true);
            }

            return redirect()->route('admin.purchases.show', $pembelian->id)
                ->with('success', 'Transaksi pembelian telah diupdate dan difinalisasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {

            $pembelian = Pembelian::findOrFail($id);


            $pembelian->delete();

            return redirect()->route('admin.purchases.index')
                ->with('success', 'Transaksi Pembelian ' . $pembelian->kode_transaksi . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * =======================================================
     * METHOD BARU 2: Hapus Item via AJAX
     * =======================================================
     */
    public function ajaxDeleteItemDraft($item_id)
    {
        try {

            $item = ItemPembelian::findOrFail($item_id);

            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportMonthlyPdf(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'cabang' => 'nullable|integer',
            'status' => 'nullable|string',
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth();

        $query = Pembelian::with(['customer', 'perusahaan_cabang', 'item_pembelian_draft'])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status_pembelian', ['deal', 'tidak_deal']);

        $branchLabel = 'Semua Cabang';
        $branchSlug = 'semua-cabang';
        if (!empty($validated['cabang'])) {
            $query->where('perusahaan_cabang_id', $validated['cabang']);
            $branch = Branch::find($validated['cabang']);
            if ($branch) {
                $branchLabel = $branch->nama;
                $branchSlug = Str::slug($branch->nama);
            }
        }

        if (!empty($validated['status']) && $validated['status'] !== 'semua') {
            $query->where('status_pembelian', $validated['status']);
        }

        $rows = $query->orderBy('created_at')->get();
        $totalDeal = $rows->sum(function ($purchase) {
            return (int) ($purchase->harga_deal ?? 0);
        });
        $totalItemDeal = $rows->sum(function ($purchase) {
            if (($purchase->status_pembelian ?? null) !== 'deal') {
                return 0;
            }
            return $purchase->item_pembelian_draft->sum(function ($item) {
                return (int) ($item->qty ?? 0);
            });
        });
        $totalTransaksiDeal = $rows->where('status_pembelian', 'deal')->count();
        $totalTransaksiNoDeal = $rows->where('status_pembelian', 'tidak_deal')->count();

        $pdf = Pdf::loadView('admin.exports.purchases-monthly', [
            'rows' => $rows,
            'period' => $start->format('F Y'),
            'totalDeal' => $totalDeal,
            'totalItemDeal' => $totalItemDeal,
            'totalTransaksiDeal' => $totalTransaksiDeal,
            'totalTransaksiNoDeal' => $totalTransaksiNoDeal,
            'branchLabel' => $branchLabel,
        ])->setPaper('A4', 'landscape');

        $filename = 'Laporan_Pembelian_' . $start->format('Y_m') . '_' . $branchSlug . '.pdf';

        return $pdf->download($filename);
    }

    public function exportMonthlyExcel(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'cabang' => 'nullable|integer',
            'status' => 'nullable|string',
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth();

        $query = Pembelian::with(['customer', 'perusahaan_cabang', 'item_pembelian_draft'])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status_pembelian', ['deal', 'tidak_deal']);

        $branchSlug = 'semua-cabang';
        if (!empty($validated['cabang'])) {
            $query->where('perusahaan_cabang_id', $validated['cabang']);
            $branch = Branch::find($validated['cabang']);
            if ($branch) {
                $branchSlug = Str::slug($branch->nama);
            }
        }

        if (!empty($validated['status']) && $validated['status'] !== 'semua') {
            $query->where('status_pembelian', $validated['status']);
        }

        $rows = $query->orderBy('created_at')->get();
        $filename = 'Laporan_Pembelian_' . $start->format('Y_m') . '_' . $branchSlug . '.xlsx';

        return Excel::download(new PurchasesMonthlyExport($rows), $filename);
    }
}
