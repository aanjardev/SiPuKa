<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()
            ->withCount([
                'penjualan as total_penjualan',
                'pembelian as total_pembelian',
            ]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%")
                  ->orWhere('identitas', 'like', "%{$search}%"); // ← Tambahan pencarian NIK
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

        $data_pelanggan = $query->paginate(10)->withQueryString();

        return view('admin.dataPelanggan', [
            'data_pelanggan' => $data_pelanggan,
            'search_term' => $request->input('search', ''),
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'no_telp' => ['required', 'string', 'max:20', 'regex:/^\d+$/'],
            'identitas' => ['nullable', 'string', 'max:20', 'regex:/^\d+$/'],
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string|max:100',
            'referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ], [
            'no_telp.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'identitas.regex' => 'NIK hanya boleh berisi angka.',
        ]);

        if ($validator->fails()) {

            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }


        $customer = Customer::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan!',
            'customer' => $customer // Kirim data customer baru kembali ke JS
        ]);
    }

    public function show($id)
    {
        $pelanggan = Customer::findOrFail($id);

        $riwayat_penjualan = Penjualan::with([
                'perusahaan_cabang:id,nama',
                'detail_penjualan' => function ($query) {
                    $query->select('id', 'penjualan_id', 'produk_id', 'qty');
                },
                'detail_penjualan.produk:id,nama_produk',
            ])
            ->where('customer_id', $pelanggan->id)
            ->latest('created_at')
            ->get(['id', 'customer_id', 'perusahaan_cabang_id', 'kode_transaksi', 'harga_total', 'kas', 'tanggal', 'created_at']);

        $ringkasan_transaksi = [
            'total_transaksi' => $riwayat_penjualan->count(),
            'total_item' => $riwayat_penjualan->sum(function ($sale) {
                return $sale->detail_penjualan->sum('qty');
            }),
            'total_nilai' => $riwayat_penjualan->sum('harga_total'),
            'transaksi_terakhir' => optional($riwayat_penjualan->first())->created_at,
        ];

        $riwayat_pembelian = Pembelian::with([
                'perusahaan_cabang:id,nama',
                'item_pembelian_draft' => function ($query) {
                    $query->select('id', 'pembelian_id', 'nama_item', 'qty');
                },
            ])
            ->where('customer_id', $pelanggan->id)
            ->latest('created_at')
            ->get([
                'id',
                'customer_id',
                'perusahaan_cabang_id',
                'kode_transaksi',
                'status_pembelian',
                'harga_tawaran_customer',
                'harga_tawaran_toko',
                'harga_deal',
                'created_at',
            ]);

        $ringkasan_pembelian = [
            'total_transaksi' => $riwayat_pembelian->count(),
            'total_deal' => $riwayat_pembelian->where('status_pembelian', 'deal')->count(),
            'total_nominal_deal' => $riwayat_pembelian->where('status_pembelian', 'deal')->sum('harga_deal'),
            'total_item' => $riwayat_pembelian->sum(function ($pembelian) {
                $qty = $pembelian->item_pembelian_draft->sum('qty');
                return $qty > 0 ? $qty : $pembelian->item_pembelian_draft->count();
            }),
            'deal_terakhir' => optional($riwayat_pembelian->firstWhere('status_pembelian', 'deal'))->created_at,
        ];

        return view('admin.inputDataPelanggan', compact(
            'pelanggan',
            'riwayat_penjualan',
            'ringkasan_transaksi',
            'riwayat_pembelian',
            'ringkasan_pembelian'
        ))
            ->with('readOnly', true);
    }

    public function edit($id)
    {
        $pelanggan = Customer::findOrFail($id);
        return view('admin.inputDataPelanggan', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Customer::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50',
            'no_telp' => ['required', 'string', 'max:20', 'regex:/^\d+$/'],
            'email' => 'nullable|email|max:100',
            'identitas' => ['nullable', 'string', 'max:20', 'regex:/^\d+$/'],
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string|max:100',
            'referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'no_telp.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'email.email' => 'Format email tidak valid.',
            'identitas.regex' => 'NIK hanya boleh berisi angka.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $pelanggan->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diperbarui.',
                'customer' => $pelanggan
            ]);
        }

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = Customer::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('admin.customers.index')
                         ->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 3) {
            return response()->json([]);
        }

        $customers = Customer::where('nama', 'LIKE', '%' . $query . '%')
                            ->orWhere('no_telp', 'LIKE', '%' . $query . '%')

                            ->limit(10) // Batasi hasil
                            ->get(['id', 'nama', 'no_telp', 'identitas']);

        $formattedCustomers = $customers->map(function ($customer) {

            $raw = preg_replace('/\D+/', '', $customer->no_telp ?? '');
            $phone = '';
            if ($raw !== '') {
                if (strlen($raw) <= 4) {
                    $phone = $raw;
                } elseif (strlen($raw) <= 8) {
                    $phone = substr($raw, 0, 4) . '-' . substr($raw, 4);
                } else {
                    $phone = substr($raw, 0, 4) . '-' . substr($raw, 4, 4) . '-' . substr($raw, 8);
                }
            }

            $text = $customer->nama;
            if ($phone) {
                $text .= ' (' . $phone . ')';
            } elseif (!empty($customer->no_telp)) {
                $text .= ' (' . $customer->no_telp . ')';
            }





            return [
                'id' => $customer->id,
                'text' => $text,
                'no_telp' => $customer->no_telp,
            ];
        });


        return response()->json($formattedCustomers);
    }

    public function showJson(Customer $customer)
    {
        return response()->json([
            'id' => $customer->id,
            'nama' => $customer->nama,
            'no_telp' => $customer->no_telp,
            'jenis_kelamin' => $customer->jenis_kelamin,
            'alamat' => $customer->alamat,
            'identitas' => $customer->identitas,
            'referensi' => $customer->referensi,
            'keterangan' => $customer->keterangan,
        ]);
    }
}
