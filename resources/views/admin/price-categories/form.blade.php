@extends('layouts.admin')

@section('title', isset($priceCategory) ? 'Edit Kategori Harga' : 'Tambah Kategori Harga')

@push('page-actions')
<a href="{{ route('admin.price-categories.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
@endpush

@section('content')
<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ isset($priceCategory) ? route('admin.price-categories.update', $priceCategory) : route('admin.price-categories.store') }}">
            @csrf
            @isset($priceCategory) @method('PUT') @endisset
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Nama kategori</label><input class="form-control @error('nama') is-invalid @enderror" name="nama" required value="{{ old('nama', $priceCategory->nama ?? '') }}">@error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label class="form-label">Slug (opsional)</label><input class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $priceCategory->slug ?? '') }}">@error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label class="form-label">Harga minimum</label><input type="number" min="0" class="form-control @error('harga_minimum') is-invalid @enderror" name="harga_minimum" required value="{{ old('harga_minimum', $priceCategory->harga_minimum ?? 0) }}">@error('harga_minimum')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label class="form-label">Harga maksimum</label><input type="number" min="0" class="form-control @error('harga_maksimum') is-invalid @enderror" name="harga_maksimum" value="{{ old('harga_maksimum', $priceCategory->harga_maksimum ?? '') }}"><div class="form-text">Kosongkan jika tanpa batas atas.</div>@error('harga_maksimum')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" min="0" class="form-control" name="urutan" required value="{{ old('urutan', $priceCategory->urutan ?? 0) }}"></div>
                <div class="col-12"><input type="hidden" name="is_active" value="0"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $priceCategory->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label" for="is_active">Tampilkan di katalog</label></div></div>
                <div class="col-12"><button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button></div>
            </div>
        </form>
    </div>
</div>
@endsection
