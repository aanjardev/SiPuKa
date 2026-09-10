@php
    $setting = \App\Models\CatalogSettings::first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth | Dinoyo Kamera')</title>
    <link rel="shortcut icon" href="{{ $setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" type="image/png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/form-validation.css') }}" rel="stylesheet">
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/auth.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>
    @stack('styles')
</head>
<body class="auth-body">
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">
            <div class="col-lg-6 auth-wrapper">
                <div class="auth-card-container">
                    <div class="auth-card">
                        @yield('content')
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-flex login-bg-side">
                <div class="bg-element bg-element-1"></div>
                <div class="bg-element bg-element-2"></div>
                <div class="bg-element bg-element-3"></div>

                <div class="bg-content">
                    <div class="brand-container">
                        <div class="company-logo">
                            <img src="{{ $setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" alt="{{ $setting?->nama_website ?? 'Dinoyo Kamera' }}" class="logo-img">
                        </div>
                        <div class="brand-text">
                            <h1 class="company-name">SiDiKa</h1>
                            <p class="company-tagline">Sistem Informasi Dinoyo Kamera</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/form-validation.js') }}"></script>
    @stack('scripts')
</body>
</html>
