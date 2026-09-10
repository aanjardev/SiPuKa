@extends('layouts.customer')

@php
    $rawPhone = preg_replace('/\D+/', '', $store['telepon'] ?? $cat_setting?->nomor_telfon ?? '');
    if ($rawPhone && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
        $rawPhone = '62' . $rawPhone;
    }
    $whatsAppUrl = $rawPhone ? 'https://wa.me/' . $rawPhone : null;
    $mapsUrl = $store['link_maps'] ?? $store['embed'] ?? null;
    $socials = [
        ['url' => $cat_setting?->instagram_link, 'icon' => 'fa-instagram', 'label' => 'Instagram'],
        ['url' => $cat_setting?->tiktok_link, 'icon' => 'fa-tiktok', 'label' => 'TikTok'],
        ['url' => $cat_setting?->youtube_link, 'icon' => 'fa-youtube', 'label' => 'YouTube'],
        ['url' => $cat_setting?->facebook_link, 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
    ];
@endphp

@section('title', 'Hubungi Kami — Pusat Kamera Malang')

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/contact-brutal.css') }}?v=1">
@endpush

@section('content')
<main class="contact-brutal">
    {{-- <header class="contact-hero">
        <div class="container">
            <span class="contact-kicker">KONSULTASI GRATIS • TANPA BIKIN BINGUNG</span>
            <div class="contact-hero-grid">
                <h1>AYO<br><span>NGOBROL.</span></h1>
                <div>
                    <p>Ceritakan kebutuhan dan budgetmu. Kami bantu pilih kamera pertama atau paket fotografi yang paling pas.</p>
                    @if($whatsAppUrl)
                        <a href="{{ $whatsAppUrl }}?text={{ urlencode('Halo Pusat Kamera Malang, saya ingin konsultasi kamera sesuai kebutuhan dan budget saya.') }}" target="_blank" rel="noopener">CHAT WHATSAPP <i class="bi bi-arrow-up-right"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </header> --}}

    <section class="contact-main">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-details">
                    <div class="contact-section-title"><span>01</span><h2>KUNJUNGI TOKO</h2></div>
                    <article class="store-card">
                        <span class="store-label">SATU TOKO, SIAP MEMBANTU</span>
                        <h3>{{ $store['nama'] ?? 'Pusat Kamera Malang' }}</h3>
                        <ul>
                            <li><i class="bi bi-geo-alt-fill"></i><div><small>ALAMAT</small><strong>{{ $store['alamat'] ?? 'Alamat toko akan segera diperbarui.' }}</strong></div></li>
                            <li><i class="bi bi-whatsapp"></i><div><small>WHATSAPP</small>@if($whatsAppUrl)<a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener">{{ $store['telepon'] ?? $cat_setting?->nomor_telfon }}</a>@else<strong>Nomor belum tersedia</strong>@endif</div></li>
                            <li><i class="bi bi-clock-fill"></i><div><small>HARI INI</small><strong>{{ data_get($store, 'jam.hari_ini.hari', 'Jadwal') }} • {{ data_get($store, 'jam.hari_ini.slot', 'Belum tersedia') }}</strong></div></li>
                        </ul>

                        @if(data_get($store, 'jam.harian'))
                            <details class="hours-dropdown">
                                <summary>LIHAT JAM OPERASIONAL <i class="bi bi-chevron-down"></i></summary>
                                <div>
                                    @foreach($store['jam']['harian'] as $day)
                                        <p><span>{{ $day['hari'] }}</span><strong>{{ $day['slot'] }}</strong></p>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </article>

                    <div class="contact-socials">
                        <p>IKUTI KAMI</p>
                        <div>
                            @foreach($socials as $social)
                                @if($social['url'])<a href="{{ $social['url'] }}" target="_blank" rel="noopener"><i class="fab {{ $social['icon'] }}"></i><span>{{ strtoupper($social['label']) }}</span></a>@endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="contact-map-wrap">
                    <div class="contact-section-title"><span>02</span><h2>LOKASI KAMI</h2></div>
                    <div class="contact-map">
                        @if($store['embed'] ?? null)
                            <iframe src="{{ $store['embed'] }}" title="Lokasi Pusat Kamera Malang" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @else
                            <div><i class="bi bi-map"></i><strong>PETA SEGERA TERSEDIA</strong></div>
                        @endif
                        @if($mapsUrl)<a class="maps-button" href="{{ $mapsUrl }}" target="_blank" rel="noopener">BUKA DI GOOGLE MAPS <i class="bi bi-arrow-up-right"></i></a>@endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-form-section">
        <div class="container">
            <div class="contact-form-heading"><span>03 / CERITAKAN KEBUTUHANMU</span><h2>BINGUNG PILIH<br>KAMERA YANG MANA?</h2></div>
            <form id="whatsappContactForm" class="contact-form">
                <label><span>NAMA</span><input type="text" id="contactName" placeholder="Nama kamu" required></label>
                <label><span>BUDGET</span><select id="contactBudget"><option value="Belum menentukan budget">Pilih budget</option><option>500 Ribuan</option><option>1 Jutaan</option><option>2 Jutaan</option><option>3 Jutaan</option><option>4–5 Juta</option><option>6–10 Juta</option><option>Paket lembaga</option></select></label>
                <label class="full"><span>KEBUTUHAN</span><textarea id="contactMessage" rows="4" placeholder="Contoh: kamera pertama untuk belajar foto dan video..." required></textarea></label>
                <button type="submit" {{ !$whatsAppUrl ? 'disabled' : '' }}><i class="bi bi-whatsapp"></i> KIRIM VIA WHATSAPP <i class="bi bi-arrow-up-right"></i></button>
            </form>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
document.getElementById('whatsappContactForm')?.addEventListener('submit', (event) => {
    event.preventDefault();
    if (!event.currentTarget.reportValidity()) return;
    const name = document.getElementById('contactName').value.trim();
    const budget = document.getElementById('contactBudget').value;
    const need = document.getElementById('contactMessage').value.trim();
    const text = `Halo Pusat Kamera Malang, saya ${name}.\n\nBudget: ${budget}\nKebutuhan: ${need}\n\nMohon rekomendasinya.`;
    window.open(`https://wa.me/{{ $rawPhone }}?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
});
</script>
@endpush
