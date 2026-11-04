@extends('layouts.customer')

@section('title', 'Katalog Kamera — PusatKamera.id')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/catalog-brutal.css') }}?v=11">
@endpush

@section('content')
<main class="catalog-brutal">
    {{-- <header class="catalog-hero">
        <div class="container">
            <span class="catalog-kicker">PusatKamera.id</span>
            <div class="catalog-title-row">
                <h1>CARI KAMERA<br><span>SESUAI BUDGET.</span></h1>
                <p>Dari kamera pertama sampai paket lembaga, temukan pilihan yang pas tanpa bikin kantong kaget.</p>
            </div>
        </div>
    </header> --}}

    <section class="catalog-controls">
        <div class="container">
            @php
                $selectedCategory = $kategoriFilter ? $kategoris->firstWhere('id', $kategoriFilter) : null;
            @endphp
            <div class="catalog-dropdown-row">
                <div class="catalog-filter-field"><span class="catalog-field-label">BUDGET</span>
                <details class="catalog-dropdown">
                    <summary>
                        <span><strong>{{ $budget ? $budgetRanges[$budget]['label'] : 'Semua Harga' }}</strong></span>
                        <i class="bi bi-chevron-down"></i>
                    </summary>
                    <div class="catalog-dropdown-menu">
                        <p>PILIH RENTANG HARGA</p>
                        <a href="{{ route('product.index', request()->except('page', 'budget')) }}" class="{{ !$budget ? 'active' : '' }}"><span>Semua Harga</span>@if(!$budget)<i class="bi bi-check-lg"></i>@endif</a>
                        @foreach($budgetRanges as $key => $range)
                            <a href="{{ route('product.index', array_merge(request()->except('page', 'budget'), ['budget' => $key])) }}" class="{{ $budget === $key ? 'active' : '' }}">
                                <span>{{ $range['label'] }}</span>@if($budget === $key)<i class="bi bi-check-lg"></i>@endif
                            </a>
                        @endforeach
                    </div>
                </details>
                </div>

                <div class="catalog-filter-field"><span class="catalog-field-label">KATEGORI</span>
                <details class="catalog-dropdown">
                    <summary>
                        <span><strong>{{ $selectedCategory?->nama_kategori ?? 'Semua Kamera' }}</strong></span>
                        <i class="bi bi-chevron-down"></i>
                    </summary>
                    <div class="catalog-dropdown-menu">
                        <p>PILIH KATEGORI KAMERA</p>
                        <a href="{{ route('product.index', request()->except('page', 'kategori')) }}" class="{{ !$kategoriFilter ? 'active' : '' }}"><span>Semua Kamera</span>@if(!$kategoriFilter)<i class="bi bi-check-lg"></i>@endif</a>
                        @foreach($kategoris as $kategori)
                            <a href="{{ route('product.index', array_merge(request()->except('page', 'kategori'), ['kategori' => $kategori->id])) }}" class="{{ (string)$kategoriFilter === (string)$kategori->id ? 'active' : '' }}">
                                <span>{{ $kategori->nama_kategori }}</span>@if((string)$kategoriFilter === (string)$kategori->id)<i class="bi bi-check-lg"></i>@endif
                            </a>
                        @endforeach
                    </div>
                </details>
                </div>
            </div>

            <form method="GET" action="{{ route('product.index') }}" class="catalog-search-form">
                @if($kategoriFilter)<input type="hidden" name="kategori" value="{{ $kategoriFilter }}">@endif
                @if($budget)<input type="hidden" name="budget" value="{{ $budget }}">@endif
                <input type="hidden" name="sort" value="{{ $sort }}">
                <div class="catalog-search">
                    <label for="catalog-search-input">CARI PRODUK</label>
                    <div><i class="bi bi-search"></i><input id="catalog-search-input" type="search" name="search" value="{{ $search }}" placeholder="Contoh: Canon, Nikon, kamera pemula..."><button type="submit">CARI</button></div>
                </div>
                <div class="catalog-sort-custom">
                    <span>URUTKAN</span>
                    <details class="catalog-dropdown sort-dropdown">
                        <summary>
                            <strong>{{ $sortOptions[$sort] ?? $sortOptions['terbaru'] }}</strong>
                            <i class="bi bi-chevron-down"></i>
                        </summary>
                        <div class="catalog-dropdown-menu sort-dropdown-menu">
                            {{-- <p>URUTKAN PRODUK</p> --}}
                            @foreach($sortOptions as $sortKey => $sortLabel)
                                <a href="{{ route('product.index', array_merge(request()->except('page', 'sort'), ['sort' => $sortKey])) }}" class="{{ $sort === $sortKey ? 'active' : '' }}">
                                    <span>{{ $sortLabel }}</span>@if($sort === $sortKey)<i class="bi bi-check-lg"></i>@endif
                                </a>
                            @endforeach
                        </div>
                    </details>
                </div>
            </form>
        </div>
    </section>

    <section class="catalog-results">
        <div class="container">
            <div class="results-heading">
                <div><h2>DAFTAR PRODUK</h2></div>
                <p><strong>{{ $products->total() }}</strong> produk</p>
            </div>

            @if($search || $kategoriFilter || $budget)
                <div class="active-filter-row">
                    <span>FILTER AKTIF:</span>
                    @if($search)<b>“{{ $search }}”</b>@endif
                    @if($selectedCategory)<b>{{ $selectedCategory->nama_kategori }}</b>@endif
                    @if($budget)<b>{{ $budgetRanges[$budget]['label'] }}</b>@endif
                    <a href="{{ route('product.index') }}">HAPUS SEMUA ×</a>
                </div>
            @endif

            @if($products->isEmpty())
                <div class="catalog-empty">
                    <i class="bi bi-camera"></i><h3>BELUM ADA YANG COCOK.</h3>
                    <p>Coba pilih rentang budget atau kategori lainnya.</p>
                    <a href="{{ route('product.index') }}">LIHAT SEMUA PRODUK</a>
                </div>
            @else
                <div class="catalog-product-grid">
                    @foreach($products as $product)
                        <article class="catalog-product-card">
                            <a href="{{ route('product.show', $product->id) }}" class="catalog-product-link">
                                <div class="catalog-product-media">
                                    @if($product->grade === 'A')<span class="catalog-badge">PILIHAN</span>@endif
                                    @if($product->stok_produk <= 0)<span class="catalog-badge sold">STOK HABIS</span>@endif
                                    <img src="{{ $product->gambarUtama?->thumbnail_url ?? asset('mainIMG/produk.webp') }}" alt="{{ $product->nama_produk }}" loading="lazy" decoding="async">
                                </div>
                                <div class="catalog-product-info">
                                    <small>{{ $product->kategori?->nama_kategori ?? 'Kamera' }}</small>
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

                @if($products->hasPages())
                    <nav class="brutal-pagination" aria-label="Navigasi halaman katalog">
                        @if($products->onFirstPage())<span class="disabled"><i class="bi bi-arrow-left"></i></span>@else<a href="{{ $products->previousPageUrl() }}" aria-label="Halaman sebelumnya"><i class="bi bi-arrow-left"></i></a>@endif
                        @php
                            $visiblePages = array_unique(array_merge([1, $products->lastPage()], range(max(1, $products->currentPage() - 1), min($products->lastPage(), $products->currentPage() + 1))));
                            sort($visiblePages);
                            $previousPage = 0;
                        @endphp
                        @foreach($visiblePages as $page)
                            @if($previousPage && $page - $previousPage > 1)<span class="disabled" aria-hidden="true">...</span>@endif
                            <a href="{{ $products->url($page) }}" class="{{ $page === $products->currentPage() ? 'active' : '' }}" @if($page === $products->currentPage()) aria-current="page" @endif>{{ $page }}</a>
                            @php
                                $previousPage = $page;
                            @endphp
                        @endforeach
                        @if($products->hasMorePages())<a href="{{ $products->nextPageUrl() }}" aria-label="Halaman berikutnya"><i class="bi bi-arrow-right"></i></a>@else<span class="disabled"><i class="bi bi-arrow-right"></i></span>@endif
                    </nav>
                @endif
            @endif
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.catalog-dropdown').forEach((dropdown) => {
        dropdown.addEventListener('toggle', () => {
            if (!dropdown.open) return;
            document.querySelectorAll('.catalog-dropdown[open]').forEach((other) => {
                if (other !== dropdown) other.removeAttribute('open');
            });
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('.catalog-dropdown')) return;
        document.querySelectorAll('.catalog-dropdown[open]').forEach((dropdown) => dropdown.removeAttribute('open'));
    });
</script>
@endpush
