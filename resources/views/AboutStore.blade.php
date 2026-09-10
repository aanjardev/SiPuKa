@extends('layouts.customer')

@section('title', 'Tentang Kami - ' . ($cat_setting->nama_website ?? 'Dinoyo Kamera'))

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/AboutStore.css') }}">
    <style>
        .hero-section {
            position: relative;
            background: url("{{ asset('mAboutIMG/about.avif') }}") center/cover no-repeat;
            min-height: 70vh;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.699);
            z-index: 0;
        }

        .hero-section > .container {
            position: relative;
            z-index: 1;
        }

        .customer-gallery {
            position: relative;
            overflow: hidden;
            height: 340px;
            display: flex;
            align-items: center;
        }

        .customer-gallery-track {
            display: flex;
            transition: transform 0.6s ease;
            will-change: transform;
            align-items: center;
        }

        .customer-gallery-item {
            flex: 0 0 220px;
            padding: 0 8px;
            transition: transform 0.4s ease, opacity 0.4s ease, flex-basis 0.4s ease;
            opacity: 0.55;
        }

        .customer-gallery-item.is-active {
            flex-basis: 235px;
            transform: scale(1.01);
            opacity: 1;

        }

        .customer-gallery-frame {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 10px;
            background: #f8f9fa;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;

            transform: translateZ(0);
            backface-visibility: hidden;
            will-change: transform;
            padding: 6px;
            box-sizing: border-box;
        }

        .customer-gallery-item.is-active .customer-gallery-frame {
            transform: scale(1.01);
            padding: 8px;
            transform-origin: center;
            translate: 0 -6px;
        }

        .customer-gallery-media {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            overflow: hidden;

            isolation: isolate;
            -webkit-mask-image: -webkit-radial-gradient(white, black);

            transform: translateZ(0);
            backface-visibility: hidden;
            outline: 1px solid transparent;
        }

        .customer-gallery-item.is-active .customer-gallery-media {
            transform: scale(1.04);
        }

        .customer-gallery-controls {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            pointer-events: none;
        }

        .customer-gallery-btn {
            pointer-events: auto;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            color: #1f2a37;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .customer-gallery-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.16);
        }

        .customer-gallery-btn:focus-visible {
            outline: 2px solid #1f2a37;
            outline-offset: 3px;
        }

        .customer-gallery-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 10px;
            outline: 1px solid transparent;
            transform: translateZ(0);
        }

        @media (min-width: 992px) {
            .customer-gallery-item {
                flex-basis: 260px;
            }

            .customer-gallery-item.is-active {
                flex-basis: 280px;
            }

            .customer-gallery {
                height: 380px;
            }
        }

        @media (max-width: 767px) {
            .customer-gallery-item {
                flex-basis: 180px;
            }

            .customer-gallery-item.is-active {
                flex-basis: 200px;
            }

            .customer-gallery {
                height: 280px;
            }
        }
    </style>
@endpush

@section('content')

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-lg-10 col-xl-8" data-aos="fade-up">
                    <h1 class="hero-title">
                        {{ $cat_setting->nama_website}}
                    </h1>
                    <p class="hero-description">
                        {{ $cat_setting->description ?? 'Kami adalah destinasi terpercaya untuk semua kebutuhan fotografi Anda. Dengan pengalaman lebih dari 10 tahun, kami menyediakan peralatan berkualitas dan layanan profesional untuk membantu mewujudkan visi kreatif Anda.' }}
                    </p>
                    <div class="d-flex justify-content-center gap-3 py-3">
                        <a href="#about" class="btn btn-primary" style="background-color: var(--secondary-color); border: none;">Pelajari Lebih Lanjut</a>
                        <a href="/contact" class="btn btn-outline-light">Hubungi Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Values Section -->
        <section class="py-5" id="about">
            <div class="container">
                <div class="text-center mb-5">
                    <p class="section-subtitle" data-aos="fade-up">Nilai Kami</p>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Mengapa Memilih Kami?</h2>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6" data-aos="fade-up">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="https://i.pinimg.com/736x/be/a0/3f/bea03f380b77cc4e04fd64072fbbdef0.jpg" alt="Reputasi" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Reputasi Terpercaya</h3>
                                        <p class="text-muted">{{ $cat_setting->nama_website}} telah menjadi komunitas penggemar kamera, menawarkan keaslian dan akurasi dalam setiap produk dan layanan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="{{ asset('mAboutIMG/1About.png') }}" alt="Kepercayaan" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Kepercayaan</h3>
                                        <p class="text-muted">Dengan lebih dari 10.000 item dalam stok, kami menawarkan pilihan untuk setiap fotografer, dari pemula hingga profesional, sesuai dengan visi dan anggaran Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="https://i.pinimg.com/736x/0a/f3/29/0af329768bfdb55522a37a064af27ff9.jpg" alt="Kesetiaan" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Kesetiaan</h3>
                                        <p class="text-muted">Kami menawarkan transparansi penuh dengan foto individual untuk setiap item. Tim kami siap membantu menjawab pertanyaan Anda kapan saja.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="https://i.pinimg.com/736x/6a/9c/3b/6a9c3b87e31d4d3e76a536af1e8a6138.jpg" alt="Kualitas" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Kualitas</h3>
                                        <p class="text-muted">Setiap item diuji dengan peralatan profesional untuk memastikan kecepatan rana, pengukuran, dan akurasi eksposur memenuhi standar tinggi kami.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="feature-section" id="features">
            <div class="container">
                <div class="text-center mb-5">
                    <p class="section-subtitle" data-aos="fade-up">Layanan Kami</p>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Apa yang Kami Tawarkan</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="feature-card text-center">
                            <div class="feature-icon">
                                <i class="bi bi-arrow-down-circle"></i>
                            </div>
                            <h3 class="h5 mb-3">Pembelian Kamera</h3>
                            <p class="text-muted">Punya kamera bekas yang jarang dipakai? Jual aja ke Dinoyo Kamera! Proses cepat, penilaian jujur & transparan, dan uang langsung cair tanpa ribet.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="feature-card text-center">
                            <div class="feature-icon">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <h3 class="h5 mb-3">Penjualan Kamera</h3>
                            <p class="text-muted">Cari kamera atau aksesoris? Di Dinoyo Kamera ada banyak pilihan kamera baru & bekas berkualitas. Kondisi terjamin, siap nemenin setiap momen terbaikmu.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="feature-card text-center">
                            <div class="feature-icon">
                                <i class="bi bi-tools"></i>
                            </div>
                            <h3 class="h5 mb-3">Servis Kamera</h3>
                            <p class="text-muted">Kamera bermasalah? Tenang! Bawa ke Dinoyo Kamera dan biarkan teknisi profesional kami yang menangani. Dicek dengan teliti, dijelasin dengan jelas, dan siap dipakai lagi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($gallery->isNotEmpty())
        <section class="py-5" id="gallery">
            <div class="container mb-5">
                <div class="text-center mb-5">
                    <p class="section-subtitle" data-aos="fade-up">Galeri Customer</p>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Momen Bersama Dinoyo Kamera</h2>
                </div>
                <div class="customer-gallery">
                    <div class="customer-gallery-track" id="customerGalleryTrack">
                        @foreach ($gallery as $index => $item)
                            <div class="customer-gallery-item {{ $index === 0 ? 'is-active' : '' }}">
                                <div class="customer-gallery-frame">
                                    <div class="customer-gallery-media">
                                        <img src="{{ $item->url }}" alt="Galeri customer {{ $index + 1 }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="customer-gallery-controls">
                        <button type="button" class="customer-gallery-btn" id="customerGalleryPrev" aria-label="Geser ke kiri">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="customer-gallery-btn" id="customerGalleryNext" aria-label="Geser ke kanan">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        @endif


    </main>

    <!-- Back to Top -->
    <button id="backToTop" class="btn">
        <i class="bi bi-arrow-up"></i>
    </button>

@endsection

@push('scripts')
    <script src="{{ asset('js/about.js') }}"></script>
    @if ($gallery->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const track = document.getElementById('customerGalleryTrack');
            if (!track) return;

            const originalItems = Array.from(track.querySelectorAll('.customer-gallery-item'));
            if (originalItems.length === 0) return;

            const prevButton = document.getElementById('customerGalleryPrev');
            const nextButton = document.getElementById('customerGalleryNext');

            const cloneCount = Math.min(originalItems.length, 6);
            const prependClones = originalItems.slice(-cloneCount).map(item => {
                const clone = item.cloneNode(true);
                clone.classList.add('is-clone');
                return clone;
            });
            const appendClones = originalItems.slice(0, cloneCount).map(item => {
                const clone = item.cloneNode(true);
                clone.classList.add('is-clone');
                return clone;
            });

            prependClones.forEach(clone => track.prepend(clone));
            appendClones.forEach(clone => track.appendChild(clone));

            const items = Array.from(track.querySelectorAll('.customer-gallery-item'));

            let activeIndex = cloneCount;
            let isAnimating = false;
            let autoTimer = null;

            const setPosition = (animate = true) => {
                track.style.transition = animate ? 'transform 0.6s ease' : 'none';
                const itemWidth = items[0].getBoundingClientRect().width;
                const offset = (track.parentElement.getBoundingClientRect().width / 2) - (itemWidth / 2) - (activeIndex * itemWidth);
                track.style.transform = `translateX(${offset}px)`;
            };

            const updateGallery = (animate = true) => {
                items.forEach((item, index) => {
                    item.classList.toggle('is-active', index === activeIndex);
                });

                setPosition(animate);
            };

            const goTo = (direction) => {
                if (isAnimating) return;
                isAnimating = true;
                activeIndex += direction;
                updateGallery(true);
            };

            const startAuto = () => {
                if (autoTimer) clearInterval(autoTimer);
                autoTimer = setInterval(() => {
                    goTo(1);
                }, 3000);
            };

            track.addEventListener('transitionend', () => {
                isAnimating = false;
                const maxIndex = items.length - cloneCount;
                if (activeIndex >= maxIndex) {
                    activeIndex = cloneCount;
                    updateGallery(false);
                }
                if (activeIndex < cloneCount) {
                    activeIndex = items.length - (cloneCount * 2);
                    updateGallery(false);
                }
            });

            prevButton?.addEventListener('click', () => {
                goTo(-1);
                startAuto();
            });

            nextButton?.addEventListener('click', () => {
                goTo(1);
                startAuto();
            });

            updateGallery(false);
            startAuto();
            window.addEventListener('resize', () => updateGallery(false));
        });
    </script>
    @endif
@endpush
