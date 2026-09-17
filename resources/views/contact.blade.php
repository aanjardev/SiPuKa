@extends('layouts.customer')

@php
    $store = $stores->first();
    $rawPhone = preg_replace('/\D+/', '', $store['telepon'] ?? $cat_setting?->nomor_telepon ?? '');
    if ($rawPhone && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
        $rawPhone = '62' . $rawPhone;
    }
    $whatsAppUrl = $rawPhone ? 'https://wa.me/' . $rawPhone : null;
    $mapsEmbedUrl = $store['maps_embed_url'] ?? null;
    $socials = [
        ['url' => $cat_setting?->instagram_link, 'icon' => 'fa-instagram', 'label' => 'Instagram'],
        ['url' => $cat_setting?->tiktok_link, 'icon' => 'fa-tiktok', 'label' => 'TikTok'],
        ['url' => $cat_setting?->youtube_link, 'icon' => 'fa-youtube', 'label' => 'YouTube'],
        ['url' => $cat_setting?->facebook_link, 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
    ];
@endphp

@section('title', 'Hubungi Kami — PusatKamera.id')
@section('meta_description', 'Alamat, jam operasional, WhatsApp, dan lokasi toko ' . ($cat_setting?->nama_website ?? 'PusatKamera.id') . '.')

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
                        <a href="{{ $whatsAppUrl }}?text={{ urlencode('Halo PusatKamera.id, saya ingin konsultasi kamera sesuai kebutuhan dan budget saya.') }}" target="_blank" rel="noopener">CHAT WHATSAPP <i class="bi bi-arrow-up-right"></i></a>
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
                    @if($stores->count() > 1)
                        <div class="d-flex flex-wrap gap-2 mb-3" role="tablist" aria-label="Pilih cabang">
                            @foreach($stores as $index => $branch)
                                <button type="button" class="btn {{ $index === 0 ? 'btn-dark' : 'btn-outline-dark' }} branch-selector" data-branch="{{ $branch['id'] }}">{{ $branch['nama'] }}</button>
                            @endforeach
                        </div>
                    @endif
                    @forelse($stores as $index => $branch)
                    <article class="store-card branch-card" data-branch="{{ $branch['id'] }}" @if($index !== 0) hidden @endif>
                        <span class="store-label">CABANG {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $branch['nama'] }}</h3>
                        <ul>
                            <li><i class="bi bi-geo-alt-fill"></i><div><small>ALAMAT</small><strong>{{ $branch['alamat'] }}</strong></div></li>
                            <li><i class="bi bi-whatsapp"></i><div><small>WHATSAPP</small>@if($branch['wa_phone'])<a href="https://wa.me/{{ $branch['wa_phone'] }}" target="_blank" rel="noopener">{{ $branch['telepon'] }}</a>@else<strong>Nomor belum tersedia</strong>@endif</div></li>
                            <li><i class="bi bi-clock-fill"></i><div><small>HARI INI</small><strong>{{ data_get($branch, 'jam.hari_ini.hari', 'Jadwal') }} • {{ data_get($branch, 'jam.hari_ini.slot', 'Belum tersedia') }}</strong></div></li>
                        </ul>

                        @if(data_get($branch, 'jam.harian'))
                            <details class="hours-dropdown">
                                <summary>LIHAT JAM OPERASIONAL <i class="bi bi-chevron-down"></i></summary>
                                <div>
                                    @foreach($branch['jam']['harian'] as $day)
                                        <p><span>{{ $day['hari'] }}</span><strong>{{ $day['slot'] }}</strong></p>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </article>
                    @empty
                        <article class="store-card"><strong>Belum ada cabang aktif.</strong></article>
                    @endforelse

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
                        @forelse($stores as $index => $branch)
                            <div class="branch-map" data-branch="{{ $branch['id'] }}" @if($index !== 0) hidden @endif>
                                @if($branch['maps_embed_url'])
                                    <iframe src="{{ $branch['maps_embed_url'] }}" title="Lokasi {{ $branch['nama'] }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    <a class="maps-button" href="{{ $branch['maps_embed_url'] }}" target="_blank" rel="noopener">BUKA DI GOOGLE MAPS <i class="bi bi-arrow-up-right"></i></a>
                                @else
                                    <div><i class="bi bi-map"></i><strong>PETA SEGERA TERSEDIA</strong></div>
                                @endif
                            </div>
                        @empty
                            <div><i class="bi bi-map"></i><strong>PETA SEGERA TERSEDIA</strong></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-form-section">
        <div class="container">
            <div class="contact-form-heading"><span>03 / CERITAKAN KEBUTUHANMU</span><h2>BINGUNG PILIH<br>KAMERA YANG MANA?</h2></div>
            <form id="whatsappContactForm" class="contact-form" data-phone="{{ $store['wa_phone'] ?? '' }}">
                @if($stores->count() > 1)<label><span>CABANG</span><select id="contactBranch">@foreach($stores as $branch)<option value="{{ $branch['id'] }}" data-phone="{{ $branch['wa_phone'] }}">{{ $branch['nama'] }}</option>@endforeach</select></label>@endif
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
    const text = `Halo PusatKamera.id, saya ${name}.\n\nBudget: ${budget}\nKebutuhan: ${need}\n\nMohon rekomendasinya.`;
    const branch = document.getElementById('contactBranch')?.selectedOptions[0];
    const phone = branch?.dataset.phone || event.currentTarget.dataset.phone;
    if (phone) window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank', 'noopener');
});

document.querySelectorAll('.branch-selector').forEach((button) => {
    button.addEventListener('click', () => {
        const id = button.dataset.branch;
        document.querySelectorAll('.branch-card, .branch-map').forEach((item) => item.hidden = item.dataset.branch !== id);
        document.querySelectorAll('.branch-selector').forEach((item) => {
            item.classList.toggle('btn-dark', item === button);
            item.classList.toggle('btn-outline-dark', item !== button);
        });
        const select = document.getElementById('contactBranch');
        if (select) select.value = id;
    });
});
</script>
@endpush
