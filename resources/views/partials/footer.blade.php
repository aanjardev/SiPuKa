@php
    $setting = $cat_setting ?? \App\Models\CatalogSettings::first();
    $store = \App\Models\Branch::with('jamOperasional')->where('is_active', true)->first();
    $phone = $store?->nomor_telepon ?: $setting?->nomor_telfon;
    $rawPhone = preg_replace('/\D+/', '', $phone ?? '');
    if ($rawPhone && str_starts_with($rawPhone, '0')) {
        $rawPhone = '62' . substr($rawPhone, 1);
    } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
        $rawPhone = '62' . $rawPhone;
    }
    $waLink = $rawPhone ? "https://wa.me/{$rawPhone}" : null;
    $mapLink = $store?->link_maps ?: ($store ? 'https://www.google.com/maps?q=' . urlencode($store->alamat) : null);
    $openHours = $store?->jamOperasional?->where('is_buka', true);
    $firstHours = $openHours?->first();
    $hoursText = $firstHours && $firstHours->jam_buka && $firstHours->jam_tutup
        ? substr($firstHours->jam_buka, 0, 5) . ' — ' . substr($firstHours->jam_tutup, 0, 5)
        : 'Hubungi kami untuk jam buka';
    $socials = [
        ['url' => $setting?->instagram_link, 'icon' => 'fa-instagram', 'label' => 'Instagram'],
        ['url' => $setting?->tiktok_link, 'icon' => 'fa-tiktok', 'label' => 'TikTok'],
        ['url' => $setting?->youtube_link, 'icon' => 'fa-youtube', 'label' => 'YouTube'],
        ['url' => $setting?->facebook_link, 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
    ];
@endphp

<footer id="main-footer" class="brutal-footer">
    <div class="container">
        <div class="footer-main pt-5">
            <div class="footer-intro">
                <span class="footer-logo"><img src="{{ asset('mainIMG/logopk.png') }}" alt="Logo Pusat Kamera Malang"></span>
                <h2>PUSAT KAMERA<br>MALANG.</h2>
                <p>Spesialis kamera pemula untuk pelajar, mahasiswa, ekstrakurikuler fotografi, dan lembaga pendidikan.</p>
            </div>

            <div class="footer-panel">
                <p class="footer-label">KUNJUNGI TOKO</p>
                <h3>{{ $store?->nama ?: 'Pusat Kamera Malang' }}</h3>
                <ul>
                    @if($store?->alamat)
                        <li><i class="bi bi-geo-alt-fill"></i><span>{{ $store->alamat }}</span></li>
                    @endif
                    @if($phone)
                        <li><i class="bi bi-whatsapp"></i><a href="{{ $waLink }}" target="_blank" rel="noopener">{{ $phone }}</a></li>
                    @endif
                    <li><i class="bi bi-clock-fill"></i><span>{{ $hoursText }} WIB</span></li>
                </ul>
                <div class="footer-actions">
                    @if($mapLink)<a href="{{ $mapLink }}" target="_blank" rel="noopener">BUKA MAPS <i class="bi bi-arrow-up-right"></i></a>@endif
                    @if($waLink)<a href="{{ $waLink }}" target="_blank" rel="noopener">CHAT KAMI <i class="bi bi-arrow-up-right"></i></a>@endif
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© {{ date('Y') }} PUSAT KAMERA MALANG</p>
            <nav aria-label="Navigasi footer">
                <a href="/">BERANDA</a><a href="{{ route('product.index') }}">KATALOG</a><a href="/contact">KONTAK</a>
            </nav>
            <div class="footer-socials">
                @foreach($socials as $social)
                    @if($social['url'])
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener" aria-label="{{ $social['label'] }}"><i class="fab {{ $social['icon'] }}"></i></a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</footer>

<link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/footer-brutal.css') }}">
