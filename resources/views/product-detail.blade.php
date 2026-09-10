@extends('layouts.customer')

@php
    $setting = $cat_setting ?? $setting ?? \App\Models\CatalogSettings::first();
    $rawPhone = preg_replace('/\D+/', '', $setting?->nomor_telfon ?? '');
    if ($rawPhone && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
        $rawPhone = '62' . $rawPhone;
    }

    $storeName = $setting?->nama_website ?? 'Toko';
    $pesan = "Halo {$storeName}, saya tertarik dengan produk *{$produk->nama_produk}* (SKU: {$produk->kode_sku}). Apakah produk ini masih tersedia dan bisakah saya mendapatkan informasi lebih lanjut?";
    $linkWA = $rawPhone ? ("https://wa.me/{$rawPhone}?text=" . urlencode($pesan)) : null;

    $mainImage = $produk->gambar->firstWhere('is_main', true);
    $otherImages = $produk->gambar->filter(function ($g) { return !$g->is_main; });

    if (!$mainImage && $produk->gambar->isNotEmpty()) {
        $mainImage = $produk->gambar->first();
        $otherImages = $produk->gambar->skip(1);
    }

    $galleryImages = $mainImage ? collect([$mainImage])->concat($otherImages) : collect();
    $galleryImages = $galleryImages->values();
    $galleryImageUrls = $galleryImages->map(fn($g) => $g->url);
    $mainImageUrl = $galleryImageUrls->first();
@endphp

@section('title', $produk->nama_produk . ' - Detail Produk - ' . ($setting?->nama_website ?? 'Katalog'))

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/mainPage.css') }}">
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/detail-produk.css') }}">
<style>

</style>
@endpush

@section('content')

    <section id="product-detail" style="padding-top: 50px; padding-bottom:50px" >
    <div class="container">
        <div class="row align-items-center gy-2 mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 flex-nowrap">
                    <a href="javascript:history.back()" class="btn btn-light border d-flex align-items-center gap-1 action-btn">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <form method="GET" action="{{ route('product.index') }}" class="flex-grow-1 flex-shrink-1">
                        <div class="input-group action-search w-100">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row py-3">
            <!-- KIRI: Gambar Carousel -->
            <div class="col-lg-6 mb-3">
                <div class="product-image-preview text-center mb-3" id="galleryPreview" style="position:relative;">
                    <img id="mainProductImage"
                         src="{{ $mainImageUrl ?? asset('images/placeholder.jpg') }}"
                         alt="{{ $produk->nama_produk }}"
                         class="main-image fade-image img-fluid"
                         style="max-width: 400px; max-height: 400px; aspect-ratio: 1/1; object-fit: contain; border-radius: 12px; background: #fff;">
                </div>
                <div class="d-flex align-items-center justify-content-center gap-2 mt-2 w-100" style="position:relative;">
                    <button type="button" class="gallery-nav-btn left me-2" aria-label="Sebelumnya" onclick="switchThumb(-1)" id="galleryPrevBtn"><i class="bi bi-chevron-left"></i></button>
                    <div class="thumbnails-container mb-0 flex-grow-1">
                        <div class="d-flex flex-nowrap gap-2 thumbnails-scroll" id="thumbsRow">
                        @foreach ($galleryImages as $index => $gambar)
                            <img src="{{ $gambar->url }}"
                                alt="{{ $produk->nama_produk }}"
                                class="img-fluid thumbnail{{ $index === 0 ? ' active' : '' }}"
                                onclick="switchProductImage({{ $index }})"
                                data-index="{{ $index }}">
                        @endforeach
                        </div>
                    </div>
                    <button type="button" class="gallery-nav-btn right ms-2" aria-label="Selanjutnya" onclick="switchThumb(1)" id="galleryNextBtn"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>

            <!-- KANAN: Detail Produk -->
            <div class="col-lg-6">
                <h2>{{ $produk->nama_produk }}</h2>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <p class="text-danger fw-bold fs-4 mb-2">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</p>
                    {{-- TOMBOL PESAN SEKARANG DI SINI --}}
                    @if ($produk->stok_produk > 0)
                        @if ($linkWA)
                            <a href="{{ $linkWA }}" class="btn btn-lg flex-shrink-0 btn-custom-whatsapp" target="_blank" rel="noopener noreferrer">
                                 Pesan Sekarang
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg flex-shrink-0" disabled>
                                WhatsApp belum disetel
                            </button>
                        @endif
                    @else
                        <button class="btn btn-secondary btn-lg flex-shrink-0" disabled>
                            <i class="bi bi-x-circle me-2"></i> Stok Habis
                        </button>
                    @endif

                </div>
                <p class="text-muted mb-2"><strong>Kode SKU:</strong> {{ $produk->kode_sku }}</p>
                <p class="text-muted mb-2"><strong>Kategori:</strong> {{ $produk->kategori->nama_kategori }}</p>
                <p class="text-muted mb-2"><strong>Status:</strong> {{ $produk->status }}</p>
                <p class="text-muted mb-2">
                    <strong>Stok:</strong>
                    @if ($produk->stok_produk > 0)
                        <span class="text-success">{{ $produk->stok_produk }} tersedia</span>
                    @else
                        <span class="text-danger">Stok habis</span>
                    @endif
                </p>
                @if (!empty($produk->instagram_link))
                <div class="mb-2">
                    <a href="{{ $produk->instagram_link }}"
                       class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-2"
                       target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-instagram"></i>
                        Lihat video kondisi fisik
                    </a>
                </div>
                @endif
                <p class="text-muted mb-2"><strong>Deskripsi Produk:</strong></p>
                <p id="descriptionText" class="description collapsed">
                    {!! nl2br(e($produk->deskripsi_produk)) !!}
                </p>
                <span id="toggleText" class="toggle-text" style="display: none;">Baca selengkapnya</span>

                {{-- Tambahkan detail lainnya di sini --}}
            </div>
        </div>
    </div>
</section>


@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/loadingScreen.js') }}"></script>
    <script type="module" src="{{ asset('js/productHover.js') }}"></script>
    <script type="module" src="{{ asset('js/scrollNavigation.js') }}"></script>
    <script>
        window.galleryImages = @json($galleryImageUrls->toArray());
    </script>
    <script src="{{ asset('js/productDetailGallery.js') }}"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {

    let currentGalleryIndex = 0;
    const galleryImages = @json($galleryImageUrls->toArray());
    const mainImg = document.getElementById('mainProductImage');
    const thumbs = document.querySelectorAll('.thumbnail');
    const prevBtn = document.getElementById('galleryPrevBtn');
    const nextBtn = document.getElementById('galleryNextBtn');

    function setMainImage(idx) {
        if(idx === currentGalleryIndex) return;
        mainImg.classList.add('fade-out');
        setTimeout(() => {
            mainImg.src = galleryImages[idx];
            mainImg.classList.remove('fade-out');
        }, 300);
        thumbs.forEach((t,i) => t.classList.toggle('active', i === idx));
        currentGalleryIndex = idx;
        updateNavBtn();
    }

    window.switchProductImage = setMainImage;

    window.switchThumb = function(dir) {
        let next = currentGalleryIndex + dir;
        if(next < 0) next = galleryImages.length - 1;
        if(next >= galleryImages.length) next = 0;
        setMainImage(next);
        const activeThumb = document.querySelector(`.thumbnail[data-index="${next}"]`);
        activeThumb?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    function updateNavBtn() {
        if (galleryImages.length <= 1) {
            prevBtn.setAttribute('disabled', 'disabled');
            nextBtn.setAttribute('disabled', 'disabled');
        } else {
            prevBtn.removeAttribute('disabled');
            nextBtn.removeAttribute('disabled');
        }
    }
    updateNavBtn();

    const desc = document.getElementById("descriptionText");
    const toggle = document.getElementById("toggleText");

    if (desc && toggle) {
        const collapsedHeight = 120; // px
        const needsToggle = desc.scrollHeight > collapsedHeight + 5;

        if (needsToggle) {
            toggle.style.display = "inline";
            toggle.style.color = "#007bff";
            toggle.style.cursor = "pointer";
            toggle.style.fontWeight = "500";
            toggle.style.marginTop = "10px";

            desc.style.maxHeight = collapsedHeight + "px";
            desc.style.overflow = "hidden";
            desc.style.transition = "max-height 0.3s ease";
            desc.style.maskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";
            desc.style.webkitMaskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";

            toggle.addEventListener("click", function() {
                const isCollapsed = desc.style.maxHeight === collapsedHeight + "px";
                if (isCollapsed) {
                    desc.style.maxHeight = desc.scrollHeight + "px";
                    desc.style.maskImage = "none";
                    desc.style.webkitMaskImage = "none";
                    toggle.textContent = "Tampilkan lebih sedikit";
                } else {
                    desc.style.maxHeight = collapsedHeight + "px";
                    desc.style.maskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";
                    desc.style.webkitMaskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";
                    toggle.textContent = "Baca selengkapnya";
                }
            });
        } else {
            toggle.style.display = "none";
            desc.style.maxHeight = "none";
            desc.style.maskImage = "none";
            desc.style.webkitMaskImage = "none";
        }
    }
});
</script>
@endpush
