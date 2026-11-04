@extends('layouts.customer')
@section('title', 'Cerita Pelanggan | PusatKamera.id')
@section('meta_description', 'Ulasan pilihan dari pelanggan PusatKamera, dengan sumber ulasan asli dan pengalaman belanja mereka.')
@push('styles')
<link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/testimonials.css') }}?v=1">
@endpush
@section('content')
<main class="testimonials-page">
    <div class="container">
        <header class="testimonials-heading"><h1>CERITA PELANGGAN</h1><p>Ulasan pilihan dari pelanggan PusatKamera.</p></header>
        <div class="testimonials-grid">
            @forelse($testimonials as $testimonial)
                <article class="testimonial-entry">
                    <header class="testimonial-header">
                        <h2>{{ $testimonial->customer_name }}</h2>
                        <span class="testimonial-source">{{ $testimonial->source_name }}</span>
                    </header>
                    @if($testimonial->rating !== null)
                        <div class="testimonial-rating" role="img" aria-label="Rating {{ $testimonial->rating }} dari 5">
                            @for($star = 1; $star <= 5; $star++)<i class="bi {{ $star <= $testimonial->rating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>@endfor
                        </div>
                    @endif
                    @if($testimonial->reviewed_at)<time class="testimonial-date" datetime="{{ $testimonial->reviewed_at->format('Y-m-d') }}">{{ $testimonial->reviewed_at->translatedFormat('d F Y') }}</time>@endif
                    <blockquote><p>{!! nl2br(e($testimonial->review_text)) !!}</p></blockquote>
                    @if($testimonial->screenshot_path)
                        <button type="button" class="testimonial-screenshot" data-original="{{ $testimonial->screenshot_original_url }}" data-poster-alt="Screenshot ulasan {{ $testimonial->customer_name }} dari {{ $testimonial->source_name }}" aria-haspopup="dialog" aria-controls="poster-dialog" aria-label="Perbesar screenshot ulasan {{ $testimonial->customer_name }}">
                            <img src="{{ $testimonial->screenshot_preview_url }}" alt="Screenshot ulasan {{ $testimonial->customer_name }} dari {{ $testimonial->source_name }}" loading="lazy" decoding="async">
                            <span><i class="bi bi-zoom-in" aria-hidden="true"></i> Perbesar screenshot</span>
                        </button>
                    @endif
                    <a class="testimonial-original-link" href="{{ $testimonial->source_url }}" target="_blank" rel="noopener noreferrer">Lihat ulasan asli <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                </article>
            @empty
                <p class="testimonials-empty">Ulasan pelanggan akan segera hadir.</p>
            @endforelse
        </div>
        <div class="testimonial-pagination">{{ $testimonials->links('pagination::bootstrap-5') }}</div>
    </div>
</main>
<dialog id="poster-dialog" class="testimonial-dialog" aria-labelledby="testimonial-dialog-title">
    <div class="testimonial-dialog-heading"><h2 id="testimonial-dialog-title">Screenshot Ulasan Pelanggan</h2><form method="dialog"><button type="submit" aria-label="Tutup screenshot"><i class="bi bi-x-lg" aria-hidden="true"></i></button></form></div>
    <p class="testimonial-dialog-hint">Geser gambar untuk melihat detailnya.</p>
    <div class="poster-dialog-image"><img id="poster-dialog-image" alt=""></div>
</dialog>
@endsection
@push('scripts')
<script src="{{ asset('js/sell-camera.js') }}?v=1" defer></script>
@endpush
