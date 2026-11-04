@extends('layouts.customer')
@section('title', 'Jual Kamera | PusatKamera.id')
@section('meta_description', 'Panduan menjual kamera bekas ke PusatKamera. Pelajari alurnya dan tawarkan kameramu melalui WhatsApp.')
@push('styles')
<link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/sell-camera.css') }}?v=1">
@endpush
@section('content')
<main class="sell-camera-page">
    <div class="container">
        <header class="sell-camera-heading">
            <h1>JUAL KAMERA</h1>
            <p>Ingin jual kamera? Ikuti panduannya, lalu hubungi tim kami.</p>
        </header>
        <div class="sell-camera-posters">
            @forelse($posters as $poster)
                <button type="button" class="sell-camera-poster" data-original="{{ $poster->original_url }}" data-poster-alt="{{ $poster->alt_text ?: 'Panduan jual kamera — bagian '.$loop->iteration }}" aria-haspopup="dialog" aria-controls="poster-dialog" aria-label="Perbesar {{ $poster->alt_text ?: 'Panduan jual kamera — bagian '.$loop->iteration }}">
                    <img src="{{ $poster->preview_url }}" alt="{{ $poster->alt_text ?: 'Panduan jual kamera — bagian '.$loop->iteration }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" @if($loop->first) fetchpriority="high" @endif decoding="async">
                </button>
            @empty
                <p class="sell-camera-empty">Panduan bergambar akan segera tersedia. Kamu tetap bisa menghubungi tim kami untuk menawarkan kamera.</p>
            @endforelse
        </div>
        <div class="sell-camera-action">
            @if($whatsAppUrl)
                <a href="{{ $whatsAppUrl }}" class="sell-camera-cta" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Tawarkan Kamera via WhatsApp <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            @else
                <a href="{{ url('/contact') }}" class="sell-camera-cta">Hubungi Tim Kami <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            @endif
        </div>
    </div>
</main>
<dialog id="poster-dialog" class="poster-dialog" aria-labelledby="poster-dialog-title">
    <div class="poster-dialog-heading"><h2 id="poster-dialog-title">Panduan Jual Kamera</h2><form method="dialog"><button type="submit" aria-label="Tutup poster"><i class="bi bi-x-lg" aria-hidden="true"></i></button></form></div>
    <p class="poster-dialog-hint">Geser gambar untuk melihat detailnya.</p>
    <div class="poster-dialog-image"><img id="poster-dialog-image" alt=""></div>
</dialog>
@endsection
@push('scripts')
<script src="{{ asset('js/sell-camera.js') }}?v=1" defer></script>
@endpush
