@extends('layouts.customer')

@php
    $setting = $cat_setting ?? \App\Models\CatalogSettings::first();
    $rawPhone = preg_replace('/\D+/', '', $setting?->nomor_telepon ?? '');
    if ($rawPhone && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
        $rawPhone = '62' . $rawPhone;
    }

    $storeName = $setting?->nama_website ?? 'PusatKamera.id';
    $message = "Halo {$storeName}, saya tertarik dengan {$produk->nama_produk} (SKU: {$produk->kode_sku}). Apakah masih tersedia?";
    $whatsAppUrl = $rawPhone ? 'https://wa.me/' . $rawPhone . '?text=' . urlencode($message) : null;

    // Gambar demo sementara untuk presentasi frontend.
    $galleryImageUrls = collect([asset('mainIMG/produk.png')]);
@endphp

@section('title', $produk->nama_produk . ' — PusatKamera.id')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/product-detail-brutal.css') }}?v=7">
@endpush

@section('content')
<main class="detail-brutal">
    <div class="container">
        <section class="detail-grid">
            <div class="detail-gallery">
                <nav class="detail-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('product.index') }}"><i class="bi bi-arrow-left"></i> KEMBALI KE KATALOG</a>
                    <span>/</span>
                    <span>{{ $produk->kategori?->nama_kategori ?? 'Produk' }}</span>
                </nav>
                <div class="detail-main-image" id="galleryPreview">
                    <img id="mainProductImage" src="{{ $galleryImageUrls->first() }}" alt="{{ $produk->nama_produk }}">
                    @if($produk->grade === 'Unggulan')<span class="detail-sticker">PRODUK UNGGULAN</span>@endif
                </div>

                @if($galleryImageUrls->count() > 1)
                    <div class="detail-thumbnails">
                        <button type="button" class="detail-gallery-button" id="galleryPrevBtn" aria-label="Foto sebelumnya"><i class="bi bi-arrow-left"></i></button>
                        <div class="detail-thumbnail-track" id="thumbsRow">
                            @foreach($galleryImageUrls as $index => $imageUrl)
                                <button type="button" class="detail-thumbnail {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" aria-label="Tampilkan foto {{ $index + 1 }}">
                                    <img src="{{ $imageUrl }}" alt="Foto {{ $index + 1 }} {{ $produk->nama_produk }}">
                                </button>
                            @endforeach
                        </div>
                        <button type="button" class="detail-gallery-button" id="galleryNextBtn" aria-label="Foto berikutnya"><i class="bi bi-arrow-right"></i></button>
                    </div>
                @endif
            </div>

            <article class="detail-info">
                <div class="detail-tags">
                    <span>{{ strtoupper($produk->status) }}</span>
                    <span>{{ strtoupper($produk->kategori?->nama_kategori ?? 'KAMERA') }}</span>
                    <div class="detail-availability {{ $produk->stok_produk > 0 ? 'available' : 'empty' }}">
                        <i class="bi {{ $produk->stok_produk > 0 ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                        <div>
                            <strong>{{ $produk->stok_produk > 0 ? 'STOK TERSEDIA' : 'STOK HABIS' }}</strong>
                            {{-- <span>{{ $produk->stok_produk > 0 ? $produk->stok_produk . ' unit siap ditanyakan' : 'Hubungi kami untuk alternatif produk' }}</span> --}}
                        </div>
                    </div>
                </div>
                <h1>{{ $produk->nama_produk }}</h1>
                <p class="detail-price">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</p>


                @if($produk->stok_produk > 0 && $whatsAppUrl)
                    <a href="{{ $whatsAppUrl }}" class="detail-wa-button" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp"></i><span>TANYA PRODUK VIA WHATSAPP<small>Respons cepat dari tim kami</small></span><i class="bi bi-arrow-up-right"></i>
                    </a>
                @elseif(!$whatsAppUrl)
                    <button class="detail-wa-button disabled" type="button" disabled>WHATSAPP BELUM DIATUR</button>
                @endif

                <dl class="detail-meta">
                    <div><dt>KODE PRODUK</dt><dd>{{ $produk->kode_sku }}</dd></div>
                    <div><dt>KONDISI</dt><dd>{{ $produk->status }}</dd></div>
                    <div><dt>GRADE</dt><dd>{{ $produk->grade }}</dd></div>
                    <div><dt>KATEGORI</dt><dd>{{ $produk->kategori?->nama_kategori ?? '—' }}</dd></div>
                </dl>

                @if($produk->instagram_link)
                    <a href="{{ $produk->instagram_link }}" class="detail-video-link" target="_blank" rel="noopener"><i class="bi bi-instagram"></i> LIHAT VIDEO KONDISI FISIK <i class="bi bi-arrow-up-right"></i></a>
                @endif

                <section class="detail-inline-description">
                    <h2>DESKRIPSI PRODUK</h2>
                    <div class="detail-description-box">
                        <p id="descriptionText">{!! nl2br(e($produk->deskripsi_produk ?: 'Informasi detail produk dapat ditanyakan langsung kepada tim kami.')) !!}</p>
                        <button type="button" id="toggleText" hidden>BACA SELENGKAPNYA <i class="bi bi-arrow-down"></i></button>
                    </div>
                </section>
            </article>
        </section>

        {{-- <section class="detail-help">
            <div><span>BARU PERTAMA BELI KAMERA?</span><h2>KAMI BANTU PILIH YANG PAS.</h2></div>
            <p>Ceritakan kebutuhan dan budgetmu. Tim kami akan membantu tanpa istilah teknis yang membingungkan.</p>
            @if($whatsAppUrl)<a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">KONSULTASI GRATIS <i class="bi bi-arrow-right"></i></a>@endif
        </section> --}}
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const images = @json($galleryImageUrls);
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = [...document.querySelectorAll('.detail-thumbnail')];
    const previous = document.getElementById('galleryPrevBtn');
    const next = document.getElementById('galleryNextBtn');
    let activeIndex = 0;

    const showImage = (index) => {
        if (!mainImage || images.length === 0) return;
        activeIndex = (index + images.length) % images.length;
        mainImage.classList.add('switching');
        window.setTimeout(() => {
            mainImage.src = images[activeIndex];
            mainImage.classList.remove('switching');
        }, 120);
        thumbnails.forEach((thumbnail, position) => thumbnail.classList.toggle('active', position === activeIndex));
        thumbnails[activeIndex]?.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
    };

    thumbnails.forEach((thumbnail, index) => thumbnail.addEventListener('click', () => showImage(index)));
    previous?.addEventListener('click', () => showImage(activeIndex - 1));
    next?.addEventListener('click', () => showImage(activeIndex + 1));
    if (images.length < 2) [previous, next].forEach((button) => { if (button) button.disabled = true; });

    const description = document.getElementById('descriptionText');
    const toggle = document.getElementById('toggleText');
    if (description && toggle && description.scrollHeight > 220) {
        description.classList.add('collapsed');
        toggle.hidden = false;
        toggle.addEventListener('click', () => {
            const expanded = description.classList.toggle('expanded');
            toggle.innerHTML = expanded ? 'TAMPILKAN LEBIH SEDIKIT <i class="bi bi-arrow-up"></i>' : 'BACA SELENGKAPNYA <i class="bi bi-arrow-down"></i>';
        });
    }
});
</script>
@endpush
