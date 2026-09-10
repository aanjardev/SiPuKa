@extends('layouts.customer')

@section('title', 'Hubungi Kami - ' . ($cat_setting->nama_website ?? 'Dinoyo Kamera'))

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/contactStore.css') }}">
@endpush

@section('content')

    <!-- Main Content -->
    <main class="contact-page">
        <div class="container">

            <!-- Social Media & Marketplace -->
            <div class="platform-card mb-5" data-aos="fade-up">
                <div class="text-center mb-4">
                    <h2 class="platform-title">Temukan Kami di Berbagai Platform</h2>
                    <p class="platform-subtitle">Ikuti dan belanja di kanal resmi {{ $cat_setting->nama_website }}.</p>
                </div>
                <div class="platform-grid">
                    <a href="{{ $cat_setting->facebook_link }}" class="platform-item" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                    </a>
                    <a href="{{ $cat_setting->instagram_link }}" class="platform-item" target="_blank" rel="noopener">
                        <i class="fab fa-instagram"></i>
                        <span>Instagram</span>
                    </a>
                    <a href="{{ $cat_setting->youtube_link }}" class="platform-item" target="_blank" rel="noopener">
                        <i class="fab fa-youtube"></i>
                        <span>YouTube</span>
                    </a>
                    <a href="{{ $cat_setting->tiktok_link }}" class="platform-item" target="_blank" rel="noopener">
                        <i class="fab fa-tiktok"></i>
                        <span>TikTok</span>
                    </a>
                    <a href="{{ $cat_setting->tokopedia_link }}" class="platform-item" target="_blank" rel="noopener">
                        <img src="https://raw.githubusercontent.com/aanjardev/assets/main/icon/tokopedia-svgrepo-com.png" alt="Tokopedia">
                        <span>Tokopedia</span>
                    </a>
                    <a href="{{ $cat_setting->shopee_link }}" class="platform-item" target="_blank" rel="noopener">
                        <img src="https://img.icons8.com/?size=100&id=OO5wGWyvSK0L&format=png&color=000000" alt="Shopee">
                        <span>Shopee</span>
                    </a>
                </div>
            </div>

            <!-- Store Locations -->
            <div class="row g-3 justify-content-center" data-aos="fade-up" data-aos-delay="100">
                @foreach($branches as $branch)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="location-card h-100 d-flex flex-column">
                        <div class="map-container">
                            <iframe src="{{ $branch['embed'] }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="card-body d-flex flex-column" style="padding-left: 13px; padding-right: 13px;">
                            <h3 class="store-name">{{ $branch['nama'] }}</h3>
                            <div class="info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <button class="btn p-0 text-start d-inline-flex align-items-center justify-content-between w-300" style="margin-left:-2px;"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#jamLengkap{{ $loop->index }}"
                                        aria-expanded="false"
                                        aria-controls="jamLengkap{{ $loop->index }}">
                                        <span class="fw-semibold text-dark" style="font-size: 14px !important;">Hari ini ({{ $branch['jam']['hari_ini']['hari'] }}): {{ $branch['jam']['hari_ini']['slot'] }}</span>
                                        <i class="fas fa-chevron-down small toggle-icon text-secondary" style="margin-left:10px; margin-top:-2px;"></i>
                                    </button>
                                    @if($branch['jam']['catatan'])
                                        <div class="store-closed text-muted small">{{ $branch['jam']['catatan'] }}</div>
                                    @endif
                                    <div class="collapse mt-1" id="jamLengkap{{ $loop->index }}">
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($branch['jam']['harian'] as $hari)
                                            <li class="d-flex justify-content-between">
                                                <span class="text-muted">{{ $hari['hari'] }}</span>
                                                <span class="text-end">{{ $hari['slot'] }}</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fab fa-whatsapp"></i>
                                <div>
                                    @php
                                        $rawPhone = preg_replace('/\D+/', '', $branch['telepon'] ?? '');
                                        if ($rawPhone && str_starts_with($rawPhone, '0')) {
                                            $rawPhone = '62' . substr($rawPhone, 1);
                                        } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
                                            $rawPhone = '62' . $rawPhone;
                                        }
                                        $waLink = $rawPhone ? "https://wa.me/{$rawPhone}" : '#';
                                    @endphp
                                    <a href="{{ $waLink }}" class="text-decoration-none text-dark" target="_blank" rel="noopener">{{ $branch['telepon'] }}</a>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    {{ $branch['alamat'] }}
                                </div>
                            </div>
                            <div class="mt-auto pt-2">
                                <a href="{{ $branch['link_maps'] ?? $branch['embed'] }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-map"></i> Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card" data-aos="fade-up" data-aos-delay="200">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h2 class="text-center mb-4">Kirim Pesan</h2>
                        <form id="whatsappContactForm" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" name="name" class="form-label">Nama Lengkap*</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="name" required>
                                        <div class="invalid-feedback">Mohon isi nama lengkap Anda</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email*</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" required>
                                        <div class="invalid-feedback">Mohon isi alamat email yang valid</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Pesan*</label>
                                    <textarea class="form-control" id="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">Mohon isi pesan Anda</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-submit w-100">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection

@push('scripts')
    <script>

        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true,
                offset: 100
            });
        }

        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

    document.getElementById('whatsappContactForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = this;

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const message = document.getElementById('message').value;

        const phoneNumber = '62895411200308'; // Ganti dengan nomor WhatsApp Anda!

        const whatsappMessage = `
Halo Dinoyo Kamera!%0A
Saya *${encodeURIComponent(name)}*, ingin bertanya:%0A%0A
*Pesan:*%0A${encodeURIComponent(message)}%0A%0A
*Email:* ${encodeURIComponent(email)}%0A%0A
        `.trim();

        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${whatsappMessage}`;

        window.open(whatsappUrl, '_blank');

        form.reset();
        form.classList.remove('was-validated');
    });
    </script>
@endpush
