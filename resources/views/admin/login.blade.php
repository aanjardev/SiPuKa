@extends('layouts.auth')

@section('title', 'Masuk Admin | PusatKamera.id')

@section('content')
    <div class="auth-heading mb-4">
        <span class="auth-eyebrow">AREA ADMIN</span>
        <h2>Selamat datang<span>.</span></h2>
        <p>Masuk untuk mengelola operasional PusatKamera.id.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Alamat email <span class="text-danger">*</span></label>
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
                    {!! $message !!}
                @else
                    Email wajib diisi dengan format yang benar
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata sandi <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password"
                    class="form-control required-field @error('password') is-invalid @enderror"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                    data-error-message="Password wajib diisi">
                <button class="btn btn-toggle-password" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback">
                @error('password')
                    {{ $message }}
                @else
                    Password wajib diisi
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center mb-4">
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="small text-primary">Lupa kata sandi?</a>
            @else
            <a href="{{ route('password.request') }}" class="small text-primary">Lupa kata sandi?</a>
            @endif
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                MASUK KE DASHBOARD <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </div>

    </form>

    <div class="mt-4 text-center">
        <div class="border-top pt-3">
            <p class="text-muted small mb-0">
                Belum mengaktifkan akun?
                <a href="{{ route('activation.form') }}" class="text-primary">
                    Aktivasi Akun
                </a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.querySelector("#togglePassword");
            const passwordInput = document.querySelector("#password");
            const icon = togglePassword.querySelector("i");

            togglePassword.addEventListener("click", function() {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);

                if (type === "password") {
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                } else {
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }
            });

            const loginForm = document.getElementById('loginForm');
            if (loginForm && window.FormValidator) {
                FormValidator.initForm(loginForm);
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
