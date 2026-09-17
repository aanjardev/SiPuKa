@php
    $setting = \App\Models\CatalogSettings::first();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk | ' . ($setting?->nama_website ?? 'PusatKamera.id'))</title>
    <link rel="shortcut icon" href="{{ $setting?->logo_url ?: asset('mainIMG/logopk.png') }}" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/form-validation.css') }}" rel="stylesheet">
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/auth.css') }}?v=pk-login-3" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>
    @stack('styles')
</head>
<body class="auth-body">
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">
            <div class="col-md-5 auth-wrapper">
                <div class="auth-card-container">
                    <a href="{{ url('/') }}" class="auth-mobile-brand" aria-label="Kembali ke halaman utama">
                        <img src="{{ $setting?->logo_url ?: asset('mainIMG/logopk.png') }}" onerror="this.onerror=null;this.src='{{ asset('mainIMG/logopk.png') }}'" alt="Logo PusatKamera.id">
                        <span>PusatKamera.id</span>
                    </a>
                    <div class="auth-card">
                        @yield('content')
                    </div>
                    <p class="auth-footer">© {{ date('Y') }} PusatKamera.id · Akses internal</p>
                </div>
            </div>

            <div class="col-md-7 login-bg-side" style="display: flex !important;">
                <div class="auth-grid-pattern" aria-hidden="true"></div>
                <span class="auth-corner-label">EST. MALANG</span>
                <div class="bg-content">
                    <div class="brand-container">
                        <div class="company-logo">
                            <img src="{{ $setting?->logo_url ?: asset('mainIMG/logopk.png') }}" onerror="this.onerror=null;this.src='{{ asset('mainIMG/logopk.png') }}'" alt="Logo {{ $setting?->nama_website ?? 'PusatKamera.id' }}" class="logo-img">
                        </div>
                        <div class="brand-text">
                            <span class="brand-kicker">SISTEM INTERNAL</span>
                            <h1 class="company-name">PUSAT<br><em>KAMERA.ID</em></h1>
                            <p class="company-tagline">Kelola katalog, stok, dan transaksi dalam satu ruang kerja.</p>
                        </div>
                    </div>
                </div>
                <div class="auth-yellow-block" aria-hidden="true"><span>PK</span></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
    @stack('scripts')
</body>
</html>
