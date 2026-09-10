@extends('layouts.admin')

@php
$isEdit = isset($product);
@endphp

@section('title', $isEdit ? 'Edit Data Produk' : 'Tambah Data Produk')

@push('page-actions')
@php
$backRoute = route('admin.products.index');
@endphp

<a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
    <i class="fas fa-arrow-left me-1"></i> Kembali
</a>
@if($isEdit && !($product->is_archived ?? false))
<form id="archive-product-form" action="{{ route('admin.products.archive', $product->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="button" id="archiveProductTrigger" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-box-archive"></i>
        <span>Arsipkan</span>
    </button>
</form>
@endif
<button type="submit" form="product-form" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fa-solid fa-save"></i>
    <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk Baru' }}</span>
</button>
@endpush

@section('content')

{{-- Error Alert --}}
{{-- @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0"
             role="alert"
             style="border-left: 5px solid #dc3545 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-danger"></i>
                <strong class="text-danger">Ada Kesalahan Input!</strong>
            </div>
            <ul class="mb-0 mt-1 small text-secondary">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
@endforeach
</ul>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif --}}

<form id="product-form"
    action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}"
    method="POST"
    enctype="multipart/form-data"
    data-validate-form>
    @csrf
    @if ($isEdit)
    @method('PUT')
    @endif

    <div class="row">

        {{-- KOLOM KIRI: Informasi Utama & Media --}}
        <div class="col-lg-8">

            {{-- CARD 1: INFORMASI PRODUK --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>
                        Informasi Produk
                    </h6>
                </div>
                <div class="card-body p-4">

                    {{-- Nama Produk --}}
                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-medium">Nama Produk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fa-solid fa-tag"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control border-start-0 ps-2 py-2 required-field @error('nama_produk') is-invalid @enderror"
                                name="nama_produk"
                                value="{{ old('nama_produk', $isEdit ? $product->nama_produk : '') }}"
                                placeholder="Contoh: Kamera Canon EOS 600D"
                                required
                                maxlength="200"
                                data-error-message="Nama produk wajib diisi"
                                autofocus>
                        </div>
                        @error('nama_produk')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                        <div class="invalid-feedback">Nama produk wajib diisi</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-0">
                        <label class="form-label text-secondary small fw-medium">Deskripsi Lengkap</label>
                        <textarea
                            class="form-control py-2 @error('deskripsi_produk') is-invalid @enderror"
                            name="deskripsi_produk"
                            rows="5" style="min-height:200px; font-size: 13px;"
                            placeholder="Tuliskan spesifikasi, kelengkapan, dan kondisi detail produk...">{{ old('deskripsi_produk', $isEdit ? $product->deskripsi_produk : '') }}</textarea>
                    </div>

                    {{-- Instagram Link --}}
                    <div class="mt-4">
                        <label class="form-label text-secondary small fw-medium">Link Instagram (opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fa-brands fa-instagram"></i>
                            </span>
                            <input
                                type="url"
                                class="form-control border-start-0 ps-2 py-2 @error('instagram_link') is-invalid @enderror"
                                name="instagram_link"
                                value="{{ old('instagram_link', $isEdit ? $product->instagram_link : '') }}"
                                placeholder="https://www.instagram.com/username">
                        </div>
                        @error('instagram_link')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- CARD 2: MEDIA PRODUK --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
                <div class="card-header bg-white border-bottom-0 pt-4 ps-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-images me-2 text-primary"></i>
                        Media Produk
                    </h6>
                </div>

                <div class="card-body p-4">
                    {{-- @if ($errors->any())
                    <div class="alert alert-danger mb-4 border-0 shadow-sm">
                        <h5 class="alert-heading small fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Ada Kesalahan!</h5>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif --}}

                    <div class="product-media-uploader">
                        <input type="file" name="images[]" id="product-hidden-images-input" class="d-none" multiple>
                        <input type="hidden" name="main_image" id="product-hidden-main-image">

                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="fa-solid fa-circle-info text-primary"></i>
                                <span class="small text-muted">Maksimal <strong>10 gambar</strong> (Max 10MB/file). Klik kotak di bawah untuk memilih gambar.</span>
                            </div>
                        </div>

                        <div id="product-upload-grid" class="d-flex flex-wrap gap-3 mb-4">
                            {{-- Upload boxes akan ditambahkan via JavaScript --}}
                        </div>

                        <div id="product-upload-status" class="upload-status" style="display: none;"></div>
                        <div id="removed-images-container" class="d-none"></div>
                    </div>

                    {{-- Gambar Saat Ini (Komentar Asli) --}}
                    {{--
                <hr class="my-5 opacity-25">
                <h6 class="fw-bold text-dark mb-3">Gambar Saat Ini</h6>
                <div id="current-images" class="d-flex flex-wrap gap-3">
                    @forelse ($product->photos as $photo)
                    <div class="card border position-relative" style="width: 160px;" data-image-id="{{ $photo->id }}">
                    <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="Gambar produk">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">

                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info btn-set-main" data-image-id="{{ $photo->id }}" title="Set sebagai Gambar Utama">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button class="btn btn-outline-danger remove-image" data-image-id="{{ $photo->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-100 text-center py-4 bg-light rounded border border-dashed">
                    <i class="fas fa-image fa-2x text-muted mb-2"></i>
                    <p class="text-muted small mb-0">Belum ada gambar yang diunggah.</p>
                </div>
                @endforelse
            </div>
            --}}

        </div>
    </div>

    </div>

    {{-- KOLOM KANAN: Inventaris & Harga --}}
    <div class="col-lg-4">

        {{-- CARD 3: KLASIFIKASI & STOK --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
            <div class="card-header bg-white border-bottom-0 pt-4 ps-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check me-2 text-warning"></i>
                    Klasifikasi & Stok
                </h6>
            </div>
            <div class="card-body p-4">

                {{-- Kategori --}}
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium">Kategori <span class="text-danger">*</span></label>
                    <select
                        class="form-select py-2 text-secondary required-field @error('id_kategori') is-invalid @enderror"
                        name="id_kategori"
                        required
                        data-error-message="Kategori wajib dipilih">
                        <option value="" disabled
                            {{ old('id_kategori', $isEdit ? $product->id_kategori : '') ? '' : 'selected' }}>
                            -- Pilih Kategori --
                        </option>
                        @foreach ($semua_kategori as $kategori)
                        <option
                            value="{{ $kategori->id }}"
                            {{ old('id_kategori', $isEdit ? $product->id_kategori : '') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_kategori')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Kategori wajib dipilih</div>
                    @enderror
                </div>

                {{-- SKU --}}
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium">Kode SKU <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-barcode"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-start-0 ps-2 py-2 required-field @error('kode_sku') is-invalid @enderror"
                            name="kode_sku"
                            value="{{ old('kode_sku', $isEdit ? $product->kode_sku : '') }}"
                            placeholder="SKU-0000"
                            required
                            maxlength="20"
                            data-error-message="Kode SKU wajib diisi">
                    </div>
                    @error('kode_sku')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Kode SKU wajib diisi</div>
                    @enderror
                </div>

                {{-- Stok --}}
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium">Stok</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-cubes"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control border-start-0 ps-2 py-2 numeric-only @error('stok_produk') is-invalid @enderror"
                            name="stok_produk"
                            value="{{ old('stok_produk', $isEdit ? $product->stok_produk : '') }}"
                            placeholder="0"
                            data-maxdigits="4">
                    </div>
                </div>

                {{-- Kondisi & Grade --}}
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label text-secondary small fw-medium">Kondisi</label>
                        <select class="form-select py-2 text-secondary" name="status">
                            <option
                                value="Second"
                                {{ old('status', $isEdit ? $product->status : '') === 'Second' ? 'selected' : '' }}>
                                Second
                            </option>
                            <option
                                value="Baru"
                                {{ old('status', $isEdit ? $product->status : '') === 'Baru' ? 'selected' : '' }}>
                                Baru
                            </option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-secondary small fw-medium">Grade</label>
                        <select class="form-select py-2 text-secondary" name="grade">
                            <option
                                value="Unggulan"
                                {{ old('grade', $isEdit ? $product->grade : '') === 'Unggulan' ? 'selected' : '' }}>
                                Unggulan
                            </option>
                            <option
                                value="Standar"
                                {{ old('grade', $isEdit ? $product->grade : '') === 'Standar' ? 'selected' : '' }}>
                                Standar
                            </option>
                            <option
                                value="Minus"
                                {{ old('grade', $isEdit ? $product->grade : '') === 'Minus' ? 'selected' : '' }}>
                                Minus
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Visibility --}}
                <div class="form-check form-switch mt-4">
                    <input type="hidden" name="is_visible" value="0">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="is_visible"
                        name="is_visible"
                        value="1"
                        {{ old('is_visible', $isEdit ? $product->is_visible : 1) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark" for="is_visible">Tampilkan di katalog</label>
                    {{-- <div class="text-muted small">Matikan jika produk mau disembunyikan tanpa menghapus data.</div> --}}
                </div>

            </div>
        </div>

        {{-- CARD 4: HARGA --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 8px;">
            <div class="card-header bg-white border-bottom-0 pt-4 ps-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-tags me-2 text-success"></i>
                    Harga
                </h6>
            </div>
            <div class="card-body p-4">

                {{-- Harga Beli --}}
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium">Harga Beli (Modal)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                        <input
                            type="text"
                            class="form-control border-start-0 ps-2 py-2 rupiah-mask numeric-only"
                            name="harga_beli"
                            value="{{ old('harga_beli', $isEdit ? $product->harga_beli : '') }}"
                            placeholder="0" maxlength="11">
                    </div>
                </div>

                {{-- Harga Servis --}}
                <div class="mb-0">
                    <label class="form-label text-secondary small fw-medium">Biaya Servis (opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                        <input
                            type="text"
                            class="form-control border-start-0 ps-2 py-2 rupiah-mask numeric-only"
                            name="harga_servis"
                            value="{{ old('harga_servis', $isEdit ? $product->harga_servis : '') }}"
                            placeholder="0" maxlength="11">
                    </div>
                </div>

                {{-- Harga Jual --}}
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-medium">Harga Jual</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">Rp</span>
                        <input
                            type="text"
                            class="form-control border-start-0 ps-2 py-2 rupiah-mask numeric-only"
                            name="harga_jual"
                            value="{{ old('harga_jual', $isEdit ? $product->harga_jual : '') }}"
                            placeholder="0" maxlength="11">
                    </div>
                </div>


            </div>
        </div>

    </div>

    </div>

</form>

{{-- Modal Konfirmasi Arsip --}}
@if($isEdit && !($product->is_archived ?? false))
<div class="modal fade" id="archiveConfirmModal" tabindex="-1" aria-labelledby="archiveConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="archiveConfirmLabel">
                    <i class="fa-solid fa-box-archive text-warning me-2"></i>Konfirmasi Arsip
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-box-archive fa-lg"></i>
                    </div>
                    <h6 class="fw-semibold">Arsipkan produk ini?</h6>
                    <p class="text-muted small mb-0">
                        Produk akan disembunyikan dari katalog, daftar produk, dan pemilihan penjualan hingga di-restore.
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>Batal
                </button>
                <button type="button" id="archiveConfirmBtn" class="btn btn-danger">
                    <i class="fa-solid fa-box-archive me-2"></i>Ya, Arsipkan
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/upload-photo.css') }}">
<style>
    @media (max-width: 768px) {
        #product-form .form-control,
        #product-form .form-select,
        #product-form .form-control:focus,
        #product-form .form-select:focus {
            color: #1a1a1a !important;
            background-color: #ffffff !important;
        }
        #product-form .form-control::placeholder {
            color: #6c757d !important;
        }
        #product-form .form-control {
            caret-color: #1a1a1a;
        }
    }
</style>
@endpush

@php
$existingPhotosData = ($isEdit && isset($product) && $product->photos)
    ? $product->photos->map(function ($photo) {
        return [
            'id' => $photo->id,
            'url' => $photo->url,
            'is_main' => (bool) $photo->is_main,
        ];
    })->values()
    : collect();
@endphp

@push('scripts')
<script>
    window.productImageConfigs = window.productImageConfigs || [];
    window.productImageConfigs.push({
        gridId: 'product-upload-grid',
        formId: 'product-form',
        hiddenInputId: 'product-hidden-images-input',
        statusId: 'product-upload-status',
        maxBoxes: 10,
        maxFileSize: {{ 10 * 1024 * 1024 }},
        allowMainChoice: true,
        requireFilesOnSubmit: false,
        requireAtLeastOne: false,
        existingImages: {!! $existingPhotosData->toJson() !!},
        removalInputContainerId: 'removed-images-container',
        removalInputName: 'remove_images[]',
        hiddenMainInputId: 'product-hidden-main-image',
        initialEmptyBoxes: 2
    });
</script>
<script src="{{ asset('js/productImages.js') }}"></script>
@if($isEdit && !($product->is_archived ?? false))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trigger = document.getElementById('archiveProductTrigger');
        const form = document.getElementById('archive-product-form');
        const modalEl = document.getElementById('archiveConfirmModal');
        const confirmBtn = document.getElementById('archiveConfirmBtn');

        if (!trigger || !form || !modalEl || !window.bootstrap) return;

        const modal = new bootstrap.Modal(modalEl);
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            modal.show();
        });

        confirmBtn?.addEventListener('click', function () {
            form.submit();
        });
    });
</script>
@endif
@endpush
