@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@push('page-actions')
    <a href="{{ route('admin.permissions') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
    <button type="submit" form="permissionsForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-save"></i>
        <span>{{ isset($user) ? 'Simpan Perubahan' : 'Simpan User Baru' }}</span>
    </button>
@endpush

@section('content')

{{-- Route action seharusnya ke permissions.store/update, yang sudah benar di kedua branch. --}}
<form id="permissionsForm" action="{{ isset($user) ? route('admin.permissions.update', $user->id) : route('admin.permissions.store') }}" method="POST" data-validate-form>
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Informasi Utama --}}
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-user-shield me-2 text-primary"></i>
                        {{ isset($user) ? 'Informasi Akun Pengguna' : 'Identitas Pengguna Baru' }}
                    </h6>
                    <p class="text-muted small mt-1">Hubungkan akun dengan data karyawan dan tentukan email login.</p>
                </div>

                <div class="card-body p-4">
                    {{-- Nama Karyawan --}}
                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary small">Nama Karyawan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-user-tie"></i>
                            </span>
                            <select name="karyawan_name" class="form-select border-start-0 ps-2 required-field" style="height: 45px;" required data-error-message="Karyawan wajib dipilih" autofocus {{ isset($user) ? 'disabled' : '' }}>
                                <option selected disabled value="">-- Pilih Karyawan --</option>
                                @foreach ($karyawan_data as $k)
                                    <option value="{{ $k->id }}"
                                        {{ (old('karyawan_name') == $k->id || (isset($user) && $user->id == $k->id)) ? 'selected' : '' }}>
                                        {{ $k->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            @error('karyawan_name')
                                {{ $message }}
                            @else
                                Karyawan wajib dipilih
                            @enderror
                        </div>
                    </div>
                    @if(isset($user))
                        <input type="hidden" name="karyawan_name" value="{{ $user->id }}">
                        {{-- <div class="form-text small text-muted mt-1">
                            <i class="fa-solid fa-circle-info me-1"></i>Nama karyawan terkunci pada mode edit.
                        </div> --}}
                    @endif

                    {{-- Role atau Jabatan (Ditambahkan oleh teman Anda) --}}
                    <div class="mb-3">
                        <label for="role" class="form-label fw-medium text-secondary small">Role atau Jabatan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-user-tag"></i> {{-- Mengganti ikon ke yang lebih sesuai --}}
                            </span>
                            {{-- Menambahkan required-field dan old value --}}
                            <select name="role" id="role" class="form-select border-start-0 ps-2 required-field @error('role') is-invalid @enderror" style="height: 45px;" required data-error-message="Role wajib dipilih">
                                <option selected disabled value="">-- Pilih Role --</option>
                                {{-- Role options: manager dan staff --}}
                                <option value="manager" {{ (old('role') == 'manager' || (isset($user) && $user->role == 'manager')) ? 'selected' : '' }}>
                                    Manager
                                </option>
                                <option value="staff" {{ (old('role') == 'staff' || (isset($user) && $user->role == 'staff')) ? 'selected' : '' }}>
                                    Staff
                                </option>
                            </select>
                        </div>
                        <div class="invalid-feedback">
                            @error('role')
                                {{ $message }}
                            @else
                                Role wajib dipilih
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-secondary small">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            {{-- Menggabungkan input type email dan validasi dari input-pembelian --}}
                            <input type="email" class="form-control border-start-0 ps-2 required-field @error('email') is-invalid @enderror"
                                id="email" name="email"
                                style="height: 45px;"
                                placeholder="contoh@email.com"
                                value="{{ old('email', isset($user) ? $user->email : '') }}"
                                required
                                data-error-message="Email wajib diisi"
                                data-validate="email"> {{-- Email dapat diedit pada mode edit --}}
                        </div>
                        <div class="invalid-feedback">
                            @error('email')
                                {{ $message }}
                            @else
                                Email wajib diisi dengan format yang benar
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Keamanan & Aksi --}}
        @if(!isset($user))
        <div class="col-12">

            {{-- Card Keamanan --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-lock me-2 text-warning"></i>Keamanan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="form-text small text-muted mb-3">
                        <i class="fa-solid fa-info-circle me-1 text-primary"></i>
                        User akan dibuat dengan status <strong>Pending</strong>. Karyawan perlu mengaktifkan akun melalui halaman aktivasi yang akan dikirim ke email.
                    </div>

                    {{-- Edukasi Token Expiry --}}
                    <div class="alert alert-warning mb-0" style="font-size: 0.85rem;">
                        <div class="d-flex align-items-start">
                            <i class="fa-solid fa-clock me-2 mt-1"></i>
                            <div>
                                <strong>⏰ Penting!</strong><br>
                                <ul class="mb-0 mt-2 ps-3" style="font-size: 0.9rem;">
                                    <li>Token aktivasi berlaku <strong>3 hari (72 jam)</strong></li>
                                    <li>Karyawan harus segera aktivasi sebelum kadaluarsa</li>
                                    <li>Jika token kadaluarsa, manager bisa generate ulang token</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endif
    </div>
</form>

@endsection

@push('scripts')
<!-- Script toggle password dihapus karena tidak lagi diperlukan -->
@endpush
