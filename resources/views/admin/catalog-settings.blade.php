@extends('layouts.admin')
@section('title', 'Pengaturan Web Katalog')
@push('page-actions')
<a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm" title="Lihat Website" aria-label="Lihat Website"><i class="fas fa-external-link-alt" aria-hidden="true"></i><span>Lihat Website</span></a>
<button type="submit" form="catalogSettingsForm" class="btn btn-primary btn-sm" title="Simpan Pengaturan" aria-label="Simpan Pengaturan"><i class="fas fa-save" aria-hidden="true"></i><span>Simpan Pengaturan</span></button>
@endpush
@section('content')
@if($errors->any())
<div class="alert alert-danger"><strong>Pengaturan belum tersimpan.</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<form id="catalogSettingsForm" action="{{ route('admin.catalog-settings.update') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">
<div class="col-lg-6">
    <section class="card h-100"><div class="card-body">
        <h2 class="h5">Identitas Website</h2>
        <label for="site_name" class="form-label">Nama Website</label>
        <input id="site_name" name="site_name" class="form-control mb-3" value="{{ old('site_name', $cat_setting->nama_website) }}" maxlength="255" required>
        @if($cat_setting->logo_url)<img src="{{ $cat_setting->logo_url }}" alt="Logo saat ini" class="img-fluid mb-2" style="max-height:100px">@endif
        <label for="photo_logo" class="form-label d-block">Logo</label>
        <input id="photo_logo" name="photo_logo" type="file" class="form-control" accept="image/jpeg,image/png,image/webp">
        <p class="form-text">JPG, PNG, atau WebP. Maksimal 2 MB. Kosongkan untuk mempertahankan logo.</p>
    </div></section>
</div>
<div class="col-lg-6">
    <section class="card h-100"><div class="card-body">
        <h2 class="h5">Kontak dan Media Sosial</h2>
        <p class="text-muted small">Alamat, peta, dan jam buka dikelola di <a href="{{ route('admin.branches.index') }}">Data Toko</a>. Nomor berikut dipakai untuk konsultasi di katalog.</p>
        <label for="contact_phone" class="form-label">WhatsApp Katalog</label>
        <input id="contact_phone" name="contact_phone" type="tel" class="form-control mb-3" value="{{ old('contact_phone', $cat_setting->nomor_telepon) }}" placeholder="08xxxxxxxxxx" maxlength="20">
        @foreach(['instagram'=>'Instagram','facebook'=>'Facebook','tiktok'=>'TikTok','youtube'=>'YouTube'] as $social => $label)
            <label for="social_{{ $social }}" class="form-label">{{ $label }}</label>
            <input id="social_{{ $social }}" name="social_{{ $social }}" type="url" class="form-control mb-3" value="{{ old('social_'.$social, $cat_setting->{$social.'_link'}) }}" placeholder="https://...">
        @endforeach
    </div></section>
</div>
<div class="col-12">
    <section class="card"><div class="card-body">
        <h2 class="h5">Beranda</h2>
        <p class="text-muted small">Teks pada bagian utama beranda. Kosongkan untuk memakai teks bawaan.</p>
        <label for="hero_image" class="form-label d-block">Gambar Hero</label>
        <img src="{{ $cat_setting->hero_image_url ?? asset('mainIMG/produk.png') }}" alt="Gambar hero saat ini" class="img-fluid d-block mb-2" style="max-height:240px">
        <input id="hero_image" name="hero_image" type="file" class="form-control" accept="image/jpeg,image/png,image/webp">
        <p class="form-text">Gambar pada bagian utama beranda. JPG, PNG, atau WebP, maksimal 4 MB. Kosongkan untuk mempertahankan gambar saat ini.</p>
        <div class="row g-3">
        @foreach(['hero_eyebrow'=>['Label Hero',100],'hero_title'=>['Judul Hero',120],'hero_description'=>['Deskripsi Hero',500]] as $field => $config)
            <div class="{{ $field === 'hero_description' ? 'col-12' : 'col-md-6' }}">
                <label for="{{ $field }}" class="form-label">{{ $config[0] }}</label>
                <input id="{{ $field }}" name="{{ $field }}" class="form-control" maxlength="{{ $config[1] }}" value="{{ old($field, $cat_setting->{$field}) }}">
            </div>
        @endforeach
        </div>
    </div></section>
</div>
<div class="col-12">
    <section class="card"><div class="card-body">
        <h2 class="h5">Tentang Kami dan Layanan</h2>
        <label for="about_description" class="form-label">Deskripsi Toko di Tentang Kami</label>
        <textarea id="about_description" name="about_description" rows="4" maxlength="2000" class="form-control mb-3">{{ old('about_description', $cat_setting->about_description ?? $cat_setting->description) }}</textarea>
        <div class="row g-3">
        @foreach(['buy'=>'Pembelian Kamera','sell'=>'Penjualan Kamera','repair'=>'Servis Kamera'] as $service => $label)
            <div class="col-md-4">
                <label for="service_{{ $service }}_title" class="form-label">Judul {{ $label }}</label>
                <input id="service_{{ $service }}_title" name="service_{{ $service }}_title" maxlength="100" class="form-control mb-2" value="{{ old('service_'.$service.'_title', $cat_setting->{'service_'.$service.'_title'}) }}" placeholder="{{ $label }}">
                <label for="service_{{ $service }}_description" class="form-label">Deskripsi Layanan</label>
                <textarea id="service_{{ $service }}_description" name="service_{{ $service }}_description" rows="4" maxlength="2000" class="form-control">{{ old('service_'.$service.'_description', $cat_setting->{'service_'.$service.'_description'}) }}</textarea>
            </div>
        @endforeach
        </div>
        <p class="form-text">Konten kosong memakai teks bawaan katalog.</p>
    </div></section>
</div>
<div class="col-12">
    <section class="card"><div class="card-body">
        <h2 class="h5">SEO dan Berbagi Tautan</h2>
        <label for="seo_title" class="form-label">Judul SEO</label>
        <input id="seo_title" name="seo_title" maxlength="70" class="form-control mb-3" value="{{ old('seo_title', $cat_setting->seo_title) }}">
        <label for="seo_description" class="form-label">Deskripsi SEO</label>
        <textarea id="seo_description" name="seo_description" maxlength="320" rows="3" class="form-control mb-3">{{ old('seo_description', $cat_setting->seo_description) }}</textarea>
        <label for="og_image" class="form-label">Gambar Berbagi Tautan</label>
        <input id="og_image" name="og_image" type="file" class="form-control" accept="image/jpeg,image/png,image/webp">
        <p class="form-text">Maksimal 4 MB. Kosongkan untuk mempertahankan gambar saat ini.</p>
    </div></section>
</div>
<div class="col-12"><button class="btn btn-primary" type="submit">Simpan Pengaturan</button></div>
</div>
</form>
@endsection
