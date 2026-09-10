@extends('layouts.auth')

@section('title', 'Aktivasi Akun | Dinoyo Kamera')

@section('content')
    <div class="auth-logo">
        <i class="fa-solid fa-user-plus"></i>
    </div>

    <div class="text-center mb-4">
        <h3 class="fw-bold mb-2">Aktivasi Akun</h3>
        <p class="text-muted mb-0">Masukkan email yang terdaftar untuk mengaktifkan akun Anda</p>
    </div>

    <form method="POST" action="{{ route('activation.verify-email') }}" id="activationForm">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label fw-medium text-secondary small">
                Email Address <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email"
                    class="form-control required-field @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') ?? session('email') }}"
                    placeholder="contoh@email.com"
                    required
                    autofocus
                    data-error-message="Email wajib diisi"
                    data-validate="email">
            </div>
            <div class="invalid-feedback">
                @error('email')
                    {{ $message }}
                @else
                    Email wajib diisi dengan format yang benar
                @enderror
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-paper-plane me-2"></i> Kirim Kode Verifikasi
            </button>
        </div>

        <div class="text-center">
            <small class="text-muted">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">
                    Login di sini
                </a>
            </small>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const activationForm = document.getElementById('activationForm');
            if (activationForm && window.FormValidator) {
                FormValidator.initForm(activationForm);
            }
        });
    </script>
@endpush
