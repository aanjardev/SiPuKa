<!-- Navigation -->
@php
    $setting = \App\Models\CatalogSettings::first();
    $store = \App\Models\Branch::where('is_active', true)->first();
@endphp
<nav id="main-header" class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <span class="brand-logo"><img src="{{ $setting?->logo_url ?: asset('mainIMG/logopk.png') }}" onerror="this.onerror=null;this.src='{{ asset('mainIMG/logopk.png') }}'" alt="Logo {{ $setting?->nama_website ?? 'PusatKamera.id' }}"></span>
            <span class="brand-identity">
                <span class="brand-text">{{ strtoupper($setting?->nama_website ?? 'PusatKamera.id') }}</span>
                @if($store?->alamat)
                    <span class="brand-address">{{ $store->alamat }}</span>
                @endif
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="/">BERANDA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('katalog*') ? 'active' : '' }}" href="/katalog">KATALOG</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="/contact">KONTAK</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

@include('partials.alerts')
