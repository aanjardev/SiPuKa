@php
    $setting = \App\Models\CatalogSettings::first();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $setting?->nama_website ?? 'PusatKamera.id')</title>
    <meta name="description" content="@yield('meta_description', $setting?->seo_description ?? $setting?->description ?? '')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $setting?->seo_title ?? $setting?->nama_website ?? 'PusatKamera.id')">
    <meta property="og:description" content="@yield('meta_description', $setting?->seo_description ?? $setting?->description ?? '')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    @if($setting?->og_image_url)<meta property="og:image" content="{{ $setting->og_image_url }}">@endif
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('mainIMG/logopk.png') }}?v=pusatkamera-2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('mainIMG/logopk.png') }}?v=pusatkamera-2">
    <link rel="apple-touch-icon" href="{{ asset('mainIMG/logopk.png') }}?v=pusatkamera-2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/header.css') }}?v=pk-navbar-3">
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/storefront.css') }}?v=2">

    @stack('styles')
</head>
<body>
    @include('partials.header')

    @yield('content')

    @include('partials.footer')
    @include('partials.floating-wa')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @stack('scripts')
</body>
</html>
