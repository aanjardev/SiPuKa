@extends('layouts.admin')

@section('title', 'Transaksi Pembelian')

@push('page-actions')
@php
$backRoute = route('admin.purchases.index');
if(isset($pembelian)) {
$backRoute = route('admin.purchases.show', $pembelian->id);
}
@endphp

<a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
    <i class="fas fa-arrow-left me-1"></i> Kembali
</a>
@endpush

@section('content')

{{-- Custom CSS (Dari HEAD) --}}
@push('styles')
<style>

    .card-modern { border: 1px solid #f0f0f0; border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04); transition: all 0.3s ease; }
    .card-header-modern { background-color: #fff; border-bottom: 1px solid #f0f0f0; padding: 20px 24px; border-radius: 16px 16px 0 0 !important; }


    .input-group-modern .input-group-text { background-color: #fff; border-right: none; color: #6c757d; border-color: #dee2e6; border-radius: 10px 0 0 10px; }
    .input-group-modern .form-control, .input-group-modern .form-select { border-left: none; border-color: #dee2e6; border-radius: 0 10px 10px 0; padding: 10px 15px; }
    .input-group-modern .form-control:focus, .input-group-modern .form-select:focus { box-shadow: none; border-color: #86b7fe; border-left: 1px solid #86b7fe; }


    .form-label-modern { font-size: 0.85rem; font-weight: 600; color: #344767; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }


    .btn-action-icon { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; border: 1px solid transparent; background: transparent; color: #dc3545; }
    .btn-action-icon:hover { background-color: #fee2e2; color: #dc2626; border-color: #fecaca; }


    .accordion-modern .accordion-button { background-color: #f8f9fa; border-radius: 8px !important; color: #495057; font-weight: 600; box-shadow: none; }
    .accordion-modern .accordion-button:not(.collapsed) { background-color: #e7f1ff; color: #0d6efd; }
    .accordion-modern .accordion-item { border: 1px solid #eee; border-radius: 8px !important; margin-bottom: 10px; overflow: hidden; }

    .select2-container .select2-selection--single {
        height: 44px !important;
        border-radius: 0 10px 10px 0 !important;
        padding: 7px 15px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }


    @media (max-width: 768px) {
        .purchase-cart-wrap {
            padding: 0;
        }
        .purchase-cart-table {
            width: 100%;
            min-width: 100%;
        }
    }
    .action-three-inline .d-flex {
        flex-wrap: nowrap;
        gap: 0.5rem;
    }
    .action-three-inline button {
        min-width: 0;
        font-size: 0.95rem;
        padding: 0.55rem 0.6rem;
    }
    @media (max-width: 1420px) and (min-width: 992px) {
        .action-three-inline .d-flex {
            flex-direction: column;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .action-three-inline button {
            width: 100%;
            font-size: 0.95rem;
            padding: 0.6rem 1rem;
        }
    }
    @media (max-width: 576px) {
        #btnBukaModalItem .btn-label-full { display: none; }
        #btnBukaModalItem .btn-label-mobile { display: inline; }

        .action-three-inline .d-flex {
            gap: 0.35rem;
        }
        .action-three-inline button {
            font-size: 0.85rem;
            padding: 0.45rem 0.5rem;
        }

        .purchase-cart-table {
            width: 100%;
            min-width: 900px; /* biar kolom tetap nyaman, ikut pola tabel penjualan */
        }
    }
</style>
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- Tema Select2 untuk Bootstrap 5 (Link ini mungkin perlu Anda ubah ke file lokal jika Anda memodifikasi tema) --}}
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

{{-- Form Wrapper (Logic Main + ID HEAD) --}}
<form action="{{ isset($pembelian) ? route('admin.purchases.update', $pembelian->id) : route('admin.purchases.store') }}" method="POST" id="formPembelian">
    @csrf
    @if(isset($pembelian))
        @method('PUT')
    @endif

    <input type="hidden" name="user_id" value="{{ Auth::id() ?? 1 }}">
    <input type="hidden" id="pembelian_id_hidden" name="pembelian_id" value="{{ $pembelian->id ?? '' }}">

    <div class="row g-4">

        {{-- KOLOM KIRI: Form Utama --}}
        <div class="col-lg-8">

            <div class="card card-modern mb-4">
                <div class="card-header-modern d-flex align-items-center gap-3">
                    <i class="fa-solid fa-file-invoice fa-lg text-primary"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Transaksi</h6>
                        <p class="text-muted small mb-0">Data pelanggan dan lokasi transaksi</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label-modern">Customer</label>
                            <div class="input-group input-group-modern mb-2" style="position: relative;">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>

                                {{-- Visible search input + hidden customer_id (single input area) --}}
                                <input type="text" class="form-control" id="customer_search" placeholder="Cari Nama atau No. Telp Customer..." aria-label="Cari Nama atau No. Telp Customer" value="{{ isset($pembelian) && $pembelian->customer ? $pembelian->customer->nama . ' (' . $pembelian->customer->no_telp . ')' : '' }}" autofocus>
                                <input type="hidden" id="customer_id" name="customer_id" value="{{ isset($pembelian) && $pembelian->customer ? $pembelian->customer->id : '' }}">
                                {{-- Container for autocomplete suggestions --}}
                                <div id="customer_suggestions" class="dropdown-menu" style="width:100%;"></div>

                            </div>
                            <div class="invalid-feedback" id="customer_search_error">
                                Customer wajib dipilih sebelum menambah item.
                            </div>
                            <div class="d-flex justify-content-end mt-1">
                                {{-- Tombol Modal Customer Baru --}}
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer"
                                   class="small text-decoration-none fw-bold text-primary d-inline-flex align-items-center gap-1 ms-2">
                                    <i class="fa-solid fa-plus-circle me-1"></i> Customer Baru
                                </a>
                                <button type="button"
                                    class="small text-decoration-none fw-bold text-warning border-0 bg-transparent d-inline-flex align-items-center gap-1 ms-3 d-none"
                                    id="btnEditCustomer">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Customer
                                </button>
                                <span id="customer_info_display" class="small text-muted me-2">
                                    {{-- Ini tidak lagi digunakan dengan Select2, tapi biarkan saja untuk berjaga-jaga --}}
                                </span>

                            </div>
                        </div>

                        {{-- Lokasi Cabang --}}
                        <div class="col-md-6">
                            <label for="perusahaan_cabang_id" class="form-label-modern">Lokasi Transaksi</label>
                            <div class="input-group input-group-modern">
                                <span class="input-group-text"><i class="fa-solid fa-store"></i></span>
                                <select class="form-select @error('perusahaan_cabang_id') is-invalid @enderror" id="perusahaan_cabang_id" name="perusahaan_cabang_id" required>
                                    @foreach($semua_cabang as $cabang)
                                    <option value="{{ $cabang->id }}"
                                        @if(isset($pembelian))
                                            {{ $pembelian->perusahaan_cabang_id == $cabang->id ? 'selected' : '' }}
                                        @else
                                            {{ (Auth::user()->cabang_id_default ?? 1) == $cabang->id ? 'selected' : '' }}
                                        @endif
                                    >
                                        {{ $cabang->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Daftar Item --}}
            <div class="card card-modern mb-4">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fa-solid fa-box-open fa-lg text-primary"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Keranjang Barang</h6>
                            <p class="text-muted small mb-0">Daftar unit yang akan dibeli</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-medium shadow-sm" id="btnBukaModalItem">
                        <i class="fa-solid fa-plus me-1"></i>
                        <span class="btn-label-full">Tambah Item</span>
                        <span class="btn-label-mobile">Item</span>
                    </button>
                </div>

                <div class="card-body p-4 purchase-cart-body">
                    <div class="table-responsive border rounded-3 purchase-cart-wrap">
                        <table class="table table-modern table-hover align-middle mb-0 purchase-cart-table">
                            <thead class="bg-light text-secondary small uppercase">
                                <tr>
                                    <th class="ps-4 py-3 fw-bold border-0" style="width: 60%; min-width:200px;">NAMA ITEM</th>
                                    <th class="py-3 fw-bold border-0" style="max-width: 100px;">KATEGORI</th>
                                    <th class="text-center py-3 fw-bold border-0" style="max-width: 80px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="item-list-wrapper" class="border-top-0">
                                {{-- Render JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Sticky Sidebar (Visual HEAD, Input Logic Main) --}}
        <div class="col-lg-4">
            <div class="card card-modern mb-4 position-sticky" style="top: 20px; z-index: 10;">
                <div class="card-header-modern bg-primary bg-opacity-10 border-primary border-opacity-10">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calculator"></i> Status & Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">

                    {{-- Inputs Harga: Menggunakan Logic Dual Input (Hidden + Display) --}}

                    {{-- Tawaran Customer --}}
                    <div class="mb-3">
                        <label for="display_harga_tawaran_customer" class="form-label-modern">Tawaran Customer</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            {{-- Tambahkan class is-invalid pada input yang terlihat --}}
                            <input type="text" class="form-control fw-bold rupiah-mask @error('harga_tawaran_customer') is-invalid @enderror"
                                id="display_harga_tawaran_customer" placeholder="0"
                                value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}" maxlength="11" data-maxdigits="11">
                            <input type="hidden" name="harga_tawaran_customer" id="harga_tawaran_customer"
                                value="{{ old('harga_tawaran_customer', $pembelian->harga_tawaran_customer ?? '') }}">
                        </div>
                        {{-- Tampilkan pesan error di bawah input group --}}
                        @error('harga_tawaran_customer')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Tawaran Toko --}}
                    <div class="mb-4">
                        <label for="display_harga_tawaran_toko" class="form-label-modern">Tawaran Toko</label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-light fw-medium">Rp</span>
                            {{-- Tambahkan class is-invalid pada input yang terlihat --}}
                            <input type="text" class="form-control fw-bold rupiah-mask @error('harga_tawaran_toko') is-invalid @enderror"
                                id="display_harga_tawaran_toko" placeholder="0"
                                value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}" maxlength="11" data-maxdigits="11">
                            <input type="hidden" name="harga_tawaran_toko" id="harga_tawaran_toko"
                                value="{{ old('harga_tawaran_toko', $pembelian->harga_tawaran_toko ?? '') }}">
                        </div>
                        {{-- Tampilkan pesan error di bawah input group --}}
                        @error('harga_tawaran_toko')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- FINAL DEAL --}}
                    {{-- Pembungkus utama untuk Harga Deal --}}
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 mb-4 border border-primary border-opacity-25">
                        <label for="display_harga_deal" class="form-label-modern mb-1 text-primary">HARGA DEAL</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-primary text-white border-0 fw-bold px-3">Rp</span>
                            {{-- Tambahkan class is-invalid pada input yang terlihat --}}
                            <input type="text" class="form-control border-0 fw-bold text-success fs-5 rupiah-mask @error('harga_deal') is-invalid @enderror"
                                id="display_harga_deal" placeholder="0" style="height: 50px;"
                                value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}" maxlength="11" data-maxdigits="11">
                            <input type="hidden" name="harga_deal" id="harga_deal"
                                value="{{ old('harga_deal', $pembelian->harga_deal ?? '') }}">
                        </div>
                        {{-- Tampilkan pesan error di bawah input group --}}
                        @error('harga_deal')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Action Buttons (Revisi: 3 Kolom) --}}
                    <div class="row g-2 purchase-action-row action-three-inline">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" name="status_pembelian" value="draft" id="btnDraft" class="btn btn-light border py-2 rounded-3 fw-medium flex-fill">
                                <i class="fas fa-save"></i> Draft
                            </button>
                            <button type="submit" name="status_pembelian" value="tidak_deal" id="btnNoDeal" class="btn btn-danger py-2 rounded-3 fw-medium flex-fill">
                                <i class="fas fa-times"></i> No-Deal
                            </button>
                            <button type="submit" name="status_pembelian" value="deal" id="btnDeal" class="btn btn-primary py-2 rounded-3 fw-bold shadow-sm flex-fill">
                                <i class="fas fa-check"></i> DEAL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL TAMBAH ITEM (Visual HEAD Lengkap - FINAL FLOATING ACCORDION VERSION) --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            {{-- Header --}}
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="modalTambahItemTitle">
                    <i class="fa-solid fa-box-open text-primary me-3 fs-4"></i>
                    <span class="modal-title-text">Tambah Item Pembelian</span>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div id="formTambahItem">
                    {{-- =========================================
                        SECTION 1: IDENTITAS BARANG
                        ========================================= --}}
                    <div class="mb-5">
                        <h6 class="fw-bold text-secondary mb-4 pb-2 border-bottom">
                            <span class="me-2 text-primary fw-black">1.</span> Identitas Barang
                        </h6>

                        <div class="row g-3 px-2">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-secondary small">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg fs-6 shadow-none required-field" id="item_nama_item" placeholder="Contoh: Canon EOS 60D Body Only" data-error-message="Nama item wajib diisi" autofocus>
                                <div class="invalid-feedback">Nama item wajib diisi</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary small">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg fs-6 shadow-none required-field" id="item_kategori_id" style="height: calc(2.5rem + 10px);" data-error-message="Kategori wajib dipilih">
                                    <option value="" selected disabled>Pilih...</option>
                                    @foreach($semua_kategori as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Kategori wajib dipilih</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Serial Number (Body)</label>
                                <input type="text" class="form-control font-monospace shadow-none" id="item_serial_number" placeholder="SN Body">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Serial Number (Lensa)</label>
                                <input type="text" class="form-control font-monospace shadow-none" id="item_serial_lens" placeholder="SN Lensa (Jika ada)">
                            </div>
                        </div>
                    </div>


                    {{-- =========================================
                        SECTION 2: DETAIL KONDISI (Struktur Baru)
                        ========================================= --}}
                    <div class="mb-3">
                        <h6 class="fw-bold text-secondary mb-4 pb-2 border-bottom">
                            <span class="me-2 text-primary fw-black">2.</span> Detail Kondisi & Kelengkapan
                        </h6>

                        <div class="accordion" id="qcConditionAccordion">

                            {{-- Accordion 1: Fisik --}}
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        <i class="fa-solid fa-camera me-2 text-secondary"></i> Kondisi Fisik Unit
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Kondisi Fisik Overall</label>
                                                <input type="text" id="item_kondisi_fisik" class="form-control form-control-sm" placeholder="Contoh: Oke/Paintloss">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Kondisi Baut</label>
                                                <input type="text" id="item_kondisi_baut" class="form-control form-control-sm" placeholder="Utuh/Segel/Lecet">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Tutup USB/Mic</label>
                                                <input type="text" id="item_kondisi_tutup_usb" class="form-control form-control-sm" placeholder="Ada/Putus/Hilang">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Karet Grip</label>
                                                <input type="text" id="item_kondisi_grip" class="form-control form-control-sm" placeholder="Rapat/Melar/Putih">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Accordion 2: Lensa & Sensor --}}
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        <i class="fa-solid fa-bullseye me-2 text-secondary"></i> Kondisi Lensa & Sensor
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Jamur Lensa</label>
                                                <input type="text" id="item_kondisi_jamur_lensa" class="form-control form-control-sm" placeholder="Tidak ada / Ada Tipis / Tebal">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Jamur Sensor</label>
                                                <input type="text" id="item_kondisi_jamur_sensor" class="form-control form-control-sm" placeholder="Tidak ada / Ada">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Auto Fokus (AF)</label>
                                                <input type="text" id="item_kondisi_af_lensa" class="form-control form-control-sm" placeholder="Normal / Error / Lambat">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Diafragma (Aperture)</label>
                                                <input type="text" id="item_kondisi_diafragma_lensa" class="form-control form-control-sm" placeholder="Normal / Blade error / Sticking">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Zooming</label>
                                                <input type="text" id="item_kondisi_zoom_lensa" class="form-control form-control-sm" placeholder="Normal / Seret / Macet">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Kalibrasi Fokus</label>
                                                <input type="text" id="item_kondisi_kalibrasi_fokus" class="form-control form-control-sm" placeholder="Normal / Front / Back">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Accordion 3: Fungsi Lain & Kelengkapan --}}
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        <i class="fa-solid fa-list-check me-2 text-secondary"></i> Fungsi Lain & Kelengkapan
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                                    <div class="accordion-body bg-light">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Mounting</label>
                                                <input type="text" id="item_kondisi_mounting" class="form-control form-control-sm" placeholder="Bersih/Kotor">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Slot Memori</label>
                                                <input type="text" id="item_kondisi_slot_memori" class="form-control form-control-sm" placeholder="Normal/Error">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">LCD</label>
                                                <input type="text" id="item_kondisi_lcd" class="form-control form-control-sm" placeholder="Vignette/Dead Pixel">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Tombol</label>
                                                <input type="text" id="item_kondisi_tombol" class="form-control form-control-sm" placeholder="Normal/Keras/Macet">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Flash</label>
                                                <input type="text" id="item_kondisi_flash" class="form-control form-control-sm" placeholder="Normal / Mati / Berfungsi sebagian">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small text-muted">Sound / Mic</label>
                                                <input type="text" id="item_kondisi_sound_mic" class="form-control form-control-sm" placeholder="Normal / Kresek / Mati">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">View Finder</label>
                                            <input type="text" id="item_kondisi_view_finder" class="form-control form-control-sm" placeholder="Bersih / Kotor / Berjamur">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Kondisi Lain-lain</label>
                                            <input type="text" id="item_kondisi_lain_lain" class="form-control form-control-sm">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small text-muted fw-bold">Kelengkapan</label>
                                            <input type="text" id="item_kelengkapan" class="form-control form-control-sm" placeholder="Box, Charger, Baterai, Strap...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                <button type="button" class="btn btn-light bg-white border rounded-3 px-4 fw-semibold text-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm" id="btnSimpanItem">
                    <i class="fa-solid fa-save me-2"></i> Simpan Item
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH CUSTOMER (Revisi Form Sesuai Migrasi) --}}
<div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-plus text-primary me-3 fs-4"></i>
                    Tambah Customer Baru
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahCustomer" data-validate-form>
                <div class="modal-body p-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Nama Customer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required-field" maxlength="50" id="customer_nama_modal" name="nama" required data-error-message="Nama customer wajib diisi" autofocus>
                            <div class="invalid-feedback">
                                Nama customer wajib diisi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control required-field"
                                   id="customer_no_telp_modal"
                                   name="no_telp"
                                   required
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   data-phone-validation
                                   data-max-digits="20">
                            <div class="invalid-feedback">
                                Nomor telepon wajib diisi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select required-field" id="customer_jenis_kelamin_modal" name="jenis_kelamin" required style="height:calc(2.5rem + 10px);" data-error-message="Jenis kelamin wajib dipilih">
                                <option value="" selected disabled>Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">
                                Jenis kelamin wajib dipilih.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Alamat</label>
                            <input type="text" class="form-control" name="alamat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">No. Identitas (KTP/SIM)</label>
                            <input type="text"
                                   class="form-control"
                                   name="identitas"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   data-numeric-only
                                   data-max-digits="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Referensi</label>
                            <input type="text" class="form-control" name="referensi">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light bg-white border rounded-3 px-4 fw-semibold text-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold shadow-sm" id="btnSimpanCustomer">
                        <i class="fa-solid fa-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT CUSTOMER --}}
<div class="modal fade" id="modalEditCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="fa-solid fa-user-pen text-warning me-3 fs-4"></i>
                    Edit Data Customer
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditCustomer" data-validate-form>
                <div class="modal-body p-4">
                    @csrf
                    <input type="hidden" name="customer_id" value="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Nama Customer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control required-field" maxlength="50" id="edit_customer_nama_modal" name="nama" required data-error-message="Nama customer wajib diisi">
                            <div class="invalid-feedback">
                                Nama customer wajib diisi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control required-field"
                                   id="edit_customer_no_telp_modal"
                                   name="no_telp"
                                   required
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   data-phone-validation
                                   data-max-digits="20">
                            <div class="invalid-feedback">
                                Nomor telepon wajib diisi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select required-field" id="edit_customer_jenis_kelamin_modal" name="jenis_kelamin" required style="height:calc(2.5rem + 10px);" data-error-message="Jenis kelamin wajib dipilih">
                                <option value="" selected disabled>Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">
                                Jenis kelamin wajib dipilih.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Alamat</label>
                            <input type="text" class="form-control" name="alamat">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">No. Identitas (KTP/SIM)</label>
                            <input type="text"
                                   class="form-control"
                                   name="identitas"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   data-numeric-only
                                   data-max-digits="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small">Referensi</label>
                            <input type="text" class="form-control" name="referensi">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light bg-white border rounded-3 px-4 fw-semibold text-secondary shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-3 px-4 fw-semibold shadow-sm text-white" id="btnUpdateCustomer">
                        <i class="fa-solid fa-save me-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.accordion-button:not(.collapsed) {
    color: var(--bs-dark);
    background-color: #fff;
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
}
.accordion-button::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230d6efd'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

    font-size: 0.9rem;
    color: #6c757d;
}

    border-color: #dc3545;
}

    box-shadow: 0 0 0 0.15rem rgba(220, 53, 69, 0.25);
}

    display: none;
    font-size: 0.85rem;
    color: #dc3545;
    margin-top: 0.25rem;
}



    display: block;
}
</style>
@endpush

@endsection

@push('scripts')
<script type="application/json" id="pembelian-data">
{
    "currentPembelianId": "{{ $pembelian->id ?? '' }}",
    "initialItems": @json($pembelian->item_pembelian_draft ?? []),
    "kategoriMap": @json($semua_kategori->pluck('nama_kategori','id')),
    "routes": {
        "storeItemDraft": "{{ route('admin.purchases.ajaxStoreItemDraft') }}",
        "updateItemDraftPrefix": "{{ url('/admin/purchases/update-item-draft') }}",
        "deleteItemDraftPrefix": "{{ url('/admin/purchases/delete-item-draft') }}",
        "customerSearch": "{{ route('admin.customers.search') }}",
        "customerStore": "{{ route('admin.customers.store') }}",
        "customerShow": "{{ url('/admin/customers') }}/__ID__/json",
        "customerUpdate": "{{ url('/admin/customers') }}/__ID__"
    }
}
</script>

@vite('resources/js/pembelian/pembelian.js')
@endpush
