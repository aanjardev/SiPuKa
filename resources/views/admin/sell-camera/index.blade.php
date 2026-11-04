@extends('layouts.admin')
@section('title', 'Panduan Jual Kamera')
@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div><h1 class="h3 mb-1">Panduan Jual Kamera</h1><p class="text-muted mb-0">Unggah poster dan susun alur panduan untuk pelanggan.</p></div>
        <a href="{{ route('sell-camera') }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">Lihat Halaman</a>
    </div>
    <div class="card mb-4"><div class="card-body">
        <h2 class="h5">Tambah Poster</h2>
        <p class="text-muted small">PNG, JPG, atau WebP. Maksimal 5 gambar, 2 MB per gambar, total 6 MB, dan dimensi 5000 x 5000 piksel. Gambar ditambahkan setelah poster terakhir.</p>
        <form action="{{ route('admin.sell-camera-posters.store') }}" method="POST" enctype="multipart/form-data" data-prevent-double-submit data-poster-upload>
            @csrf
            <input type="hidden" name="submission_token" value="{{ old('submission_token', (string) \Illuminate\Support\Str::uuid()) }}">
            <label for="poster-images" class="form-label">Pilih Poster</label>
            <input type="file" id="poster-images" name="images[]" class="form-control mb-3" accept="image/png,image/jpeg,image/webp" multiple required data-poster-files>
            @foreach($errors->getMessages() as $field => $messages)
                @if($field === 'images' || str_starts_with($field, 'images.') || str_starts_with($field, 'alt_texts.') || $field === 'submission_token')
                    @foreach($messages as $message)<p class="text-danger small">{{ $message }}</p>@endforeach
                @endif
            @endforeach
            <div data-poster-alt-fields></div>
            <p class="text-muted small">Teks alternatif opsional. Jika kosong, digunakan judul panduan sesuai posisi poster. Pilih ulang file jika unggahan sebelumnya gagal.</p>
            <button type="submit" class="btn btn-primary">Unggah Poster</button>
        </form>
    </div></div>
    <h2 class="h5 mb-3">Poster Tersimpan</h2>
    <div class="row g-4">
        @forelse($posters as $poster)
            <div class="col-12 col-md-6 col-xl-4"><div class="card h-100"><div class="card-body">
                <a href="{{ $poster->original_url }}" target="_blank" rel="noopener"><img src="{{ $poster->preview_url }}" alt="{{ $poster->alt_text ?: 'Panduan jual kamera — bagian '.$loop->iteration }}" loading="lazy" style="width:100%;height:240px;object-fit:contain"></a>
                <p class="my-3">{{ $poster->alt_text ?: 'Panduan jual kamera — bagian '.$loop->iteration }}</p>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @foreach(['up' => 'Naik', 'down' => 'Turun'] as $direction => $label)
                        <form method="POST" action="{{ route('admin.sell-camera-posters.move', $poster) }}" data-prevent-double-submit>
                            @csrf @method('PATCH')
                            <input type="hidden" name="direction" value="{{ $direction }}"><input type="hidden" name="expected_position" value="{{ $poster->position }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary" @disabled(($direction === 'up' && $loop->parent->first) || ($direction === 'down' && $loop->parent->last))>{{ $label }}</button>
                        </form>
                    @endforeach
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.sell-camera-posters.edit', $poster) }}" class="btn btn-sm btn-outline-secondary">Edit / Ganti</a>
                    <form action="{{ route('admin.sell-camera-posters.destroy', $poster) }}" method="POST" data-prevent-double-submit onsubmit="return confirm('Hapus poster ini dari panduan jual kamera?')">
                        @csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                </div>
            </div></div></div>
        @empty
            <div class="col-12"><p class="text-muted">Belum ada poster. Halaman publik akan menampilkan keterangan singkat dan tombol kontak.</p></div>
        @endforelse
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/sell-camera-admin.js') }}?v=1" defer></script>
@endpush
