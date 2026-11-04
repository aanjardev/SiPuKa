@extends('layouts.admin')
@section('title', 'Edit Poster Jual Kamera')
@section('content')
<div class="content-wrapper">
    <h1 class="h3 mb-4">Edit Poster Jual Kamera</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.sell-camera-posters.update', $poster) }}" enctype="multipart/form-data" data-prevent-double-submit data-poster-upload>
            @csrf @method('PUT')
            <img src="{{ $poster->preview_url }}" alt="{{ $poster->alt_text ?: 'Poster panduan jual kamera' }}" style="display:block;max-width:100%;height:300px;object-fit:contain" class="mb-4">
            <div class="mb-3">
                <label for="image" class="form-label">Ganti Poster</label>
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" data-poster-files>
                <div class="form-text">Kosongkan untuk mempertahankan gambar. PNG/JPG/WebP, maksimal 2 MB dan 5000 x 5000 piksel.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label for="alt_text" class="form-label">Teks Alternatif (opsional)</label>
                <input type="text" name="alt_text" id="alt_text" maxlength="255" value="{{ old('alt_text', $poster->alt_text) }}" class="form-control @error('alt_text') is-invalid @enderror">
                <div class="form-text">Jelaskan isi poster secara singkat, misalnya: Alur menjual kamera dari luar kota.</div>
                @error('alt_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan Poster</button>
            <a href="{{ route('admin.sell-camera-posters.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div></div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/sell-camera-admin.js') }}?v=1" defer></script>
@endpush
