@extends('layouts.auth')

@section('title', 'Reset Password | Dinoyo Kamera')

@section('content')
    <div class="text-center mb-4">
        @if(session('success'))
            <div class="success-icon">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        @endif
        <h2 class="fw-bold text-dark mb-2">Buat Password Baru</h2>
        <p class="text-muted small">Masukkan password baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('public.reset-password.post') }}" id="resetForm">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password"
                    class="form-control required-field @error('password') is-invalid @enderror"
                    id="password"
                    name="password"
                    placeholder="Masukkan password baru"
                    required
                    data-error-message="Password wajib diisi"
                    data-validate="password-strength" autofocus>
                <button class="btn btn-toggle-password" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="password-strength" id="passwordStrength"></div>
            <div class="password-strength-text small text-muted" id="passwordStrengthText"></div>
            <div class="invalid-feedback">
                @error('password')
                    {{ $message }}
                @else
                    Password minimal 6 karakter
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password"
                    class="form-control required-field @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Konfirmasi password baru"
                    required
                    data-error-message="Konfirmasi password wajib diisi">
                <button class="btn btn-toggle-password" type="button" id="togglePasswordConfirm">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback">
                @error('password_confirmation')
                    {{ $message }}
                @else
                    Konfirmasi password harus sama dengan password baru
                @enderror
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-key me-2"></i> Reset Password
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <div class="border-top pt-3">
            <p class="text-muted small mb-0">
                <a href="{{ route('login') }}" class="text-primary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Login
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

            const togglePasswordConfirm = document.querySelector("#togglePasswordConfirm");
            const passwordConfirmInput = document.querySelector("#password_confirmation");
            const iconConfirm = togglePasswordConfirm.querySelector("i");

            togglePasswordConfirm.addEventListener("click", function() {
                const type = passwordConfirmInput.getAttribute("type") === "password" ? "text" : "password";
                passwordConfirmInput.setAttribute("type", type);

                if (type === "password") {
                    iconConfirm.classList.remove("fa-eye-slash");
                    iconConfirm.classList.add("fa-eye");
                } else {
                    iconConfirm.classList.remove("fa-eye");
                    iconConfirm.classList.add("fa-eye-slash");
                }
            });

            const passwordStrength = document.getElementById('passwordStrength');
            const passwordStrengthText = document.getElementById('passwordStrengthText');

            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let strengthText = '';
                let strengthClass = '';

                if (password.length >= 6) strength++;
                if (password.length >= 10) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;

                if (strength <= 2) {
                    strengthClass = 'weak';
                    strengthText = 'Lemah';
                } else if (strength <= 4) {
                    strengthClass = 'medium';
                    strengthText = 'Sedang';
                } else {
                    strengthClass = 'strong';
                    strengthText = 'Kuat';
                }

                passwordStrength.className = 'password-strength ' + strengthClass;
                passwordStrengthText.textContent = 'Kekuatan Password: ' + strengthText;
                passwordStrengthText.className = 'password-strength-text small text-' + (strengthClass === 'weak' ? 'danger' : strengthClass === 'medium' ? 'warning' : 'success');
            });

            const resetForm = document.getElementById('resetForm');
            if (resetForm && window.FormValidator) {
                FormValidator.initForm(resetForm);
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
