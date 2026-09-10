@extends('layouts.customer')

@section('title', 'Dinoyo Kamera')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/typeit@8.7.1/dist/index.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/mainPage.css') }}">
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/katalog.css') }}">
@endpush

@section('content')

            <!-- Quick Search -->
            <section class="mt-5 mb-0 home-search-section" data-aos="fade-up">
                <div class="container">
                    <form method="GET" action="{{ route('product.index') }}" data-suggest-url="{{ route('product.suggest') }}">
                        <div class="search-suggest-wrapper">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       class="form-control search-input"
                                       id="homeSearchInput"
                                       name="search"
                                       placeholder="Cari produk..."
                                       value="{{ request('search') }}"
                                       autocomplete="off"
                                       aria-autocomplete="list"
                                       aria-controls="searchSuggestions"
                                       aria-expanded="false">
                                <button type="submit" class="btn btn-search-plain">
                                    Cari
                                </button>
                            </div>
                            <div class="search-suggestions" id="searchSuggestions" role="listbox"></div>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Promotion Carousel -->
            <section id="promotion" class="py-4" data-aos="fade-up">
                <div class="container">
                    <div id="demo" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach ($cat_banners as $index => $banner)
                                <button type="button" data-bs-target="#demo" data-bs-slide-to="{{ $index }}"
                                    class="{{ $index == 0 ? 'active' : '' }}"
                                    aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                                    aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($cat_banners as $index => $banner)
                                @php
                                    $path = $banner->banner_url;
                                    $url = $path;
                                    $alt = basename($path);
                                @endphp
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="banner-frame" style="--banner-url: url('{{ $url }}')">
                                    <img src="{{ $url }}" alt="{{ $alt }}" class="d-block banner-img">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </section>

        <!-- Categories -->
        <section id="Kategori" class="py-4" data-aos="fade-up" data-aos-delay="100">
            <div class="container d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Kategori</h2>
                <a href="/katalog" class="text-decoration-none">Lihat Selengkapnya</a>
            </div>
        </section>
        <section id="Kategori-Display" class="py-4" data-aos="fade-up" data-aos-delay="150">
            <div class="container">
                @if ($kategoris->isEmpty())
                    <div class="text-center text-muted py-4">Belum ada kategori yang dapat ditampilkan.</div>
                @else
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                        @foreach ($kategoris as $kategori)
                            <div class="col">
                                <a href="{{ route('product.index', ['kategori' => $kategori->id]) }}" class="category-link">
                                    <img src="{{ $kategori->image_url ?? asset('images/placeholder.jpg') }}"
                                        alt="{{ $kategori->nama_kategori }}"
                                        class="category-image">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section id="Kategori2" class="py-4" data-aos="fade-up">
            <div class="container d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Produk Terbaru</h2>
                <a href="{{ route('product.index', ['sort' => 'terbaru']) }}" class="text-decoration-none">Lihat Selengkapnya</a>
            </div>
        </section>

        <section class="py-4" data-aos="fade-up" data-aos-delay="200">
            <div class="container">
                @if ($latestProducts->isEmpty())
                    <div class="text-center py-5" data-aos="fade-up">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Tidak ada produk terbaru saat ini.</p>
                    </div>
                @else
                    <div class="d-flex justify-content-center row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                        @foreach ($latestProducts as $index => $product)
                            <div class="col" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                    <div class="product-card @if ($product->stok_produk == 0) sold-out @endif">
                                        <div class="product-image-wrapper">
                                            @if ($product->grade === 'Unggulan')
                                                <span class="featured-badge">
                                                    <i class="bi bi-star-fill me-1"></i>Unggulan
                                                </span>
                                            @endif
                                            @if ($product->stok_produk == 0)
                                                    <span class="sold-out-badge">SOLD OUT</span>
                                            @endif
                                            @if ($product->gambarUtama)
                                                <img src="{{ $product->gambarUtama->url }}"
                                                    alt="{{ $product->nama_produk }}"
                                                    class="product-image"
                                                    loading="lazy">
                                            @else
                                                <img src="{{ asset('images/placeholder.jpg') }}"
                                                    alt="No Image"
                                                    class="product-image">
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <h3 class="product-title" title="{{ $product->nama_produk }}">
                                                {{ $product->nama_produk }}
                                            </h3>
                                            <p class="product-price mb-0">
                                                Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section id="Kategori2" class="py-4" data-aos="fade-up">
            <div class="container d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Rekomendasi</h2>
                <a href="{{ route('product.index', ['sort' => 'rekomendasi']) }}" class="text-decoration-none">Lihat Selengkapnya</a>
            </div>
        </section>

        <section class="py-4" data-aos="fade-up" data-aos-delay="250">
            <div class="container">
                @if ($produkUnggulan->isEmpty())
                    <div class="text-center py-5" data-aos="fade-up">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Tidak ada produk rekomendasi saat ini.</p>
                    </div>
                @else
                    <div class="d-flex justify-content-center row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                        @foreach ($produkUnggulan as $index => $product)
                            <div class="col" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                    <div class="product-card @if ($product->stok_produk == 0) sold-out @endif">
                                        <div class="product-image-wrapper">
                                            @if ($product->grade === 'Unggulan')
                                                <span class="featured-badge">
                                                    <i class="bi bi-star-fill me-1"></i>Unggulan
                                                </span>
                                            @endif
                                            @if ($product->stok_produk == 0)
                                                    <span class="sold-out-badge">SOLD OUT</span>
                                            @endif
                                            @if ($product->gambarUtama)
                                                <img src="{{ $product->gambarUtama->url }}"
                                                    alt="{{ $product->nama_produk }}"
                                                    class="product-image"
                                                    loading="lazy">
                                            @else
                                                <img src="{{ asset('images/placeholder.jpg') }}"
                                                    alt="No Image"
                                                    class="product-image">
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <h3 class="product-title" title="{{ $product->nama_produk }}">
                                                {{ $product->nama_produk }}
                                            </h3>
                                            <p class="product-price mb-0">
                                                Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <!-- Brands -->
        <section id="Brand" class="py-4" data-aos="fade-up">
            <div class="container">
                <h2 class="section-title">Brands</h2>
            </div>
        </section>
        <section id="Kategori-Display-Brand" class="py-4" data-aos="fade-up" data-aos-delay="300">
            <div class="container">
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-4 g-3 justify-content-center">
                @foreach ($cat_partner as $index => $partner)
                    @php
                        $path = $partner->url;
                        $url = $path;
                        $alt = basename($path);
                    @endphp
                    <div class="col">
                        <div class="brand-card">
                            <img src="{{ $url }}" alt="{{ $alt }}" class="img-fluid">
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </section>

        <!-- YouTube Section -->
        <section class="youtube-section" data-aos="fade-up" data-aos-delay="350">
            <div class="container">
                <div class="youtube-title">
                    <h2>{{$cat_setting->nama_website}} Channel</h2>
                    <p>Temukan tips dan review produk terbaru dari tim kami di channel YouTube {{$cat_setting->nama_website}}</p>
                </div>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="youtube-container">
                            <iframe src="https://www.youtube.com/embed/3o2IrQrb7ws?si=t5g5UGYrifjmhKxD" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section id="Benefit" class="pt-4" data-aos="fade-up" data-aos-delay="400">
            <div class="container">
                <h2 class="section-title">Benefit</h2>
            </div>
        </section>
        <section id="Benefit-Item" class="py-4" data-aos="fade-up" data-aos-delay="450">
            <div class="container">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 text-center">
                    <div class="col">
                        <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/100___shopping_with_trust___comfort_Benefit_Customer-02.png" alt="Terpercaya" class="img-fluid mb-2">
                        <h2 class="benefit-title">100% Terpercaya</h2>
                    </div>
                    <div class="col">
                        <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/Tax-Free.png" alt="Bebas Pajak" class="img-fluid mb-2">
                        <h2 class="benefit-title">Bebas Pajak</h2>
                    </div>
                    <div class="col">
                        <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/Good_Experience_Shopping_at_Focus_Nusantara_Benefit_Customer-09.png" alt="Pengalaman Terbaik" class="img-fluid mb-2">
                        <h2 class="benefit-title">Pengalaman Terbaik</h2>
                    </div>
                    <div class="col">
                        <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/quality___friendly_service_Benefit_Customer-04.png" alt="Pelayanan Baik" class="img-fluid mb-2">
                        <h2 class="benefit-title">Kualitas Pelayanan Baik</h2>
                    </div>
                    <div class="col">
                        <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/GPP_Guaranteed_3_Years_Benefit_Customer-05.png" alt="Bergaransi" class="img-fluid mb-2">
                        <h2 class="benefit-title">Bergaransi</h2>
                    </div>
                    <div class="col">
                        <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/Free_Fast___Safe_to_Destination_Benefit_Customer-07.png" alt="Aman Sampai Tujuan" class="img-fluid mb-2">
                        <h2 class="benefit-title">Aman Sampai Tujuan</h2>
                    </div>
                </div>
            </div>
        </section>

@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/loadingScreen.js') }}"></script>
    <script type="module" src="{{ asset('js/productHover.js') }}"></script>
    <script type="module" src="{{ asset('js/scrollNavigation.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/typeit@8.7.1/dist/index.umd.min.js"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
    <script>
        const searchInput = document.getElementById('homeSearchInput');
        const suggestions = document.getElementById('searchSuggestions');
        const suggestForm = searchInput ? searchInput.closest('form') : null;
        const suggestUrl = suggestForm ? suggestForm.getAttribute('data-suggest-url') : '';
        let suggestTimeout;
        let activeController;
        let isPointerInside = false;

        function hideSuggestions() {
            if (suggestions) {
                suggestions.classList.remove('show');
                suggestions.innerHTML = '';
            }
            if (searchInput) {
                searchInput.setAttribute('aria-expanded', 'false');
            }
        }

        function showSuggestions(items) {
            if (!suggestions) return;
            suggestions.innerHTML = '';
            if (!items.length) {
                hideSuggestions();
                return;
            }

            items.forEach(item => {
                const link = document.createElement('a');
                link.href = item.url;
                link.className = 'suggestion-item';
                link.setAttribute('role', 'option');
                link.innerHTML = `
                    <span class="suggestion-media">
                        <img src="${item.thumbnail}" alt="${item.name}">
                    </span>
                    <span class="suggestion-text">
                        <span class="suggestion-name">${item.name}</span>
                        <span class="suggestion-meta">${item.price_formatted}</span>
                    </span>
                `;
                suggestions.appendChild(link);
            });

            suggestions.classList.add('show');
            if (searchInput) {
                searchInput.setAttribute('aria-expanded', 'true');
            }
        }

        function fetchSuggestions(query) {
            if (!suggestUrl) return;
            if (activeController) {
                activeController.abort();
            }
            activeController = new AbortController();
            const url = `${suggestUrl}?q=${encodeURIComponent(query)}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: activeController.signal
            })
                .then(response => response.ok ? response.json() : [])
                .then(data => showSuggestions(Array.isArray(data) ? data : []))
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        hideSuggestions();
                    }
                });
        }

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const value = searchInput.value.trim();
                clearTimeout(suggestTimeout);

                if (value.length < 2) {
                    hideSuggestions();
                    return;
                }

                suggestTimeout = setTimeout(() => {
                    fetchSuggestions(value);
                }, 250);
            });

            searchInput.addEventListener('focus', () => {
                if (suggestions && suggestions.children.length) {
                    suggestions.classList.add('show');
                    searchInput.setAttribute('aria-expanded', 'true');
                }
            });

            searchInput.addEventListener('blur', () => {
                setTimeout(() => {
                    if (!isPointerInside) {
                        hideSuggestions();
                    }
                }, 150);
            });
        }

        if (suggestions) {
            suggestions.addEventListener('mouseenter', () => {
                isPointerInside = true;
            });
            suggestions.addEventListener('mouseleave', () => {
                isPointerInside = false;
            });
        }

        document.addEventListener('click', (event) => {
            if (!suggestions || !searchInput) return;
            const wrapper = suggestions.closest('.search-suggest-wrapper');
            if (wrapper && !wrapper.contains(event.target)) {
                hideSuggestions();
            }
        });
    </script>
@endpush
