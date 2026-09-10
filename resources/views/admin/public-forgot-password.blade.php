@extends('layouts.auth')

@section('title', 'Lupa Password | Dinoyo Kamera')

@section('content')
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">Lupa Password</h2>
        <p class="text-muted small">Masukkan email untuk reset password Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email"
                    class="form-control required-field @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
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

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane me-2"></i> Kirim Kode Verifikasi
            </button>
        </div>

    </form>

    <div class="mt-4 text-center">
        <div class="border-top pt-3">
            <p class="text-muted small mb-0">
                Ingat password Anda?
                <a href="{{ route('login') }}" class="text-primary">
                    Kembali ke Login
                </a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const forgotForm = document.getElementById('forgotForm');
            if (forgotForm && window.FormValidator) {
                FormValidator.initForm(forgotForm);
            }

            const authCard = document.querySelector('.auth-card');
            if (authCard) {
                authCard.style.animation = 'slideInUp 0.6s ease-out';
            }
        });

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
@endpush
