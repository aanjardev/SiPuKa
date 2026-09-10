@extends('layouts.admin')

@section('title', 'Setting Web Katalog')

{{-- @push('page-actions')
    <button type="button" onclick="document.getElementById('settingsForm').submit()" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-save fa-fw"></i>
        <span>Simpan Perubahan</span>
    </button>
@endpush --}}

@push('styles')
<style>
    .card.position-relative {
        overflow: visible;
    }
    .card-save-btn {
        display: none;
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 2;
    }
    .card-save-btn.show-save {
        display: inline-flex;
    }

    .card-save-btn-logo {
        display: none;
        position: static;
        margin: 12px auto 0 auto;
    }
    .card-save-btn-logo.show-save {
        display: inline-flex;
    }

    /* Default: show text on larger screens */
    .card-save-btn .save-label {
        display: inline;
    }
    .card-save-btn .save-icon {
        display: none;
    }
    .card-save-btn-logo .save-label {
        display: inline;
    }
    .card-save-btn-logo .save-icon {
        display: none;
    }

    /* Icon-only under 1400px */
    @media (max-width: 1400px) {
        .card-save-btn,
        .card-save-btn-logo {
            padding-left: 10px;
            padding-right: 10px;
        }
        .card-save-btn .save-label,
        .card-save-btn-logo .save-label {
            display: none;
        }
        .card-save-btn .save-icon,
        .card-save-btn-logo .save-icon {
            display: inline;
        }
    }

    /* Hide global save on small screens if present */
    @media (max-width: 768px) {
        .page-actions .btn-simpan-semua,
        .mobile-save-all {
            display: none !important;
        }
    }


    .card.card-uniform {
        border-radius: 12px;
        padding: 6px 6px 0 6px;
    }
    .card.card-uniform .card-header {
        border: none;
        background: #fff;
        padding: 14px 16px 10px 16px;
    }
    .card.card-uniform .card-body {
        padding: 20px;
        padding-top: 5px;
        padding-bottom: 30px;
    }

    .gallery-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }

    .gallery-grid-scroll {
        max-height: 520px;
        overflow-y: auto;
        padding-right: 6px;
    }

    @media (max-width: 575px) {
        .gallery-grid-scroll {
            max-height: 420px;
        }
    }

    .table-scroll-fixed {
        table-layout: fixed;
    }

    .table-scroll-fixed thead,
    .table-scroll-fixed tbody {
        display: block;
        width: 100%;
    }

    .table-scroll-fixed thead tr,
    .table-scroll-fixed tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    .table-scroll-fixed tbody {
        max-height: 420px;
        overflow-y: auto;
    }

    @media (max-width: 575px) {
        .table-scroll-fixed tbody {
            max-height: 320px;
        }
    }

    .upload-box {
        width: 160px;
        height: 160px;
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: default;
        transition: all 0.3s ease;
        background: #fafafa;
        position: relative;
        overflow: hidden;
    }

    .upload-box.has-image {
        border: 2px solid #86b7fe;
        background: #fff;
    }

    .upload-box .preview {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        border-radius: 10px;
        overflow: hidden;
    }

    .upload-box .preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-box .controls {
        position: absolute;
        top: 8px;
        right: 8px;
        display: flex;
        gap: 6px;
    }

    .catalog-description-textarea {
        min-height: 200px !important;
        height: auto;
    }
</style>
@endpush

@section('content')
<form action="{{ route('admin.catalog-settings.update') }}"
      method="POST"
      enctype="multipart/form-data"
      id="settingsForm"
      data-route-partner-destroy="{{ route('admin.catalog-settings.partner.destroy', ':id') }}"
      data-route-banner-destroy="{{ route('admin.catalog-settings.banner.destroy', ':id') }}"
      data-route-gallery-destroy="{{ route('admin.catalog-settings.gallery.destroy', ':id') }}">
    @csrf

    <div class="row">
        {{-- KOLOM KIRI: Logo, Informasi Umum, Kontak, & Sosmed --}}
        <div class="col-lg-7 mb-4">

            <div class="row g-3 mb-4">
                <div class="col-lg-4">
                    {{-- Card: Logo Utama --}}
                    <div class="card card-uniform position-relative shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fa-regular fa-image me-2 text-primary"></i>Logo Website</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="bg-light rounded-3 p-3 mb-3 border border-dashed d-flex align-items-center justify-content-center" style="min-height: 150px;">
                                @php
                                    $path = $cat_setting->logo_path;
                                    $url = Str::startsWith($path, 'photos/') ? asset('storage/' . $path) : asset($path);
                        @endphp
                        <img src="{{ $cat_setting->logo_url }}" class="img-fluid">
                    </div>
                    <input type="file" class="form-control form-control-sm" name="photo_logo" accept="image/png,image/jpeg,image/jpg,image/webp" data-max-bytes="2097152" data-max-label="2MB">
                    <div class="invalid-feedback">Ukuran file terlalu besar. Maksimal 2MB.</div>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                        Format: PNG/JPG. Max: 2MB. Resolusi optimal: 400x400px (rasio 1:1).
                    </div>
                    @error('photo_logo') <div class="invalid-feedback d-block small">{{ $message }}</div> @enderror
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-sm card-save-btn-logo">
                            <span class="save-icon"><i class="fas fa-save"></i></span>
                            <span class="save-label">Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>
                </div>

                <div class="col-lg-8">
                    {{-- Card: Identitas & Kontak --}}
                    <div class="card card-uniform position-relative shadow-sm border-0 h-100">
                        <button type="submit" class="btn btn-primary btn-sm card-save-btn">
                            <span class="save-icon"><i class="fas fa-save"></i></span>
                            <span class="save-label">Simpan Perubahan</span>
                        </button>
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Identitas & Kontak</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="site_name" class="form-label fw-medium small text-muted">Nama Website</label>
                                <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $cat_setting->nama_website) }}" placeholder="Contoh: Dinoyo Kamera">
                                @error('site_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium small text-muted">Nomor Telepon / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-brands fa-whatsapp text-success"></i></span>
                                     <input type="text"
                                         class="form-control border-start-0 ps-2 @error('contact_phone') is-invalid @enderror"
                                         id="contact_phone_display"
                                         data-format="phone"
                                         data-target="#contact_phone"
                                         value="{{ old('contact_phone', $cat_setting->nomor_telfon) }}"
                                         placeholder="08xx-xxxx-xxxx">
                                    <input type="hidden"
                                           name="contact_phone"
                                           id="contact_phone"
                                           value="{{ old('contact_phone', $cat_setting->nomor_telfon) }}">
                                </div>
                                <div class="invalid-feedback">Nomor telepon wajib diawali 0/62 dan hanya berisi angka.</div>
                                @error('contact_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-medium small text-muted">Deskripsi Toko</label>
                                <textarea class="form-control catalog-description-textarea" name="description_text" rows="4" placeholder="Tulis deskripsi singkat tentang toko...">{{ old('description_text', $cat_setting->description) }}</textarea>
                                @error('description_text') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Media Sosial --}}
            <div class="card card-uniform position-relative shadow-sm border-0">
                        <button type="submit" class="btn btn-primary btn-sm card-save-btn">
                            <span class="save-icon"><i class="fas fa-save"></i></span>
                            <span class="save-label">Simpan Perubahan</span>
                        </button>
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-share-nodes me-2 text-primary"></i>Link Media Sosial</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-instagram text-danger"></i></span>
                                <input type="text" class="form-control @error('social_instagram') is-invalid @enderror" name="social_instagram" value="{{ old('social_instagram', $cat_setting->instagram_link) }}" placeholder="https://instagram.com/...">
                                @error('social_instagram') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-facebook text-primary"></i></span>
                                <input type="text" class="form-control @error('social_facebook') is-invalid @enderror" name="social_facebook" value="{{ old('social_facebook', $cat_setting->facebook_link) }}" placeholder="https://facebook.com/...">
                                @error('social_facebook') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-tiktok text-dark"></i></span>
                                <input type="text" class="form-control @error('social_tiktok') is-invalid @enderror" name="social_tiktok" value="{{ old('social_tiktok', $cat_setting->tiktok_link) }}" placeholder="https://tiktok.com/@...">
                                @error('social_tiktok') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">YouTube</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-youtube text-danger"></i></span>
                                <input type="text" class="form-control @error('social_youtube') is-invalid @enderror" name="social_youtube" value="{{ old('social_youtube', $cat_setting->youtube_link) }}" placeholder="https://youtube.com/...">
                                @error('social_youtube') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Tokopedia</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-store text-success"></i></span>
                                <input type="text" class="form-control @error('social_tokopedia') is-invalid @enderror" name="social_tokopedia" value="{{ old('social_tokopedia', $cat_setting->tokopedia_link) }}" placeholder="Link Tokopedia">
                                @error('social_tokopedia') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Shopee</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-bag-shopping" style="color: #ff4000ff"></i></span>
                                <input type="text" class="form-control @error('social_shopee') is-invalid @enderror" name="social_shopee" value="{{ old('social_shopee', $cat_setting->shopee_link) }}" placeholder="Link Shopee">
                                @error('social_shopee') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Galeri Customer --}}
            <div class="card card-uniform position-relative shadow-sm border-0 mt-4">
                <button type="submit" class="btn btn-primary btn-sm card-save-btn">
                    <span class="save-icon"><i class="fas fa-save"></i></span>
                    <span class="save-label">Simpan Perubahan</span>
                </button>
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-images me-2 text-primary"></i>Galeri Customer</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Upload Foto Galeri</label>
                        <input type="file" class="form-control form-control-sm" name="customer_gallery[]" accept="image/png,image/jpeg,image/jpg,image/webp" multiple data-max-bytes="4194304" data-max-label="4MB">
                        <div class="invalid-feedback">Ukuran file terlalu besar. Maksimal 4MB.</div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Format: JPG/PNG. Max: 4MB. Disarankan rasio 1:1.</div>
                        @error('customer_gallery') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        @error('customer_gallery.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="gallery-grid-scroll">
                        <div class="gallery-grid">
                            @foreach ($cat_gallery as $gallery)
                            <div class="upload-box has-image" data-id="{{ $gallery->id }}" data-type="gallery">
                                <div class="preview">
                                    <img src="{{ $gallery->url }}" alt="Galeri customer">
                                </div>
                                <div class="controls">
                                    <button type="button" class="btn-action btn-remove text-danger" title="Hapus Foto">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="deleted_galleries" id="deletedGalleries">
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Aset Visual (Logo, Banner, Partner) --}}
        <div class="col-lg-5 mb-4">

            {{-- Card: Banner Homepage --}}
            <div class="card card-uniform position-relative shadow-sm border-0 mb-4">
                <button type="submit" class="btn btn-primary btn-sm card-save-btn">
                    <span class="save-icon"><i class="fas fa-save"></i></span>
                    <span class="save-label">Simpan Perubahan</span>
                </button>
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-panorama me-2 text-primary"></i>Banner Slider</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Upload Banner Baru</label>
                        <input type="file" class="form-control form-control-sm" name="banner" accept="image/png,image/jpeg,image/jpg,image/webp" data-max-bytes="4194304" data-max-label="4MB">
                        <div class="invalid-feedback">Ukuran file terlalu besar. Maksimal 4MB.</div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Format: JPG/PNG. Max: 4MB. Resolusi optimal: 700x300px (rasio 7:3).</div>
                        @error('banner') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="table-responsive border rounded-3">
                        <table class="table table-modern table-sm mb-0 table-scroll-fixed">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60%">Gambar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cat_banners as $index => $banner)
                                <tr data-id="{{ $banner->id }}" data-type="banner">
                                    <td class="p-2">
                                        <img src="{{ $banner->banner_url }}" class="rounded shadow-sm w-100" style="height:60px;object-fit:cover;">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn-action btn-remove text-danger mx-auto" title="Hapus Banner">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="deleted_banners" id="deletedBanners">
                </div>
            </div>

            {{-- Card: Logo Partner --}}
            <div class="card card-uniform position-relative shadow-sm border-0">
                <button type="submit" class="btn btn-primary btn-sm card-save-btn">
                    <span class="save-icon"><i class="fas fa-save"></i></span>
                    <span class="save-label">Simpan Perubahan</span>
                </button>
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-handshake me-2 text-primary"></i>Logo Brand/Partner</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Upload Logo Baru</label>
                        <input type="file" class="form-control form-control-sm" name="brand_logos" accept="image/png,image/jpeg,image/jpg,image/webp" data-max-bytes="2097152" data-max-label="2MB">
                        <div class="invalid-feedback">Ukuran file terlalu besar. Maksimal 2MB.</div>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Format: PNG/JPG. Max: 2MB. Resolusi optimal: 300x100px (rasio 3:1).</div>
                        @error('brand_logos') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="table-responsive border rounded-3">
                        <table class="table table-modern table-sm mb-0 table-scroll-fixed">
                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th style="width: 60%">Logo</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cat_partner as $index => $partner)
                                <tr data-id="{{ $partner->id }}" data-type="partner">
                                    <td class="p-2 align-middle">
                                        <div class="bg-light rounded p-1 text-center border border-dashed">
                                            <img src="{{ Str::startsWith($partner->url, 'http') ? $partner->url : asset('storage/' . $partner->logo_path) }}"
                                                 class="img-fluid"
                                                 style="max-height: 40px;">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn-action btn-remove text-danger mx-auto" title="Hapus Logo">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="deleted_partners" id="deletedPartners">
                </div>
            </div>

        </div>
    </div>

    {{-- Tombol Simpan Mobile / Bottom --}}
    <div class="d-block d-md-none fixed-bottom bg-white border-top p-3 shadow-lg mobile-save-all">
        <button type="submit" class="btn btn-primary w-100">Simpan Semua Pengaturan</button>
    </div>
</form>
@endsection

@push('scripts')
@vite(['resources/js/utils/phone-input-validation.js', 'resources/js/admin/catalog-settings.js'])
@endpush
