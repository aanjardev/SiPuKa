@extends('layouts.admin')

@section('title', 'Transaksi Penjualan')

@push('styles')
<style>
    #btnTambahItem .btn-label-mobile { display: none; }
    @media (max-width: 576px) {
        #btnTambahItem .btn-label-full { display: none; }
        #btnTambahItem .btn-label-mobile { display: inline; }
    }
    .product-name-cell { max-width: 520px; width: 50%; }
    .product-name-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal !important;
    }
</style>
@endpush

@push('page-actions')
@php
$backRoute = route('admin.sales.create');
if(isset($penjualan)) {
$backRoute = route('admin.sales.show', $penjualan->id);
}
@endphp

<a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
    <i class="fas fa-arrow-left me-1"></i> Kembali
</a>
@endpush

@section('content')

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <strong>Ada Kesalahan Input!</strong>
    </div>
    <ul class="mb-0 mt-1 small">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@php
$isEdit = isset($penjualan);
$items = $items ?? [];
$daftar_produk = $daftar_produk ?? collect();
$produkUntukJs = $daftar_produk->map(function ($produk) {
$imageUrl = $produk->gambarUtama->url
?? $produk->gambar->first()?->url
?? null;
return [
'id' => $produk->id,
'nama_produk' => $produk->nama_produk,
'kode_sku' => $produk->kode_sku,
'harga_jual' => $produk->harga_jual,
'stok_produk' => is_null($produk->stok_produk) ? null : (int) $produk->stok_produk,
'image_url' => $imageUrl,
];
})->values()->toArray();
@endphp

<form action="{{ $isEdit ? route('admin.sales.update', $penjualan->id) : route('admin.sales.store') }}"
    method="POST" id="formPenjualan" data-is-edit="{{ $isEdit ? '1' : '0' }}">
    @csrf
    <input type="hidden" name="items" id="itemsInput" value='{{ $raw_items ?? '[]' }}'>
    @if($isEdit)
    @method('PUT')
    @endif

    <div class="row">

        {{-- KOLOM KIRI: Informasi & Item --}}
        <div class="col-lg-8">

            {{-- CARD 1: Informasi Transaksi --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-file-invoice me-2 text-primary"></i>Informasi Transaksi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        {{-- Customer --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Customer <span class="text-danger">*</span></label>

                            <div class="input-group position-relative">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                                <input
                                    type="text"
                                    class="form-control border-start-0 ps-2 @error('customer_id') is-invalid @enderror"
                                    id="customer_search"
                                    placeholder="Cari nama atau no. telp customer..."
                                    value="{{ old('customer_search', isset($penjualan) && $penjualan->customer ? $penjualan->customer->nama . ' (' . $penjualan->customer->no_telp . ')' : '') }}"
                                    data-search-url="{{ route('admin.customers.search') }}"
                                    autocomplete="off"
                                    autofocus>
                                <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id', $penjualan->customer_id ?? '') }}" required>
                                <div id="customer_suggestions" class="dropdown-menu" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; max-height: 300px; overflow-y: auto;"></div>

                            </div>
                            <div class="invalid-feedback d-block" id="customer_search_error" style="display: none !important;">
                                @error('customer_id') {{ $message }} @else Customer wajib dipilih @enderror
                            </div>
                            <div class="d-flex justify-content-end mt-1">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer" class="small text-decoration-none fw-bold text-primary">
                                    <i class="fa-solid fa-plus-circle me-1"></i>Customer Baru
                                </a>
                                <button type="button"
                                    class="small text-decoration-none fw-bold text-warning border-0 bg-transparent ms-3 d-none"
                                    id="btnEditCustomer"
                                    data-fetch-url-template="{{ url('/admin/customers') }}/__ID__/json"
                                    data-update-url-template="{{ url('/admin/customers') }}/__ID__">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Edit Customer
                                </button>
                            </div>
                        </div>

                        {{-- Cabang --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Lokasi Transaksi (Cabang)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-store"></i></span>
                                <select class="form-select border-start-0 ps-2 @error('perusahaan_cabang_id') is-invalid @enderror" id="perusahaan_cabang_id" name="perusahaan_cabang_id" required style="height: 45px;">
                                    @foreach ($semua_cabang as $branch)
                                    <option value="{{ $branch->id }}" {{ (string) old('perusahaan_cabang_id', $penjualan->perusahaan_cabang_id ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nama }}{{ !$branch->is_active ? ' (Non-Aktif)' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback">
                                @error('perusahaan_cabang_id')
                                    {{ $message }}
                                @else
                                    Cabang wajib dipilih
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Daftar Item --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-cart-shopping me-2 text-warning"></i>Item Penjualan
                    </h6>
                    <a href="{{ route('admin.sales.create') }}"
                        class="btn btn-primary btn-sm fw-medium d-flex align-items-center gap-2"
                        id="btnTambahItem">
                        <i class="fa-solid fa-plus fa-fw"></i>
                        <span class="btn-label-full">Tambah Item</span>
                        <span class="btn-label-mobile">Item</span>
                    </a>

                </div>

                <div class="card-body p-4">
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 50%; min-width:250px">Nama Produk</th>
                                    <th class="text-center py-3" style="width: 120px;">Qty</th>
                                    <th class="text-end py-3" style="width: 140px;">Harga Satuan</th>
                                    <th class="text-end pe-3 py-3" style="width: 160px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tableItemsBody">
                                @forelse ($items as $item)
                                @php
                                $product = $item['product'] ?? null;
                                $productImage = $product?->gambarUtama?->url
                                ?? $product?->gambar?->first()?->url;
                                @endphp
                                <tr data-product-id="{{ $item['product']->id ?? '' }}">
                                    <td class="ps-3 product-name-cell">
                                        @if($productImage)
                                        <img src="{{ $productImage }}" loading="lazy" alt="Img"
                                            class="rounded-3 shadow-sm me-2"
                                            style="width: 45px; height: 45px; object-fit: cover;">
                                        @endif
                                        <div class="fw-semibold text-dark product-name-clamp">{{ $product->nama_produk ?? 'Produk tidak tersedia' }}</div>
                                        <small class="text-muted font-monospace">{{ $product->kode_sku ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if(isset($item['product']->id))
                                        <div class="input-group input-group-sm qty-control justify-content-center" data-product-id="{{ $item['product']->id }}" style="width: 100px; margin: auto;">
                                            <button type="button" class="btn btn-light border btn-qty-dec" data-product-id="{{ $item['product']->id }}"><i class="fa-solid fa-minus"></i></button>
                                            <span class="input-group-text bg-white border-start-0 border-end-0 fw-bold qty-value" style="min-width: 30px; justify-content: center;" data-product-id="{{ $item['product']->id }}">{{ $item['qty'] }}</span>
                                            <button type="button" class="btn btn-light border btn-qty-inc" data-product-id="{{ $item['product']->id }}"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                        @else
                                        x{{ $item['qty'] }}
                                        @endif
                                    </td>
                                    <td class="text-end text-muted small">Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 fw-medium text-dark">Rp{{ number_format($item['line_total'], 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="fa-solid fa-cart-plus fa-2x mb-2 opacity-50"></i>
                                        <p class="small mb-0">Belum ada item yang dipilih.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Pembayaran --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px; border-radius: 10px; z-index: 10;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-cash-register me-2 text-success"></i>Rincian Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">

                    {{-- Metode Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Metode Pembayaran</label>
                        @php $kasValue = old('kas', $penjualan->kas ?? 'cash'); @endphp
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-wallet"></i></span>
                            <select name="kas" class="form-select border-start-0 ps-2" style="height: 45px;">
                                <option value="cash" {{ $kasValue === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="transfer" {{ $kasValue === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Input Biaya Tambahan & Diskon --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium text-secondary small">Diskon</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                                <input type="text" name="diskon" class="form-control border-start-0 ps-1 rupiah-mask" value="{{ old('diskon', $penjualan->diskon ?? 0) }}" min="0" maxlength="11">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium text-secondary small">Biaya Tambahan</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                                <input type="text" name="biaya_tambahan" class="form-control border-start-0 ps-1 rupiah-mask" value="{{ old('biaya_tambahan', $biaya_tambahan_awal ?? 0) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Harga Depresiasi</small></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                            <input type="text" name="depresiasi" class="form-control border-start-0 ps-1 rupiah-mask" value="{{ old('depresiasi', $depresiasi_awal ?? 0) }}" min="0" maxlength="11">
                        </div>
                    </div>

                    {{-- Breakdown Kalkulasi --}}
                    <div class="bg-light p-3 rounded-3 mb-3 small" id="breakdownSection">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal Produk</span>
                            <span class="fw-bold text-dark" id="lineSubtotal">Rp{{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger mb-1" id="lineDiskonRow" style="display: none;">
                            <span>Diskon</span>
                            <span id="lineDiskon">-Rp0</span>
                        </div>
                        {{-- Depresiasi tidak ditampilkan di breakdown kalkulasi, hanya sebagai info untuk nota --}}
                        <div class="d-flex justify-content-between text-success mb-1" id="lineBiayaRow" style="display: none;">
                            <span>Biaya Tambahan</span>
                            <span id="lineBiaya">Rp0</span>
                        </div>
                        <div class="border-top my-2"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">TOTAL BAYAR</span>
                            <span class="fw-bold text-primary fs-5" id="subtotalValue">Rp{{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Catatan Transaksi</label>
                        <textarea name="keterangan" rows="2" class="form-control" placeholder="Opsional...">{{ old('keterangan', $penjualan->keterangan ?? '') }}</textarea>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm" id="btnSubmitPenjualan">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            {{ $isEdit ? 'Update Transaksi' : 'Proses Transaksi' }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal Tambah Item --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 10px;">
            <form id="formTambahItem">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>Tambah Produk
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Pilih Produk</label>
                        <input type="text" class="form-control mb-2" id="produkSearchInput" placeholder="Cari nama atau SKU produk..." autofocus>
                        <select class="form-select" id="produkBaru" style="height: 45px;">
                            <option value="" selected disabled>-- Cari Produk --</option>
                            @foreach(($daftar_produk ?? collect()) as $produk)
                            <option value="{{ $produk->id }}"
                                data-price="{{ $produk->harga_jual }}"
                                data-stock="{{ is_null($produk->stok_produk) ? '' : $produk->stok_produk }}">
                                {{ $produk->nama_produk }} ({{ $produk->kode_sku }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="qtyProdukWrapper">
                        <label class="form-label fw-medium text-secondary small">Jumlah</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-light border" data-action="dec"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" class="form-control text-center" id="qtyProdukBaru" value="1" min="1">
                            <button type="button" class="btn btn-light border" data-action="inc"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="alert alert-warning d-none small border-0 bg-warning bg-opacity-10 text-warning" id="infoStokProduk"></div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light border px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium">Tambah ke Keranjang</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Customer (reuse dari inputPembelian) --}}
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

{{-- Modal Edit Customer --}}
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

@push('scripts')
<script type="application/json" id="produk-data-json">
    @json($produkUntukJs)
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formPenjualan');
    const submitBtn = document.getElementById('btnSubmitPenjualan');
    if (!form) return;

    form.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter') return;

        const tag = (e.target.tagName || '').toLowerCase();
        const type = (e.target.getAttribute && e.target.getAttribute('type'))?.toLowerCase();

        // Biarkan textarea dan tombol/submit bawaan bekerja normal
        if (tag === 'textarea' || type === 'submit' || type === 'button') {
            return;
        }

        e.preventDefault();
        submitBtn?.click();
    });
});
</script>
@vite('resources/js/penjualan/penjualan.js')
@endpush
@endsection
