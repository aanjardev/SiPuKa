@extends('layouts.admin')

@section('title', 'Detail Pembelian')

@push('page-actions')
    {{-- Tombol Kembali ke Daftar --}}
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left me-1"></i>
        <span>Kembali</span>
    </a>

    {{-- Tombol Edit Transaksi Ini (Diubah ke Dark Gold Outline) --}}
    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}" class="btn btn-outline-dark-gold btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-pen-to-square fa-fw"></i>
        <span>Edit Transaksi</span>
    </a>

    {{-- Tombol Cetak PDF (Tetap Biru Outline) --}}
    @if ($pembelian->status_pembelian == 'deal')
        <a href="{{ route('admin.purchases.print', $pembelian->id) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" target="_blank">
            <i class="fas fa-print fa-fw"></i>
            <span>Cetak Nota</span>
        </a>
    @endif
    {{-- Tombol Salin Link --}}
    <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" onclick="window.copyToClipboard()">
        <i class="fas fa-link fa-fw"></i>
        <span>Salin Link</span>
    </button>
@endpush

@section('content')

{{--
    Ini adalah halaman read-only untuk di-share.
    Mirip dengan form, tapi semua data tidak bisa diedit.
--}}

{{-- Toast message untuk "Salin Link" --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    <div id="copyLinkToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="copyLinkToastBody">Link telah disalin.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="row g-4 align-items-stretch">
    {{-- KOLOM KIRI: Informasi Transaksi --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 14px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #e0edff;">
                        <i class="fa-solid fa-file-invoice text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Informasi Transaksi</h6>
                        <small class="text-muted">Ringkasan pembelian</small>
                    </div>
                </div>

                <div class="row small">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">No. Transaksi</dt>
                            <dd class="col-7 fw-semibold">{{ $pembelian->kode_transaksi ?? ('#' . $pembelian->id) }}</dd>

                            <dt class="col-5 text-muted">Tanggal</dt>
                            <dd class="col-7">{{ $pembelian->created_at?->format('d M Y, H:i') ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Kasir</dt>
                            <dd class="col-7">{{ $pembelian->user->name ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Cabang</dt>
                            <dd class="col-7">{{ $pembelian->perusahaan_cabang->nama ?? '-' }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Customer</dt>
                            <dd class="col-7">{{ $pembelian->customer->nama ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Telp</dt>
                            <dd class="col-7">{{ $pembelian->customer->no_telp ?? '-' }}</dd>

                            <dt class="col-5 text-muted">Status</dt>
                            <dd class="col-7">
                                @if($pembelian->status_pembelian == 'deal')
                                    <span class="badge bg-success-subtle text-success-emphasis px-2">DEAL</span>
                                @elseif($pembelian->status_pembelian == 'tidak_deal')
                                    <span class="badge bg-danger-subtle text-danger-emphasis px-2">TIDAK DEAL</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis px-2">DRAFT</span>
                                @endif
                            </dd>

                            <dt class="col-5 text-muted">Keterangan</dt>
                            <dd class="col-7 small text-muted">{{ $pembelian->keterangan ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Rincian Total --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100 position-sticky" style="border-radius: 14px; top: 20px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #eaf7ef;">
                        <i class="fa-solid fa-cash-register text-success"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Rincian Total</h6>
                        <small class="text-muted">Kalkulasi transaksi</small>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tawaran Customer</span>
                        <span class="fw-semibold">Rp {{ number_format($pembelian->harga_tawaran_customer, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tawaran Toko</span>
                        <span class="fw-semibold">Rp {{ number_format($pembelian->harga_tawaran_toko, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-top my-2"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success">HARGA DEAL</span>
                        <span class="fw-bold text-success fs-5">Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ITEM TERJUAL / ITEM DRAFT --}}
<div class="card shadow-sm border-0 mt-4" style="border-radius: 14px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #fff7e6;">
                    <i class="fa-solid fa-cart-shopping text-warning"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Item Pembelian</h6>
                    <small class="text-muted">Daftar produk dalam transaksi ini ({{ $pembelian->item_pembelian_draft->count() }} item)</small>
                </div>
            </div>
        </div>

        {{-- Accordion item --}}
        <div class="accordion" id="accordionItemReview">
            @forelse ($pembelian->item_pembelian_draft as $item)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed item-head" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse-{{ $item->id }}">

                            <div class="item-head-main d-flex align-items-center flex-wrap gap-2">
                                <span class="fw-bold item-name">{{ $item->nama_item }}</span>
                                <span class="badge bg-secondary">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                            </div>
                            <div class="item-sn-wrap text-muted small">
                                <div><b>SN:</b> {{ $item->serial_number ?? '-' }}</div>
                                <div class="ms-lg-2"><b>SNL:</b> {{ $item->serial_lens ?? '-' }}</div>
                            </div>
                            {{-- <span class="item-toggle-label ms-auto">Lihat Detail</span> --}}

                        </button>
                    </h2>

                    <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionItemReview">
                        <div class="accordion-body bg-light">
                            <h6 class="fw-bold text-primary">Detail Kondisi</h6>
                            <div class="row">
                                @php
                                    $fields = [
                                        'Fisik' => $item->kondisi_fisik,
                                        'Baut' => $item->kondisi_baut,
                                        'Tutup USB' => $item->kondisi_tutup_usb,
                                        'Karet Grip' => $item->kondisi_grip,
                                        'Jamur Lensa' => $item->kondisi_jamur_lensa,
                                        'View Finder' => $item->kondisi_view_finder,
                                        'Mounting' => $item->kondisi_mounting,
                                        'Slot Memori' => $item->kondisi_slot_memori,
                                        'Jamur Sensor' => $item->kondisi_jamur_sensor,
                                        'LCD' => $item->kondisi_lcd,
                                        'Tombol' => $item->kondisi_tombol,
                                        'Zoom Lensa' => $item->kondisi_zoom_lensa,
                                        'AF Lensa' => $item->kondisi_af_lensa,
                                        'Diafragma Lensa' => $item->kondisi_diafragma_lensa,
                                        'Kalibrasi Fokus' => $item->kondisi_kalibrasi_fokus,
                                        'Flash' => $item->kondisi_flash,
                                        'Sound/Mic' => $item->kondisi_sound_mic,
                                        'Lain-lain' => $item->kondisi_lain_lain,
                                    ];
                                @endphp

                                @foreach ($fields as $label => $value)
                                    <div class="col-md-6 mb-1">
                                        <div class="d-flex small">
                                            <div style="width: 140px; font-weight: 600;">{{ $label }}</div>
                                            <div class="flex-grow-1">: &nbsp;{{ $value ?? '-' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr>

                            <h6 class="fw-bold text-primary">Kelengkapan</h6>
                            <p class="small mb-0">{{ $item->kelengkapan ?? 'Tidak ada kelengkapan khusus.' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada item dalam transaksi draft ini.</p>
            @endforelse
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

    /* Item accordion head responsive */
    .item-head {
        align-items: center !important;
        gap: 12px;
    }
    .item-head-main { flex: 1 1 auto; min-width: 0; }
    .item-name {
        display: inline-block;
        max-width: 360px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        vertical-align: middle;
    }
    .item-sn-wrap {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-left: auto;
        white-space: nowrap;
        flex-wrap: wrap;
    }
    .item-toggle-label {
        display: none;
        font-size: 0.85rem;
        color: #6c757d;
        margin-right: 0.35rem; /* tepat sebelum arrow */
    }

    @media (max-width: 900px) {
        .item-head {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 6px;
        }
        .item-sn-wrap {
            width: 100%;
            flex-direction: row;
            align-items: flex-start;
            gap: 6px 12px;
            margin-left: 0;
        }
        .item-name {
            max-width: 100%;
            white-space: normal;
        }
        .item-toggle-label { display: inline; order: 2; align-self: flex-end; }
    }
</style>
@endpush

@push('scripts')
{{-- ======================================================= --}}
{{-- SCRIPT UNTUK AUTO-COPY LINK & FUNGSI COPY TO CLIPBOARD --}}
{{-- ======================================================= --}}
<script>
    function showCopyToast(message, variant = 'success') {
        const toastEl = document.getElementById('copyLinkToast');
        const toastBodyEl = document.getElementById('copyLinkToastBody');
        if (!toastEl || !toastBodyEl) return;

        toastBodyEl.textContent = message;
        toastEl.classList.remove('text-bg-success', 'text-bg-danger', 'text-bg-secondary');
        toastEl.classList.add(`text-bg-${variant}`);

        if (window.bootstrap?.Toast) {
            window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 1600 }).show();
        } else {
            alert(message);
        }
    }

    window.copyToClipboard = async function() {
        const url = "{{ route('admin.purchases.show', $pembelian->id) }}";
        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(url);
            } else {
                const tempInput = document.createElement('input');
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);
                const ok = document.execCommand('copy');
                tempInput.remove();
                if (!ok) throw new Error('execCommand copy failed');
            }

            showCopyToast('Link telah disalin.');
        } catch (e) {
            console.error(e);
            showCopyToast('Gagal menyalin link.', 'danger');
        }
    }
</script>
@endpush
