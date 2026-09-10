<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Customer;
use App\Models\Branch as PerusahaanCabang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Exports\SalesMonthlyExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class PenjualanController extends Controller
{

    public function index(Request $request)
    {

        $search = $request->query('search');
        $filterKategori = $request->query('kategori');
        $filterCabang = $request->query('cabang');
        $status = $request->query('status');
        $sort = $request->query('sort', 'terbaru'); // default = terbaru

        $query = Penjualan::with([
            'customer',
            'perusahaan_cabang',
            'user',
            'detail_penjualan.produk'
        ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qCust) use ($search) {
                        $qCust->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filterCabang)) {
            $query->where('perusahaan_cabang_id', $filterCabang);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($sort === 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $data_penjualan = $query->paginate(10)->withQueryString();

        $semua_cabang = PerusahaanCabang::orderBy('nama')->get();

        if ($request->ajax()) {
            return response()->json([
                'table_html' => view('admin.partials.sales_table_content', ['data_penjualan' => $data_penjualan])->render(),
                'pagination_html' => $data_penjualan->links('pagination::bootstrap-5')->render(),
            ]);
        }


        return view('admin.Datapenjualan', [
            'data_penjualan' => $data_penjualan,
            'semua_cabang' => $semua_cabang,
            'search' => $search,
            'filterCabang' => $filterCabang,
            'status' => $status,
            'sort' => $sort,
        ]);
    }


    public function new()
    {
        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();




        return view('admin.Datapenjualan', [
            'kategori' => $kategori,

        ]);
    }

    public function create(Request $request)
    {

        $search_term = $request->input('search');
        $selected_kategori = $request->input('kategori', 'all');
        $sort_by = $request->input('sort_by', 'terbaru');

        $query = Produk::with(['gambarUtama', 'kategori'])
            ->where('is_archived', false)
            ->where('is_visible', true);

        if ($search_term) {
            $query->where(function($q) use ($search_term) {
                $q->where('nama_produk', 'like', '%' . $search_term . '%')
                  ->orWhere('kode_sku', 'like', '%' . $search_term . '%');
            });
        }

        if ($selected_kategori && $selected_kategori != 'all') {
            $query->where('id_kategori', $selected_kategori);
        }

        switch ($sort_by) {
            case 'nama_asc':
                $query->orderBy('nama_produk', 'asc');
                break;
            case 'harga_asc':
                $query->orderBy('harga_jual', 'asc');
                break;
            case 'terbaru':
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        $products = $query->paginate(20)->withQueryString();

        $kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        $cartSelections = collect(session('cart_penjualan', []))
            ->map(function ($item) {
                return [
                    'id' => isset($item['id']) ? (string) $item['id'] : null,
                    'qty' => max(1, (int) ($item['qty'] ?? 0)),
                    'price' => (int) ($item['price'] ?? 0),
                ];
            })
            ->filter(function ($item) {
                return $item['id'] && $item['qty'] > 0;
            })
            ->values();

        return view('admin.listProdukJual', [
            'products' => $products,
            'kategori' => $kategori,
            'cartSelections' => $cartSelections,
            'search_term' => $search_term,
            'selected_kategori' => $selected_kategori,
            'sort_by' => $sort_by,
        ]);
    }

    public function syncCart(Request $request)
    {
        $items = $request->input('items');

        if (!is_array($items)) {
            return response()->json([
                'message' => 'Format data tidak valid.'
            ], 422);
        }

        $normalized = collect($items)
            ->map(function ($item) {
                $id = isset($item['id']) ? (string) $item['id'] : null;
                $qty = max(0, (int) ($item['qty'] ?? 0));
                $price = (int) ($item['price'] ?? 0);

                return $id ? [
                    'id' => $id,
                    'qty' => $qty,
                    'price' => $price,
                ] : null;
            })
            ->filter(fn($row) => $row && $row['qty'] > 0)
            ->values()
            ->all();

        if (empty($normalized)) {
            session()->forget('cart_penjualan');
        } else {
            session(['cart_penjualan' => $normalized]);
        }

        return response()->json([
            'status' => 'ok',
            'count' => count($normalized),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
        ]);

        $decoded = json_decode($request->input('items'), true) ?? [];
        if (empty($decoded)) {
            session()->forget('cart_penjualan');
            return redirect()->route('admin.sales.create')
                ->withErrors(['items' => 'Tidak ada produk yang dipilih.']);
        }

        $productIds = collect($decoded)->pluck('id')->unique()->values()->all();
        $products = Produk::with(['gambarUtama', 'gambar', 'kategori'])
            ->where('is_archived', false)
            ->where('is_visible', true)
            ->whereIn('id', $productIds)
            ->get();

        $items = [];
        $total = 0;
        $normalizedCart = [];

        foreach ($decoded as $row) {
            $product = $products->firstWhere('id', $row['id']);
            if (!$product) {
                continue;
            }
            $qty = min((int) ($row['qty'] ?? 0), (int) ($product->stok_produk ?? 0));
            if ($qty < 1) {
                continue;
            }
            $price = $product->harga_jual ?? 0;
            $lineTotal = $qty * $price;
            $total += $lineTotal;
            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'price' => $price,
                'line_total' => $lineTotal,
            ];

            $normalizedCart[] = [
                'id' => (string) $product->id,
                'qty' => $qty,
                'price' => $price,
            ];
        }

        if (empty($items)) {
            session()->forget('cart_penjualan');
            return redirect()->route('admin.sales.create')
                ->withErrors(['items' => 'Tidak ada produk yang valid untuk dibuat penjualan.']);
        }

        session(['cart_penjualan' => $normalizedCart]);

        $data_customer = Customer::orderBy('nama', 'asc')->get();
        $data_cabang = PerusahaanCabang::where('is_active', true)->orderBy('nama', 'asc')->get();
        if ($data_cabang->isEmpty()) {
            return redirect()->route('admin.sales.create')
                ->withErrors(['perusahaan_cabang_id' => 'Tidak ada cabang aktif. Aktifkan cabang terlebih dahulu sebelum membuat transaksi.']);
        }
        $daftar_produk = Produk::with(['gambarUtama', 'gambar'])
            ->where('is_archived', false)
            ->where('is_visible', true)
            ->orderBy('nama_produk', 'asc')
            ->get(['id', 'nama_produk', 'kode_sku', 'harga_jual', 'stok_produk']);

        return view('admin.inputPenjualan', [
            'items' => $items,
            'subtotal' => $total,
            'semua_customer' => $data_customer,
            'semua_cabang' => $data_cabang,
            'raw_items' => json_encode($normalizedCart),
            'daftar_produk' => $daftar_produk,
            'biaya_tambahan_awal' => 0,
            'depresiasi_awal' => 0,
        ]);
    }

    public function store(Request $request)
    {

        if ($request->input('from_checkout') == '1') {

            $validated = $request->validate([
                'items' => 'required|string',
            ]);

            return $this->checkout($request);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => [
                'required',
                Rule::exists('perusahaan_cabang', 'id')->where('is_active', true),
            ],
            'kas' => 'required|in:cash,transfer',
            'diskon' => 'nullable|numeric|min:0',
            'biaya_tambahan' => 'nullable|numeric|min:0',
            'depresiasi' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:200',
            'items' => 'required|string',
        ], [
            'perusahaan_cabang_id.exists' => 'Cabang tidak tersedia atau sedang non-aktif.',
        ]);

        [$detailItems, $subtotal, $products] = $this->prepareSaleItems($validated['items']);
        if (empty($detailItems)) {
            return back()->withErrors(['items' => 'Tidak ada item penjualan yang valid.'])->withInput();
        }

        $diskon = (int) ($validated['diskon'] ?? 0);
        $biayaTambahan = (int) ($validated['biaya_tambahan'] ?? 0);
        $hargaTotal = max(0, $subtotal - $diskon + $biayaTambahan);
        $hargaDepresiasi = (float) ($validated['depresiasi'] ?? 0);

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'customer_id' => $validated['customer_id'],
                'perusahaan_cabang_id' => $validated['perusahaan_cabang_id'],
                'user_id' => Auth::id(),
                'harga_total' => $hargaTotal,
                'diskon' => $diskon,
                'kas' => $validated['kas'],
                'keterangan' => $validated['keterangan'] ?? null,
                'tanggal' => now()->toDateString(),
            ]);

            foreach ($detailItems as $detail) {
                $penjualan->detail_penjualan()->create(array_merge($detail, [
                    'harga_depresiasi' => $hargaDepresiasi,
                ]));

                if (
                    isset($products[$detail['produk_id']]) &&
                    !is_null($products[$detail['produk_id']]->stok_produk)
                ) {
                    $products[$detail['produk_id']]->decrement('stok_produk', $detail['qty']);
                }
            }

            DB::commit();

            session()->forget('cart_penjualan');

            return redirect()
                ->route('admin.sales.show', $penjualan->id)
                ->with('success', 'Transaksi penjualan berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penjualan: ' . $th->getMessage())->withInput();
        }
    }

    public function edit(Penjualan $sale)
    {
        $sale->loadMissing(['detail_penjualan.produk.gambarUtama', 'detail_penjualan.produk.gambar']);

        if ($sale->detail_penjualan->isEmpty()) {
            return redirect()->route('admin.sales.index')
                ->with('error', 'Transaksi tidak memiliki detail penjualan untuk diedit.');
        }

        $items = $sale->detail_penjualan->map(function ($detail) {
            $lineTotal = $detail->qty * $detail->harga_jual_satuan;
            return [
                'product' => $detail->produk,
                'qty' => $detail->qty,
                'price' => $detail->harga_jual_satuan,
                'line_total' => $lineTotal,
            ];
        });

        $subtotal = $items->sum(function ($item) {
            return $item['line_total'];
        });

        $rawItems = $sale->detail_penjualan->map(function ($detail) {
            return [
                'id' => $detail->produk_id,
                'qty' => $detail->qty,
            ];
        })->values();

        $diskon = (int) ($sale->diskon ?? 0);
        $additionalFee = max(0, $sale->harga_total - $subtotal + $diskon);
        $defaultDepresiasi = (float) ($sale->detail_penjualan->first()->harga_depresiasi ?? 0);

        $semua_customer = Customer::orderBy('nama', 'asc')->get();
        $semua_cabang = PerusahaanCabang::orderBy('nama', 'asc')
            ->where(function ($query) use ($sale) {
                $query->where('is_active', true)
                    ->orWhere('id', $sale->perusahaan_cabang_id);
            })
            ->get();

        $daftar_produk = Produk::with(['gambarUtama', 'gambar'])
            ->where('is_archived', false)
            ->where('is_visible', true)
            ->orderBy('nama_produk', 'asc')
            ->get(['id', 'nama_produk', 'kode_sku', 'harga_jual', 'stok_produk'])
            ->keyBy('id');

        $qtyByProduct = $sale->detail_penjualan
            ->groupBy('produk_id')
            ->map->sum('qty');

        foreach ($qtyByProduct as $productId => $qty) {
            if (isset($daftar_produk[$productId]) && !is_null($daftar_produk[$productId]->stok_produk)) {
                $daftar_produk[$productId]->stok_produk += $qty;
            }
        }

        return view('admin.inputPenjualan', [
            'penjualan' => $sale,
            'items' => $items,
            'subtotal' => $subtotal,
            'semua_customer' => $semua_customer,
            'semua_cabang' => $semua_cabang,
            'raw_items' => $rawItems->toJson(),
            'daftar_produk' => $daftar_produk->values(),
            'biaya_tambahan_awal' => $additionalFee,
            'depresiasi_awal' => $defaultDepresiasi,
        ]);
    }

    public function show(Penjualan $sale)
    {
        $sale->load([
            'customer',
            'perusahaan_cabang',
            'user',
            'detail_penjualan.produk',
        ]);

        $subtotal = $sale->detail_penjualan->sum(function ($detail) {
            return (int) ($detail->qty ?? 0) * (int) ($detail->harga_jual_satuan ?? 0);
        });

        $diskon = (int) ($sale->diskon ?? 0);
        $biayaTambahan = (int) ($sale->biaya_tambahan ?? 0);
        if ($biayaTambahan === 0 && isset($sale->harga_total)) {
            $biayaTambahan = max(0, (int) $sale->harga_total - $subtotal + $diskon);
        }

        $totalNominal = $sale->harga_total ?? max(0, $subtotal - $diskon + $biayaTambahan);

        return view('admin.showPenjualan', [
            'penjualan' => $sale,
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'biaya_tambahan' => $biayaTambahan,
            'total_nominal' => $totalNominal,
        ]);
    }

    public function update(Request $request, Penjualan $sale)
    {
        $currentBranchId = $sale->perusahaan_cabang_id;
        $validated = $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => [
                'required',
                Rule::exists('perusahaan_cabang', 'id')->where(function ($query) use ($currentBranchId) {
                    $query->where(function ($q) use ($currentBranchId) {
                        $q->where('is_active', true);
                        if ($currentBranchId) {
                            $q->orWhere('id', $currentBranchId);
                        }
                    });
                }),
            ],
            'kas' => 'required|in:cash,transfer',
            'diskon' => 'nullable|numeric|min:0',
            'biaya_tambahan' => 'nullable|numeric|min:0',
            'depresiasi' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:200',
            'items' => 'required|string',
        ], [
            'perusahaan_cabang_id.exists' => 'Cabang tidak tersedia atau sedang non-aktif.',
        ]);

        $diskon = (int) ($validated['diskon'] ?? 0);
        $biayaTambahan = (int) ($validated['biaya_tambahan'] ?? 0);
        $hargaDepresiasi = (float) ($validated['depresiasi'] ?? 0);
        $itemsPayload = $validated['items'];

        DB::beginTransaction();
        try {
            $sale->load('detail_penjualan.produk');

            foreach ($sale->detail_penjualan as $detail) {
                if ($detail->produk && !is_null($detail->produk->stok_produk)) {
                    $detail->produk->increment('stok_produk', $detail->qty);
                }
            }

            $sale->detail_penjualan()->delete();

            [$detailItems, $subtotal, $products] = $this->prepareSaleItems($itemsPayload);
            if (empty($detailItems)) {
                throw new \RuntimeException('Tidak ada detail penjualan yang valid.');
            }

            $hargaTotal = max(0, $subtotal - $diskon + $biayaTambahan);

            $sale->update([
                'customer_id' => $validated['customer_id'],
                'perusahaan_cabang_id' => $validated['perusahaan_cabang_id'],
                'kas' => $validated['kas'],
                'diskon' => $diskon,
                'harga_total' => $hargaTotal,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            foreach ($detailItems as $detail) {
                $sale->detail_penjualan()->create(array_merge($detail, [
                    'harga_depresiasi' => $hargaDepresiasi,
                ]));

                if (
                    isset($products[$detail['produk_id']]) &&
                    !is_null($products[$detail['produk_id']]->stok_produk)
                ) {
                    $products[$detail['produk_id']]->decrement('stok_produk', $detail['qty']);
                }
            }

            DB::commit();

            return redirect()->route('admin.sales.index')->with('success', 'Data penjualan berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui penjualan: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy(Penjualan $sale)
    {
        DB::beginTransaction();
        try {
            $sale->load('detail_penjualan.produk');

            foreach ($sale->detail_penjualan as $detail) {
                if ($detail->produk && !is_null($detail->produk->stok_produk)) {
                    $detail->produk->increment('stok_produk', $detail->qty);
                }
            }

            $sale->delete();

            DB::commit();

            return redirect()->route('admin.sales.index')->with('success', 'Data penjualan berhasil dihapus.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus penjualan: ' . $th->getMessage());
        }
    }

    protected function prepareSaleItems(string $itemsJson): array
    {
        $decoded = json_decode($itemsJson, true) ?? [];
        if (empty($decoded) || !is_array($decoded)) {
            return [[], 0, collect()];
        }

        $productIds = collect($decoded)->pluck('id')->filter()->unique()->values();
        if ($productIds->isEmpty()) {
            return [[], 0, collect()];
        }

        $products = Produk::whereIn('id', $productIds)
            ->where('is_archived', false)
            ->where('is_visible', true)
            ->get()
            ->keyBy('id');

        $detailItems = [];
        $subtotal = 0;

        foreach ($decoded as $item) {
            $productId = (int) ($item['id'] ?? 0);
            if (!$productId || !isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            $requestedQty = (int) ($item['qty'] ?? 0);
            $availableStock = $product->stok_produk === null
                ? PHP_INT_MAX
                : max((int) $product->stok_produk, 0);

            $qty = min($requestedQty, $availableStock);
            if ($qty < 1) {
                continue;
            }

            $price = (int) ($product->harga_jual ?? 0);
            $lineTotal = $qty * $price;
            $subtotal += $lineTotal;

            $detailItems[] = [
                'produk_id' => $productId,
                'qty' => $qty,
                'harga_jual_satuan' => $price,
            ];
        }

        return [$detailItems, $subtotal, $products];
    }

    public function printNota($id)
    {
        $penjualan = Penjualan::with([
            'customer',
            'perusahaan_cabang',
            'user',
            'detail_penjualan.produk',
        ])->findOrFail($id);

        $subtotal = $penjualan->detail_penjualan->sum(function ($detail) {
            $price = (int) ($detail->harga_jual_satuan ?? 0);
            $qty = (int) ($detail->qty ?? 0);
            return $price * $qty;
        });

        $data = [
            'penjualan' => $penjualan,
            'subtotal' => $subtotal,
            'title' => 'Nota Penjualan #'.$penjualan->kode_transaksi,
        ];

        $pdf = Pdf::loadView('admin.notaPenjualan', $data)->setPaper('A4', 'portrait');

        return $pdf->stream('Nota_Penjualan_'.$penjualan->kode_transaksi.'.pdf');
    }

    public function exportMonthlyPdf(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'cabang' => 'nullable|integer',
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth();

        $query = Penjualan::with(['customer', 'perusahaan_cabang', 'detail_penjualan.produk'])
            ->whereBetween('created_at', [$start, $end]);

        $branchLabel = 'Semua Cabang';
        $branchSlug = 'semua-cabang';
        if (!empty($validated['cabang'])) {
            $query->where('perusahaan_cabang_id', $validated['cabang']);
            $branch = PerusahaanCabang::find($validated['cabang']);
            if ($branch) {
                $branchLabel = $branch->nama;
                $branchSlug = Str::slug($branch->nama);
            }
        }

        $rows = $query->orderBy('created_at')->get();

        $totalNominal = $rows->sum(function ($sale) {
            $fallbackTotal = $sale->detail_penjualan->sum(function ($detail) {
                return (int) ($detail->qty ?? 0) * (int) ($detail->harga_jual_satuan ?? 0);
            });
            return ($sale->harga_total ?? 0) > 0 ? $sale->harga_total : $fallbackTotal;
        });

        $totalHpp = $rows->sum(function ($sale) {
            return $sale->detail_penjualan->sum(function ($detail) {
                $hargaBeli = (int) ($detail->produk->harga_beli ?? 0);
                $hargaServis = (int) ($detail->produk->harga_servis ?? 0);
                return (int) ($detail->qty ?? 0) * ($hargaBeli + $hargaServis);
            });
        });

        $pdf = Pdf::loadView('admin.exports.sales-monthly', [
            'rows' => $rows,
            'period' => $start->format('F Y'),
            'totalNominal' => $totalNominal,
            'totalHpp' => $totalHpp,
            'branchLabel' => $branchLabel,
        ])->setPaper('A4', 'landscape');

        $filename = 'Laporan_Penjualan_' . $start->format('Y_m') . '_' . $branchSlug . '.pdf';

        return $pdf->download($filename);
    }

    public function exportMonthlyExcel(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'cabang' => 'nullable|integer',
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $validated['month'])->endOfMonth();

        $query = Penjualan::with(['customer', 'perusahaan_cabang', 'detail_penjualan.produk'])
            ->whereBetween('created_at', [$start, $end]);

        $branchSlug = 'semua-cabang';
        if (!empty($validated['cabang'])) {
            $query->where('perusahaan_cabang_id', $validated['cabang']);
            $branch = PerusahaanCabang::find($validated['cabang']);
            if ($branch) {
                $branchSlug = Str::slug($branch->nama);
            }
        }

        $rows = $query->orderBy('created_at')->get();
        $filename = 'Laporan_Penjualan_' . $start->format('Y_m') . '_' . $branchSlug . '.xlsx';

        return Excel::download(new SalesMonthlyExport($rows), $filename);
    }
}
