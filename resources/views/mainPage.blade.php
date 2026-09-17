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
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/home-brutal.css') }}?v=7">
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
                    <img class="hero-image" src="{{ asset('mainIMG/produk.png') }}" alt="Kamera di PusatKamera.id">
                    <p>SIAP UNTUK<br>MULAI BERKARYA</p>
                </div>
            </div>
        </div>
    </section>

    <section class="brutal-section brutal-section-yellow">
        <div class="container">
            <div class="section-heading">
                <div><span class="section-number">01</span><h2>KATEGORI</h2></div>
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

    <section class="brutal-section products-section">
        <div class="container">
            <div class="section-heading">
                <div><span class="section-number">02</span><h2>PRODUK TERBARU</h2></div>
                {{-- <p>Pilihan terbaru untuk memulai perjalanan fotografimu.</p> --}}
            </div>
            @if ($latestProducts->isEmpty())
                <div class="brutal-empty">Produk terbaru akan segera hadir.</div>
            @else
                <div class="home-product-grid">
                    @foreach ($latestProducts as $product)
                        <a href="{{ route('product.show', $product->id) }}" class="home-product-card">
                            <div class="home-product-media">
                                @if ($product->grade === 'Unggulan')
                                    <span class="product-badge">PILIHAN</span>
                                @endif
                                <img src="{{ asset('mainIMG/produk.png') }}" alt="{{ $product->nama_produk }}" loading="lazy">
                            </div>
                            <div class="home-product-info">
                                <h3>{{ $product->nama_produk }}</h3>
                                <div><strong>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</strong><span><i class="bi bi-arrow-up-right"></i></span></div>
                            </div>
                        </a>
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
                <article><span>01</span><i class="bi bi-camera-fill"></i><h3>RAMAH UNTUK PEMULA</h3><p>Pilihan kamera yang mudah dipahami untuk mulai belajar fotografi.</p></article>
                <article>
                    <span>02</span>
                    <i class="bi bi-tags-fill"></i>
                    <h3>HARGA SESUAI BUDGET</h3>
                    <p>Pilih kamera dari berbagai rentang harga sesuai anggaranmu.</p>
                </article>
                <article><span>03</span><i class="bi bi-people-fill"></i><h3>PAKET SESUAI KEBUTUHAN</h3><p>Paket untuk fotografer pemula, kegiatan ekstrakurikuler, mahasiswa jurusan DKV, dan anggota komunitas fotografi.</p></article>
            </div>
        </div>
    </section>

    <section class="cta-brutal">
        <div class="container">
            <div class="cta-box">
                <span>MASIH BINGUNG PILIH KAMERA?</span>
                <h2>CERITAKAN KEBUTUHANMU.</h2>
                <a href="/contact" class="brutal-btn brutal-btn-yellow">MULAI KONSULTASI <i class="bi bi-arrow-up-right"></i></a>
            </div>
        </div>
    </section>
</main>
@endsection
