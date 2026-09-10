@extends('layouts.auth')

@section('title', 'Setup Password | Dinoyo Kamera')

@section('content')
    <div class="auth-logo">
        <i class="fa-solid fa-lock"></i>
    </div>

    <div class="text-center mb-4">
        <h3 class="fw-bold mb-2">Buat Password</h3>
        <p class="text-muted mb-0">Buat password untuk akun <strong>{{ $user->email }}</strong></p>
    </div>

    <form method="POST" action="{{ route('activation.setup-password', $user->activation_token) }}" id="setupForm">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label fw-medium text-secondary small">
                Password <span class="text-danger">*</span>
            </label>
            <div class="input-group mb-2">
                <span class="input-group-text">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password"
                    class="form-control required-field @error('password') is-invalid @enderror"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                    autofocus
                    data-error-message="Password wajib diisi"
                    minlength="6">
                <button class="btn btn-toggle-password" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback">
                @error('password')
                    {{ $message }}
                @else
                    Password wajib diisi minimal 6 karakter
                @enderror
            </div>
            <div class="password-strength" id="passwordStrength"></div>
            <div class="form-text small text-muted mt-2">
                Password minimal 6 karakter. Gunakan kombinasi huruf dan angka untuk keamanan.
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-medium text-secondary small">
                Konfirmasi Password <span class="text-danger">*</span>
            </label>
            <div class="input-group mb-2">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password"
                    class="form-control required-field @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Ulangi password"
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
                    Konfirmasi password harus sama dengan password
                @enderror
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-check-circle me-2"></i> Aktifkan Akun & Login
            </button>
        </div>

        <div class="alert alert-info small">
            <i class="fa-solid fa-info-circle me-2"></i>
            Setelah aktivasi, Anda akan langsung login ke sistem.
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const setupForm = document.getElementById('setupForm');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');

            if (setupForm && window.FormValidator) {
                FormValidator.initForm(setupForm);
            }

            function setupTogglePassword(toggleBtn, inputField) {
                if (toggleBtn && inputField) {
                    toggleBtn.addEventListener('click', function() {
                        const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                        inputField.setAttribute('type', type);

                        const icon = toggleBtn.querySelector('i');
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });
                }
            }

            setupTogglePassword(togglePassword, passwordInput);
            setupTogglePassword(togglePasswordConfirm, passwordConfirmInput);

            if (passwordInput && passwordStrength) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    if (password.length >= 6) strength++;
                    if (password.length >= 10) strength++;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^a-zA-Z0-9]/.test(password)) strength++;

                    passwordStrength.className = 'password-strength';

                    if (password.length === 0) {
                        passwordStrength.style.display = 'none';
                    } else {
                        passwordStrength.style.display = 'block';
                        if (strength <= 2) {
                            passwordStrength.classList.add('strength-weak');
                        } else if (strength <= 4) {
                            passwordStrength.classList.add('strength-medium');
                        } else {
                            passwordStrength.classList.add('strength-strong');
                        }
                    }
                });
            }

            if (passwordConfirmInput && passwordInput) {
                passwordConfirmInput.addEventListener('input', function() {
                    if (this.value !== passwordInput.value) {
                        this.setCustomValidity('Password tidak sama');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
@endpush
