<!-- Footer -->
<footer id="main-footer" class="footer-area">
    @php
        $setting = \App\Models\CatalogSettings::first();
        $branches = \App\Models\Branch::with('jamOperasional')
            ->where('is_active', true)
            ->get();
    @endphp
    <div class="container">
        <div class="footer-row g-3">
            <!-- Company Description -->
            <div class="footer-col footer-col-company">
                <div class="footer-widget">
                    <h4 class="widget-title">{{$setting->nama_website}}</h4>
                    <p class="company-desc">{{$setting->description}}</p>
                    <div class="social-links mt-3">
                        <a href="{{$setting->facebook_link}}" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{$setting->instagram_link}}" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="{{$setting->youtube_link}}" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="{{$setting->tiktok_link}}" class="social-link"><i class="fab fa-tiktok"></i></a>
                        <a href="{{$setting->tokopedia_link}}" target="_blank" rel="noopener" class="social-link" title="Tokopedia">
                            <img src="https://i0.wp.com/x-mos.com/wp-content/uploads/2020/11/logo-tokopedia-icon-mascot-400x400-copy.png?fit=400%2C400&ssl=1&w=640" alt="Tokopedia" style="width:25px;height:25px;object-fit:contain;">
                        </a>
                        <a href="{{$setting->shopee_link}}" target="_blank" rel="noopener" class="social-link" title="Shopee">
                            <img src="https://img.icons8.com/?size=100&id=OO5wGWyvSK0L&format=png&color=FFFFFF" alt="Shopee" style="width:20px;height:20px;object-fit:contain;">
                        </a>
                    </div>
                </div>
            </div>

            @foreach($branches as $branch)
            @php
                $hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
                $todayIndo = $hariMap[now()->format('l')] ?? now()->translatedFormat('l');
                $todayRow = $branch->jamOperasional->firstWhere('hari', $todayIndo);
                if ($todayRow && $todayRow->is_buka) {
                    $slot = ($todayRow->jam_buka && $todayRow->jam_tutup)
                        ? \Illuminate\Support\Str::of($todayRow->jam_buka)->beforeLast(':') . ' - ' . \Illuminate\Support\Str::of($todayRow->jam_tutup)->beforeLast(':')
                        : 'Buka';
                } else {
                    $slot = 'Tutup';
                }
                $mapLink = $branch->link_maps ?: 'https://www.google.com/maps?q=' . urlencode($branch->nama . ' ' . $branch->alamat);
            @endphp
            <div class="footer-col footer-col-branch">
                <div class="footer-widget">
                    <h4 class="widget-title">{{ $branch->nama }}</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-clock"></i>
                            <div class="d-flex flex-column">
                                <button class="btn p-0 text-start d-inline-flex align-items-center justify-content-start gap-2 w-100"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#jamFooter{{ $loop->index }}"
                                    aria-expanded="false"
                                    aria-controls="jamFooter{{ $loop->index }}">
                                    <span class="text-white">Hari ini ({{ $todayIndo }}): {{ $slot }} <i class="fas fa-chevron-down small text-white" style="margin-top:-3px"></i></span>
                                </button>
                                <div class="collapse mt-1" id="jamFooter{{ $loop->index }}">
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach($branch->jamOperasional as $jam)
                                        @php
                                            $slotHarian = $jam->is_buka
                                                ? (\Illuminate\Support\Str::of($jam->jam_buka)->beforeLast(':') . ' - ' . \Illuminate\Support\Str::of($jam->jam_tutup)->beforeLast(':'))
                                                : 'Tutup';
                                        @endphp
                                        <li class="d-flex justify-content-between">
                                            <span class="text-white-50">{{ $jam->hari }}</span>
                                            <span class="text-white-50">{{ $slotHarian }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            @php
                                $rawPhone = preg_replace('/\D+/', '', $branch->nomor_telepon ?? '');
                                if ($rawPhone && str_starts_with($rawPhone, '0')) {
                                    $rawPhone = '62' . substr($rawPhone, 1);
                                } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
                                    $rawPhone = '62' . $rawPhone;
                                }
                                $waLink = $rawPhone ? "https://wa.me/{$rawPhone}" : '#';
                            @endphp
                            <a href="{{ $waLink }}" target="_blank" rel="noopener">{{ $branch->nomor_telepon }}</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $branch->alamat }}</span>
                        </li>
                        <li>
                            <i class="fas fa-location-arrow"></i>
                            <a href="{{ $mapLink }}" target="_blank">Lihat di Google Maps</a>
                        </li>
                    </ul>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <div class="row">
                <div class="col-12">
                    <div class="copyright text-center">
                        <p>&copy; {{ date('Y') }} Dinoyo Kamera. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Footer styles -->
{{-- <link rel="stylesheet" href="{{ asset('css/footer.css') }}"> --}}

<style>

:root {
    --footer-bg: #020405;
    --footer-text: #ffffff;
    --footer-muted: rgba(255, 255, 255, 0.7);
    --footer-link: #EA4E2D;
    --footer-link-hover: #ff6b4a;
    --footer-border: rgba(255, 255, 255, 0.1);
}

.footer-area {
    background-color: var(--footer-bg);
    color: var(--footer-text);
    padding: 5rem 0 2rem;
    position: relative;
}

/* Flexible Footer Row Layout */
.footer-row {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    margin: 0 -0.5rem;
}

.footer-col {
    flex: 0 0 calc(20% - 1rem);
    padding: 0 0.5rem;
}

.footer-col-company {
    flex: 0 0 calc(20% - 1rem);
}

.footer-col-branch {
    flex: 0 0 calc(18% - 1rem);
}

.footer-widget {
    margin-bottom: 2rem;
}

.widget-title {
    color: var(--footer-text);
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.75rem;
}

.widget-title::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 250px;
    height: 2px;
    background-color: var(--footer-link);
}

.company-desc {
    color: var(--footer-muted);
    line-height: 1.8;
    margin-bottom: 1.5rem;
}


.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--footer-text);
    transition: all 0.3s ease;
    text-decoration: none;
}

.social-link:hover {
    background-color: var(--footer-link);
    color: var(--footer-text);
    transform: translateY(-3px);
}


.contact-info {
    list-style: none;
    padding: 0;
    margin: 0;
}

.contact-info li {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.85rem;
    color: var(--footer-muted);
}

.contact-info li i {
    margin-right: 0.75rem;
    margin-top: 0.25rem;
    color: var(--footer-link);
}

.contact-info li span {
    flex: 1;
    line-height: 1.5;
    font-size: 0.95rem;
}

.contact-info li a {
    color: var(--footer-muted);
    text-decoration: none;
    transition: all 0.3s ease;
}

.contact-info li a:hover {
    color: var(--footer-link);
}

/* Copyright Section */
.footer-bottom {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid var(--footer-border);
}

.copyright {
    color: var(--footer-muted);
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 1399px) {
    .footer-col {
        flex: 0 0 calc(25% - 1rem);
    }

    .footer-col-company {
        flex: 0 0 calc(25% - 1rem);
    }

    .footer-col-branch {
        flex: 0 0 calc(25% - 1rem);
    }
}

@media (min-width: 1200px) {
    .footer-area .container {
        max-width: 100%;
        padding-left: 2rem;
        padding-right: 2rem;
    }
}

@media (max-width: 991px) {
    .footer-area {
        padding: 4rem 0 2rem;
    }

    .footer-row {
        gap: 1.5rem;
        margin: 0 -0.25rem;
    }

    .footer-col {
        flex: 0 0 calc(50% - 0.75rem);
        padding: 0 0.25rem;
    }

    .footer-col-company {
        flex: 0 0 calc(50% - 0.75rem);
    }

    .footer-col-branch {
        flex: 0 0 calc(50% - 0.75rem);
    }
}

@media (max-width: 767px) {
    .footer-widget {
        margin-bottom: 2.5rem;
    }

    .widget-title {
        margin-bottom: 1rem;
    }

    .footer-row {
        gap: 1rem;
        margin: 0;
    }

    .footer-col {
        flex: 0 0 100%;
        padding: 0;
    }

    .footer-col-company {
        flex: 0 0 100%;
    }

    .footer-col-branch {
        flex: 0 0 100%;
    }

    .footer-bottom {
        margin-top: 2rem;
    }
}

@media (max-width: 576px) {
    .footer-area {
        padding: 3rem 0 1.5rem;
    }

    .social-links {
        justify-content: flex-start;
    }

    .widget-title::after {
        left: 0;
        transform: none;
    }

    .footer-widget {
        text-align: left;
    }

    .contact-info li {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: start;
        justify-content: center;
        column-gap: 0.5rem;
        text-align: left;
        margin-bottom: 0.65rem;
    }

    .contact-info li i {
        margin: 0.15rem 0 0 0;
    }

    .contact-info li span,
    .contact-info li a {
        font-size: 0.9rem;
        line-height: 1.45;
        white-space: normal;
        word-break: break-word;
    }
}
</style>
