@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Data Kategori' : 'Tambah Data Kategori')

@push('page-actions')
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
    <button type="submit" form="categoryForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-save"></i>
        <span>{{ isset($category) ? 'Simpan Perubahan' : 'Simpan Kategori' }}</span>
    </button>
@endpush

@section('content')

{{-- Hapus 'justify-content-center' agar tidak di tengah --}}
<div class="row">
    {{-- Ubah menjadi 'col-12' agar Card memenuhi lebar layar yang tersedia --}}
    <div class="col-12">

        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            {{-- Card Header --}}
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-layer-group me-2 text-primary"></i>
                    {{ isset($category) ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h5>
                <p class="text-muted small mt-1">
                    {{ isset($category) ? 'Perbarui nama kategori produk di sini.' : 'Buat kategori baru untuk mengelompokkan produk.' }}
                </p>
            </div>

            <div class="card-body p-4">
                <form id="categoryForm" action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST" data-validate-form enctype="multipart/form-data">
                    @csrf
                    @if(isset($category))
                        @method('PUT')
                    @endif

                    <div class="row">
                        {{-- Kolom Input --}}
                        <div class="col-md-7 col-lg-6">
                            <div class="mb-4">
                                <label for="nama_kategori" class="form-label fw-medium text-secondary small">Nama Kategori <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                        <i class="fa-solid fa-tag"></i>
                                    </span>
                                    <input type="text"
                                        name="nama_kategori"
                                        id="nama_kategori"
                                        class="form-control border-start-0 ps-2 required-field @error('nama_kategori') is-invalid @enderror"
                                        style="height: 45px;"
                                        placeholder="Contoh: Kamera DSLR, Lensa, Tripod..."
                                        value="{{ old('nama_kategori', isset($category) ? $category->nama_kategori : '') }}"
                                        required

                                        data-error-message="Nama kategori wajib diisi"

                                        autofocus>
                                </div>
                                <div class="invalid-feedback">
                                    @error('nama_kategori')
                                        {{ $message }}
                                    @else
                                        Nama kategori wajib diisi
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="gambar" class="form-label fw-medium text-secondary small">Gambar Kategori</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                        <i class="fa-solid fa-image"></i>
                                    </span>
                                    <input type="file"
                                           name="gambar"
                                           id="gambar"
                                           accept="image/*"
                                           class="form-control border-start-0 ps-2 @error('gambar') is-invalid @enderror">
                                </div>
                                @if(isset($category) && $category->image_url)
                                    <div class="mt-3">
                                        <span class="text-muted small d-block mb-1">Pratinjau saat ini:</span>
                                        <img src="{{ $category->image_url }}" alt="Gambar {{ $category->nama_kategori }}" class="rounded border" style="height: 90px; width: auto;">
                                    </div>
                                @endif
                                <div class="invalid-feedback d-block">
                                    @error('gambar')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="mt-3">
                        <div class="alert alert-light border-0 text-muted small">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                            <strong>Tips:</strong><br>
                            Pastikan nama kategori unik dan mudah dipahami. Nama kategori ini akan muncul di filter pencarian produk.
                        </div>
                    </div> --}}

                    <hr class="my-4 opacity-25">

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
