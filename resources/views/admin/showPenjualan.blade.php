@extends('layouts.admin')

@section('title', 'Detail Penjualan')

@push('page-actions')
    {{-- Tombol Kembali ke Daftar --}}
    <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left me-1"></i>
        <span>Kembali</span>
    </a>

    {{-- Tombol Edit Transaksi --}}
    <a href="{{ route('admin.sales.edit', $penjualan->id) }}" class="btn btn-outline-dark-gold btn-sm d-flex align-items-center gap-2 border">
        <i class="fas fa-pen-to-square fa-fw"></i>
        <span>Edit Transaksi</span>
    </a>

    {{-- Tombol Cetak Nota (status penjualan selalu deal) --}}
    <a href="{{ route('admin.sales.print', $penjualan->id) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" target="_blank">
        <i class="fas fa-print fa-fw"></i>
        <span>Cetak Nota</span>
    </a>
@endpush

@section('content')
@php

    $subtotal = isset($subtotal) ? (int) $subtotal : $penjualan->detail_penjualan->sum(function ($d) {
        return (int) ($d->qty ?? 0) * (int) ($d->harga_jual_satuan ?? 0);
    });
    $diskon = isset($diskon) ? (int) $diskon : (int) ($penjualan->diskon ?? 0);
    $biayaTambahan = isset($biaya_tambahan) ? (int) $biaya_tambahan : (int) ($penjualan->biaya_tambahan ?? 0);

    if ($biayaTambahan === 0 && isset($penjualan->harga_total)) {
        $biayaTambahan = max(0, (int) $penjualan->harga_total - $subtotal + $diskon);
    }

    $totalNominal = isset($total_nominal)
        ? (int) $total_nominal
        : ($penjualan->harga_total ?? max(0, $subtotal - $diskon + $biayaTambahan));
@endphp

<div class="row g-4 align-items-stretch">
    {{-- KOLOM KIRI: Informasi Transaksi --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #e0edff;">
                        <i class="fa-solid fa-file-invoice text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Informasi Transaksi</h6>
                        <small class="text-muted">Ringkasan penjualan</small>
                    </div>
                </div>

                <div class="row small">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">No. Transaksi</dt>
                            <dd class="col-7 fw-semibold">{{ $penjualan->kode_transaksi ?? ('#' . $penjualan->id) }}</dd>

                            <dt class="col-5 text-muted">Tanggal</dt>
                            <dd class="col-7">{{ $penjualan->created_at?->format('d M Y, H:i') ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Kasir</dt>
                            <dd class="col-7">{{ $penjualan->user->name ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Cabang</dt>
                            <dd class="col-7">{{ $penjualan->perusahaan_cabang->nama ?? '-' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Customer</dt>
                            <dd class="col-7">{{ $penjualan->customer->nama ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Telp</dt>
                            <dd class="col-7">{{ $penjualan->customer->no_telp ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Metode Bayar</dt>
                            <dd class="col-7 text-uppercase">{{ $penjualan->kas ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Harga Depresiasi</dt>
                            <dd class="col-7">Rp{{ number_format((int) ($penjualan->detail_penjualan->first()->harga_depresiasi ?? 0), 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-2">
                    <dl class="row mb-0 info-transaksi-list">
                        <dt class="col-5 text-muted">Keterangan</dt>
                        <dd class="col-7 small text-muted">{{ $penjualan->keterangan ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Rincian Total --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #eaf7ef;">
                        <i class="fa-solid fa-cash-register text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Rincian Total</h6>
                        <small class="text-muted">Kalkulasi pembayaran</small>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal Produk</span>
                        <span class="fw-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-danger mb-1">
                        <span>Diskon</span>
                        <span>-Rp{{ number_format($diskon, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-success mb-1">
                        <span>Biaya Tambahan</span>
                        <span>Rp{{ number_format($biayaTambahan, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-top my-2"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">TOTAL BAYAR</span>
                        <span class="fw-bold text-primary fs-5">Rp{{ number_format($totalNominal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ITEM TERJUAL --}}
<div class="card shadow-sm border-0 mt-4" style="border-radius: 14px;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #fff7e6;">
                    <i class="fa-solid fa-cart-shopping text-warning"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Item Terjual</h6>
                    <small class="text-muted">Daftar produk dalam transaksi ini</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 50%; min-width:200px;">Produk</th>
                        <th class="text-center" style="width: 80px;">Qty</th>
                        <th class="text-end" style="width: 120px;">Harga</th>
                        <th class="text-end" style="width: 140px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penjualan->detail_penjualan as $detail)
                        @php
                            $price = $detail->harga_jual_satuan ?? 0;
                            $lineTotal = ($detail->qty ?? 0) * $price;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $detail->produk->nama_produk ?? 'Produk tidak tersedia' }}</div>
                                <small class="text-muted">{{ $detail->produk->kode_sku ?? '-' }}</small>
                            </td>
                            <td class="text-center">x{{ $detail->qty ?? 0 }}</td>
                            <td class="text-end">Rp{{ number_format($price, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp{{ number_format($lineTotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <div class="d-flex flex-column align-items-center opacity-75">
                                    <i class="fa-solid fa-cart-shopping fa-2x mb-2"></i>
                                    <span>Belum ada detail penjualan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

    .accordion-button:not(.collapsed) {
        color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(78, 107, 255, 0.25);
    }
    .btn-outline-dark-gold {

        color: #CC9900 !important;
        border-color: #CC9900 !important;
    }


    .btn-outline-dark-gold:hover,
    .btn-outline-dark-gold:focus,
    .btn-outline-dark-gold:active {
        color: #000 !important;
        background-color: #D4A017 !important;
        border-color: #D4A017 !important;
    }
    .info-transaksi-list dt {
        width: 100%;
    }
    .info-transaksi-list dd {
        width: 100%;
        margin-left: 0;
    }
</style>
@endpush
