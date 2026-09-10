@extends('layouts.admin')

@section('title', 'Detail Produk')

@push('page-actions')
<a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-arrow-left"></i>
    <span>Kembali</span>
</a>
@if(!$product->is_archived)
<form action="{{ route('admin.products.archive', $product->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2" onclick="return confirm('Arsipkan produk ini? Produk akan disembunyikan dari katalog.')">
        <i class="fas fa-box-archive"></i>
        <span>Arsipkan</span>
    </button>
</form>
<a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-pen"></i>
    <span>Edit</span>
</a>
@else
<form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-rotate-left"></i>
        <span>Restore</span>
    </button>
</form>
@endif
<!-- <a href="{{ route('admin.products.photos.upload', $product->id) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-images"></i>
    <span>Kelola Foto</span>
</a> -->
@endpush

@section('content')
@php
$formatCurrency = fn ($value) => 'Rp' . number_format($value ?? 0, 0, ',', '.');
@endphp

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-auto">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-image me-2 text-primary"></i>
                    Galeri Produk
                </h6>

                <div class="ratio ratio-1x1 mb-3 rounded-2 bg-light overflow-hidden position-relative">
                    @if ($mainImage)
                    <img src="{{ $mainImage->url }}"
                        alt="{{ $product->nama_produk }}"
                        id="product-main-image"
                        class="w-100 h-100 object-fit-cover">
                    @else
                    <div class="d-flex flex-column justify-content-center align-items-center text-muted">
                        <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada foto</p>
                    </div>
                    @endif
                </div>

                @if ($images->count() > 1)
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($images as $image)
                    <button type="button"
                        class="product-thumb btn btn-light p-0 border {{ $loop->first ? 'active' : '' }}"
                        data-thumb-src="{{ $image->url }}"
                        data-thumb-alt="{{ $product->nama_produk }}"
                        title="Lihat gambar">
                        <img src="{{ $image->url }}"
                            alt="Thumbnail"
                            class="rounded-3"
                            style="width: 64px; height: 64px; object-fit: cover;">
                    </button>
                    @endforeach
                </div>
                @endif

                <div class="mt-4 small text-muted">
                    <div class="d-flex justify-content-between">
                        <span>Total Foto</span>
                        <span class="fw-semibold">{{ $images->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Terakhir Diperbarui</span>
                        <span class="fw-semibold">{{ $product->updated_at?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Produk</p>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold mb-0">{{ $product->nama_produk }}</h3> <br>
                            <span class="badge bg-light text-dark border">{{ $product->kategori->nama_kategori ?? 'Tanpa kategori' }}</span>
                            @if($product->is_archived)
                            <span class="badge bg-light text-secondary border">Diarsipkan</span>
                            @endif
                            @if(!$product->is_visible)
                            <span class="badge bg-light text-secondary border">Hidden</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
                        <p class="text-muted small mb-1">SKU</p>
                        <span class="fw-bold fs-5">{{ $product->kode_sku ?? '-' }}</span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Status</p>
                        <div class="fw-semibold">{{ $product->status ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Grade</p>
                        <div class="fw-semibold">{{ $product->grade ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Stok Saat Ini</p>
                        <div class="fw-semibold">{{ $product->stok_produk ?? 0 }} unit</div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Harga Jual</p>
                        <div class="fw-bold text-success">{{ $formatCurrency($product->harga_jual) }}</div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Harga Beli (Modal)</p>
                        <div class="fw-semibold">{{ $formatCurrency($product->harga_beli) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Biaya Servis</p>
                        <div class="fw-semibold">{{ $formatCurrency($product->harga_servis) }}</div>
                    </div>
                    <div class="col-12">
                        <p class="text-muted small mb-1">Instagram</p>
                        @if (!empty($product->instagram_link))
                        <a href="{{ $product->instagram_link }}" target="_blank" rel="noopener noreferrer" class="fw-semibold text-primary text-decoration-none">
                            {{ $product->instagram_link }}
                        </a>
                        @else
                        <div class="text-muted">-</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-align-left me-2 text-secondary"></i>
                    Deskripsi Produk
                </h6>

                <div class="text-muted" style="white-space: pre-line;">{{ trim($product->deskripsi_produk) ?: 'Belum ada deskripsi yang ditambahkan.' }}</div>


            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .product-thumb {
        width: 68px;
        height: 68px;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .product-thumb.active,
    .product-thumb:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
    }

        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const target = document.getElementById('product-main-image');
        if (!target) return;

        document.querySelectorAll('.product-thumb').forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                const src = this.dataset.thumbSrc;
                const alt = this.dataset.thumbAlt || target.alt;
                if (!src) return;
                target.src = src;
                target.alt = alt;

                document.querySelectorAll('.product-thumb').forEach(function(btn) {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    });
</script>
@endpush
