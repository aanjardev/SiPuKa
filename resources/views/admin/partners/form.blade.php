@extends('layouts.admin')
@section('title', $partner->exists ? 'Edit Partner' : 'Tambah Partner')
@section('content')
<div class="content-wrapper">
    <h1 class="h3 mb-4">{{ $partner->exists ? 'Edit Partner' : 'Tambah Partner' }}</h1>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ $partner->exists ? route('admin.partners.update', $partner) : route('admin.partners.store') }}" enctype="multipart/form-data" data-prevent-double-submit>
            @csrf
            @unless($partner->exists)<input type="hidden" name="submission_token" value="{{ old('submission_token', (string) \Illuminate\Support\Str::uuid()) }}">@endunless
            @if($partner->exists) @method('PUT') @endif
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Partner</label>
                <input id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $partner->nama) }}" maxlength="120" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo Partner</label>
                @if($partner->exists)<div class="mb-2"><img src="{{ $partner->logo_url }}" alt="Logo {{ $partner->nama }}" width="150" height="90" style="object-fit:contain"></div>@endif
                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" class="form-control @error('logo') is-invalid @enderror" {{ $partner->exists ? '' : 'required' }}>
                <div class="form-text">PNG, JPG, atau WebP. Maksimal 2 MB dan 5000 x 5000 piksel. Gunakan logo berlatar transparan. Kosongkan saat edit untuk mempertahankan logo.</div>
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <input type="hidden" name="is_active" value="0">
            <div class="form-check mb-4">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $partner->is_active))>
                <label for="is_active" class="form-check-label">Tampilkan di beranda</label>
                @error('is_active')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Simpan Partner</button><a href="{{ route('admin.partners.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div>
@endsection
