@extends('layouts.admin')
@section('title', $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni')
@section('content')
<div class="content-wrapper">
    <h1 class="h3 mb-4">{{ $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h1>
    <div class="card"><div class="card-body">
        <p class="text-muted small">Masukkan ulasan asli yang boleh ditampilkan, tanpa mengubah maknanya. Screenshot dan rating boleh dikosongkan.</p>
        <form data-validate-form method="POST" action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" enctype="multipart/form-data" data-prevent-double-submit data-testimonial-form>
            @csrf
            @if($testimonial->exists) @method('PUT') @else<input type="hidden" name="submission_token" value="{{ old('submission_token', (string) \Illuminate\Support\Str::uuid()) }}">@endif
            @error('submission_token')<p class="text-danger small">{{ $message }}</p>@enderror
            <div class="mb-3">
                <label for="customer_name" class="form-label">Nama Pelanggan</label>
                <input id="customer_name" name="customer_name" maxlength="120" required value="{{ old('customer_name', $testimonial->customer_name) }}" class="form-control @error('customer_name') is-invalid @enderror">
                @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="review_text" class="form-label">Isi Ulasan</label>
                <textarea id="review_text" name="review_text" maxlength="2000" rows="6" required class="form-control @error('review_text') is-invalid @enderror">{{ old('review_text', $testimonial->review_text) }}</textarea>
                @error('review_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="source_name" class="form-label">Nama Sumber</label>
                    <input id="source_name" name="source_name" maxlength="80" required value="{{ old('source_name', $testimonial->source_name) }}" placeholder="Google Maps, Shopee, Tokopedia..." class="form-control @error('source_name') is-invalid @enderror">
                    @error('source_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="source_url" class="form-label">Tautan Sumber</label>
                    <input type="url" id="source_url" name="source_url" maxlength="2048" required value="{{ old('source_url', $testimonial->source_url) }}" placeholder="https://..." class="form-control @error('source_url') is-invalid @enderror">
                    <div class="form-text">Tautan ulasan asli, halaman produk, atau toko yang memuat ulasan.</div>
                    @error('source_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rating" class="form-label">Rating (opsional)</label>
                    <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror">
                        <option value="">Tidak dicantumkan</option>
                        @for($number = 1; $number <= 5; $number++)<option value="{{ $number }}" @selected((string) old('rating', $testimonial->rating) === (string) $number)>{{ $number }} dari 5</option>@endfor
                    </select>
                    @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="reviewed_at" class="form-label">Tanggal Ulasan (opsional)</label>
                    <input type="date" id="reviewed_at" name="reviewed_at" max="{{ now()->toDateString() }}" value="{{ old('reviewed_at', $testimonial->reviewed_at?->format('Y-m-d')) }}" class="form-control @error('reviewed_at') is-invalid @enderror">
                    @error('reviewed_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="screenshot" class="form-label">Screenshot (opsional)</label>
                @if($testimonial->screenshot_path)
                    <div class="mb-2"><img src="{{ $testimonial->screenshot_preview_url }}" alt="Screenshot ulasan {{ $testimonial->customer_name }}" style="max-width:100%;height:220px;object-fit:contain"></div>
                    <div class="form-check mb-2"><input type="checkbox" id="remove_screenshot" name="remove_screenshot" value="1" class="form-check-input" @checked(old('remove_screenshot', false))><label for="remove_screenshot" class="form-check-label">Hapus screenshot tersimpan</label></div>
                @endif
                <input type="file" name="screenshot" data-max-bytes="2097152" id="screenshot" accept="image/png,image/jpeg,image/webp" class="form-control @error('screenshot') is-invalid @enderror">
                <div class="form-text">PNG/JPG/WebP, maksimal 2 MB dan 5000 x 5000 piksel. Kosongkan untuk mempertahankan screenshot lama.</div>
                @error('screenshot')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <input type="hidden" name="is_active" value="0">
            <div class="form-check mb-4"><input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $testimonial->is_active))><label for="is_active" class="form-check-label">Tampilkan di website</label>@error('is_active')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <button type="submit" class="btn btn-primary">Simpan Testimoni</button><a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div></div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/testimonial-form.js') }}?v=1" defer></script>
@endpush
