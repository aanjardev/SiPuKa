@extends('layouts.customer')

@php
    $budgetCategories = [
        ['label' => '500 Ribuan', 'eyebrow' => 'BUDGET KAMERA', 'price' => '500RB', 'url' => route('product.index', ['budget' => '500rb'])],
        ['label' => '1 Jutaan', 'eyebrow' => 'BUDGET KAMERA', 'price' => '1JT', 'url' => route('product.index', ['budget' => '1jt'])],
        ['label' => '2 Jutaan', 'eyebrow' => 'BUDGET KAMERA', 'price' => '2JT', 'url' => route('product.index', ['budget' => '2jt'])],
        ['label' => '3 Jutaan', 'eyebrow' => 'BUDGET KAMERA', 'price' => '3JT', 'url' => route('product.index', ['budget' => '3jt'])],
        ['label' => '4–5 Juta', 'eyebrow' => 'BUDGET KAMERA', 'price' => '4—5JT', 'url' => route('product.index', ['budget' => '4-5jt'])],
        ['label' => '6–10 Juta', 'eyebrow' => 'BUDGET KAMERA', 'price' => '6—10JT', 'url' => route('product.index', ['budget' => '6-10jt'])],
        ['label' => 'Di Atas 10 Juta', 'eyebrow' => 'BUDGET KAMERA', 'price' => '>10JT', 'url' => route('product.index', ['budget' => 'diatas-10jt'])],
        ['label' => 'Paket Ekstrakurikuler', 'eyebrow' => 'UNTUK SEKOLAH', 'price' => 'PAKET', 'url' => route('product.index', ['search' => 'paket ekstrakurikuler'])],
    ];
@endphp

@section('title', $cat_setting?->seo_title ?? (($cat_setting?->nama_website ?? 'PusatKamera.id') . ' — Katalog Kamera'))
@section('meta_description', $cat_setting?->seo_description ?? $cat_setting?->description ?? '')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/home-brutal.css') }}?v=15">
@endpush

@section('content')
<main class="home-brutal">
    <section class="hero-brutal">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-copy">
                    <span class="brutal-sticker">{{ $cat_setting?->hero_eyebrow ?? 'JAGONYA KAMERA PEMULA & LEMBAGA' }}</span>
                    <h1>{{ $cat_setting?->hero_title ?? $cat_setting?->nama_website ?? 'PusatKamera.id' }}</h1>
                    <p>{{ $cat_setting?->hero_description ?? 'Pilihan kamera terjangkau untuk pelajar, mahasiswa, ekstrakurikuler fotografi, dan lembaga pendidikan.' }}</p>
                    <div class="hero-actions">
                        <a href="{{ route('product.index') }}" class="brutal-btn brutal-btn-light">LIHAT KATALOG <i class="bi bi-arrow-up-right"></i></a>
                        <a href="/contact" class="brutal-btn brutal-btn-light">KONSULTASI GRATIS</a>
                    </div>
                </div>
                <div class="hero-art" aria-label="Ilustrasi kamera">
                    <span class="hero-burst">CEK<br>STOK!</span>
                    <img fetchpriority="high" decoding="async" class="hero-image" src="{{ $cat_setting?->hero_image_url ?? asset('mainIMG/produk.webp') }}" alt="Kamera di PusatKamera.id">
                    <p>SIAP UNTUK<br>MULAI BERKARYA</p>
                </div>
            </div>
        </div>
    </section>

    <section class="brutal-section brutal-section-yellow">
        <div class="container">
            <div class="section-heading">
                <div><h2>PILIHAN HARGA</h2></div>
                <a href="{{ route('product.index') }}">LIHAT SEMUA <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="category-grid budget-category-grid">
                @foreach ($managedBudgetCategories as $category)
                    <a href="{{ route('product.index', ['budget' => $category->slug]) }}" class="category-card budget-category-card">
                        <div class="category-media">
                            <small>BUDGET KAMERA</small>
                            <strong>{{ strtoupper($category->nama) }}</strong>
                        </div>
                        <div class="category-name"><span>LIHAT PRODUK</span><i class="bi bi-arrow-up-right"></i></div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="brutal-section camera-category-section">
        <div class="container">
            <div class="section-heading">
                <div><h2>JENIS KAMERA</h2></div>
                <a href="{{ route('product.index') }}">LIHAT SEMUA <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="camera-type-grid">
                @forelse ($kategoris as $kategori)
                    <a href="{{ route('product.index', ['kategori' => $kategori->id]) }}" class="camera-type-link">
                        <i class="bi bi-camera" aria-hidden="true"></i>
                        <span>{{ $kategori->nama_kategori }}</span>
                        <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    </a>
                @empty
                    <p class="mb-0">Kategori kamera akan segera tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="brutal-section products-section">
        <div class="container">
            <div class="section-heading">
                <div><h2>PRODUK TERBARU</h2></div>
                {{-- <p>Pilihan terbaru untuk memulai perjalanan fotografimu.</p> --}}
            </div>
            @if ($latestProducts->isEmpty())
                <div class="brutal-empty">Produk terbaru akan segera hadir.</div>
            @else
                <div class="home-product-grid">
                    @foreach ($latestProducts as $product)
                        <article class="home-product-card">
                            <a href="{{ route('product.show', $product->id) }}" class="home-product-link">
                                <div class="home-product-media">
                                    @if ($product->stok_produk <= 0)
                                        <span class="product-badge">STOK HABIS</span>
                                    @elseif ($product->grade === 'A')
                                        <span class="product-badge">PILIHAN</span>
                                    @endif
                                    <img src="{{ $product->gambarUtama?->thumbnail_url ?? asset('mainIMG/produk.webp') }}" alt="{{ $product->nama_produk }}" loading="lazy" decoding="async">
                                </div>
                                <div class="home-product-info">
                                    <h3>{{ $product->nama_produk }}</h3>
                                    <div><strong>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</strong><span><i class="bi bi-arrow-up-right"></i></span></div>
                                </div>
                            </a>
                            @if($product->video_url)
                                <a href="{{ $product->video_url }}" class="product-card-video" target="_blank" rel="noopener noreferrer" aria-label="Lihat video {{ $product->nama_produk }}">
                                    <i class="bi bi-youtube" aria-hidden="true"></i> Lihat video <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
            <div class="products-more">
                <a href="{{ route('product.index', ['sort' => 'terbaru']) }}" class="brutal-btn brutal-btn-yellow">LIHAT SELENGKAPNYA <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <section class="brutal-section trust-section">
        <div class="container">
            <div class="trust-grid">
                <article><i class="bi bi-camera-fill"></i><h3>RAMAH UNTUK PEMULA</h3><p>Pilihan kamera yang mudah dipahami untuk mulai belajar fotografi.</p></article>
                <article>

                    <i class="bi bi-tags-fill"></i>
                    <h3>HARGA SESUAI BUDGET</h3>
                    <p>Pilih kamera dari berbagai rentang harga sesuai anggaranmu.</p>
                </article>
                <article><i class="bi bi-people-fill"></i><h3>PAKET SESUAI KEBUTUHAN</h3><p>Paket untuk fotografer pemula, kegiatan ekstrakurikuler, mahasiswa jurusan DKV, dan anggota komunitas fotografi.</p></article>
            </div>
        </div>
    </section>

    @include('partials.home-partners')

    <section class="cta-brutal">
        <div class="container">
            <div class="cta-box">
                <span>PILIH KAMERA YANG PAS.</span>
                <h2>KAMERA MAHAL BELUM TENTU PAS.</h2>
                <p>Jangan sampai bayar mahal untuk kamera yang kurang sesuai. Kami bantu pilih sesuai kebutuhan dan budget kamu.</p>
                <a href="/contact" class="brutal-btn brutal-btn-yellow">KONSULTASI GRATIS <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>
</main>
<button type="button" class="home-search-float" id="openProductSearch" aria-haspopup="dialog" aria-controls="homeProductSearch">
    <i class="bi bi-search" aria-hidden="true"></i> <span>Cari produk</span>
</button>
<dialog class="home-search-dialog" id="homeProductSearch" aria-labelledby="homeSearchTitle">
    <div class="home-search-heading">
        <h2 id="homeSearchTitle">CARI PRODUK</h2>
        <form method="dialog"><button type="submit" aria-label="Tutup pencarian"><i class="bi bi-x-lg" aria-hidden="true"></i></button></form>
    </div>
    <form method="GET" action="{{ route('product.index') }}" class="home-search-form">
        <label for="homeProductSearchInput">Produk apa yang kamu cari?</label>
        <input type="search" id="homeProductSearchInput" name="search" placeholder="Contoh: Canon, Nikon, kamera pemula..." required autofocus>
        <button type="submit">CARI PRODUK <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
    const productSearchDialog = document.getElementById('homeProductSearch');
    document.getElementById('openProductSearch').addEventListener('click', () => productSearchDialog.showModal());
</script>
@endpush
