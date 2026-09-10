@extends('layouts.admin')

@section('title', isset($readOnly) && $readOnly ? 'Detail Data Pelanggan' : 'Edit Data Pelanggan')

@push('page-actions')
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
    @if(isset($readOnly) && $readOnly)
        <a href="{{ route('admin.customers.edit', $pelanggan->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit</span>
        </a>
    @else
        <button type="submit" form="customerForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-save"></i>
            <span>Simpan</span>
        </button>
    @endif
@endpush

@section('content')

{{-- ========================================== --}}
{{-- LAYOUT 1: MODE READ-ONLY (Detail & Riwayat)--}}
{{-- ========================================== --}}
@if(isset($readOnly) && $readOnly)
    <div class="row g-4">

        {{-- KOLOM KIRI: Informasi Pelanggan (Sidebar) --}}
        <div class="col-lg-4 col-xl-3">
            <div class="sticky-top" style="top: 1rem; z-index: 1;">
                @include('admin.partials.customer_detail_card')
            </div>
        </div>

        {{-- KOLOM KANAN: Statistik & Riwayat (Konten Utama) --}}
        <div class="col-lg-8 col-xl-9">
            <div class="d-flex flex-column gap-4">

                {{-- 1. Kartu Ringkasan Penjualan --}}
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Riwayat Transaksi Penjualan</h6>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Stats Row --}}
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Transaksi</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_transaksi['total_transaksi'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Item</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_transaksi['total_item'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-primary bg-opacity-10 rounded-3 border border-primary text-center h-100">
                                    <small class="text-primary d-block mb-1">Total Belanja</small>
                                    <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($ringkasan_transaksi['total_nilai'] ?? 0, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive rounded border">
                            <table class="table table-hover align-middle mb-0 customer-history-table" style="font-size: 0.9rem;">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-3 py-3">No</th>
                                        <th class="py-3">Kode</th>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3">Produk</th>
                                        <th class="py-3">Status</th>
                                        <th class="text-end pe-3 py-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat_penjualan as $index => $penjualan)
                                    <tr class="clickable-row" data-detail-url="{{ route('admin.sales.show', $penjualan->id) }}">
                                        <td class="ps-3 text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="text-muted small">
                                            <span class="fw-semibold text-primary font-monospace">
                                                {{ $penjualan->kode_transaksi ?? ('#' . $penjualan->id) }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <span class="fw-medium text-dark">{{ $penjualan->tanggal ? \Carbon\Carbon::parse($penjualan->tanggal)->format('d M Y') : $penjualan->created_at->format('d M Y') }}</span>
                                            <br>
                                            <span class="opacity-75">{{ $penjualan->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td>
                                            @php
                                                $items = $penjualan->detail_penjualan->take(2);
                                                $extraCount = $penjualan->detail_penjualan->count() - $items->count();
                                            @endphp
                                            <ul class="mb-0 ps-3 small">
                                                @foreach($items as $detail)
                                                    <li>{{ $detail->produk->nama_produk ?? 'Produk' }} (x{{ $detail->qty }})</li>
                                                @endforeach
                                                @if($extraCount > 0)
                                                    <li class="text-muted">+{{ $extraCount }} item lainnya</li>
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Selesai</span>
                                        </td>
                                        <td class="text-end pe-3 fw-bold text-dark">
                                            Rp {{ number_format($penjualan->harga_total ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i><br>
                                            Data riwayat akan muncul di sini
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 2. Kartu Ringkasan Pembelian (Deal/Nego) --}}
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Riwayat Transaksi Pembelian</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Stats Row --}}
                        <div class="row g-3 mb-4">
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Transaksi</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_pembelian['total_transaksi'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Deal</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_pembelian['total_deal'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Item</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_pembelian['total_item'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning text-center h-100">
                                    <small class="text-warning d-block mb-1">Total Nilai Deal</small>
                                    <h5 class="fw-bold text-warning mb-0">Rp {{ number_format($ringkasan_pembelian['total_nominal_deal'] ?? 0, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive rounded border">
                            <table class="table table-hover align-middle mb-0 customer-history-table" style="font-size: 0.9rem;">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-3 py-3">No</th>
                                        <th class="py-3">Kode</th>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3">Produk</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3">Harga Deal</th>
                                        <th class="pe-3 py-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayat_pembelian as $index => $pembelian)
                                    <tr class="clickable-row" data-detail-url="{{ route('admin.purchases.show', $pembelian->id) }}">
                                        <td class="ps-3 text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="text-muted small">
                                            <span class="fw-semibold text-warning font-monospace">
                                                {{ $pembelian->kode_transaksi ?? ('#' . $pembelian->id) }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            <span class="fw-medium text-dark">{{ $pembelian->created_at->format('d M Y') }}</span>
                                            <br>
                                            <span class="opacity-75">{{ $pembelian->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td>
                                            @php
                                                $items = $pembelian->item_pembelian_draft->take(2);
                                                $extraCount = $pembelian->item_pembelian_draft->count() - $items->count();
                                            @endphp
                                            <ul class="mb-0 ps-3 small">
                                                @foreach($items as $item)
                                                    <li>{{ $item->nama_item ?? 'Item' }} (x{{ $item->qty ?? 0 }})</li>
                                                @endforeach
                                                @if($extraCount > 0)
                                                    <li class="text-muted">+{{ $extraCount }} item lainnya</li>
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            @if($pembelian->status_pembelian == 'deal')
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Deal</span>
                                            @elseif($pembelian->status_pembelian == 'tidak_deal')
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">No-Deal</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark">
                                            @if($pembelian->harga_deal)
                                                Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-muted small">
                                            {{ $pembelian->keterangan ?? '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-handshake fa-2x mb-2 opacity-50"></i><br>
                                            Belum ada data riwayat pembelian
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


{{-- ========================================== --}}
{{-- LAYOUT 2: MODE EDIT (Fokus Form Saja)      --}}
{{-- ========================================== --}}
@else
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            @include('admin.partials.customer_detail_card')
        </div>
    </div>
@endif

@endsection

@push('styles')
<style>
    .customer-history-table {
        width: 100%;
        min-width: 100%;
    }
    @media (max-width: 576px) {
        .customer-history-table {
            min-width: 900px;
        }
        .customer-history-table th,
        .customer-history-table td {
            white-space: nowrap;
        }
    }
</style>
@endpush
