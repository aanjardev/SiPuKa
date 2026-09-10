@extends('layouts.auth')

@section('title', 'Reset Password | SiDiKa')

@push('styles')
    <style>
        .progress {
            height: 6px;
        }

        .progress-bar {
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .progress-bar.bg-danger { background-color: #dc3545 !important; }
        .progress-bar.bg-warning { background-color: #ffc107 !important; }
        .progress-bar.bg-info { background-color: #17a2b8 !important; }
        .progress-bar.bg-success { background-color: #28a745 !important; }

        .form-control.is-valid {
            border-color: #198754;
            background-color: #f0fff4;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }
    </style>
@endpush

@section('content')
    <div class="auth-logo">
        <i class="fas fa-unlock-alt"></i>
    </div>

    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">Buat Password Baru</h2>
        <p class="text-muted small">Verifikasi berhasil! Silakan buat password baru</p>
    </div>

    <form method="POST" action="{{ route('admin.profile.reset-forgotten-password.post') }}" id="resetForm" class="needs-validation" novalidate>
        @csrf

        <div class="mb-3">
            <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-key"></i>
                </span>
                <input type="password"
                       class="form-control"
                       id="new_password"
                       name="new_password"
                       placeholder="Masukkan password baru"
                       required
                       minlength="6"
                       autocomplete="new-password" autofocus>
                <button class="btn btn-toggle-password" type="button" id="toggleNewPassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="form-text text-muted">
                Minimal 6 karakter
            </div>
            <div class="invalid-feedback">
                Password minimal 6 karakter
            </div>
        </div>

        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-key"></i>
                </span>
                <input type="password"
                       class="form-control"
                       id="new_password_confirmation"
                       name="new_password_confirmation"
                       placeholder="Ulangi password baru"
                       required
                       minlength="6"
                       autocomplete="new-password">
                <button class="btn btn-toggle-password" type="button" id="toggleConfirmPassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="invalid-feedback">
                Konfirmasi password harus sama
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-muted">Kekuatan Password:</span>
                <span id="strengthText" class="small fw-bold">-</span>
            </div>
            <div class="progress">
                <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Simpan Password Baru
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <div class="border-top pt-3">
            <a href="{{ route('admin.profile') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali ke Profil
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const newPasswordInput = document.getElementById('new_password');
            const newIcon = toggleNewPassword.querySelector('i');
            
            toggleNewPassword.addEventListener('click', function() {
                const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPasswordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    newIcon.classList.remove('fa-eye-slash');
                    newIcon.classList.add('fa-eye');
                } else {
                    newIcon.classList.remove('fa-eye');
                    newIcon.classList.add('fa-eye-slash');
                }
            });

            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');
            const confirmIcon = toggleConfirmPassword.querySelector('i');
            
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    confirmIcon.classList.remove('fa-eye-slash');
                    confirmIcon.classList.add('fa-eye');
                } else {
                    confirmIcon.classList.remove('fa-eye');
                    confirmIcon.classList.add('fa-eye-slash');
                }
            });

            function checkPasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 6) strength++;
                if (password.length >= 10) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;
                
                const strengthBar = document.getElementById('strengthBar');
                const strengthText = document.getElementById('strengthText');

                strengthBar.className = 'progress-bar';
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthBar.style.width = '20%';
                        strengthBar.classList.add('bg-danger');
                        strengthText.textContent = 'Sangat Lemah';
                        strengthText.className = 'small fw-bold text-danger';
                        break;
                    case 2:
                        strengthBar.style.width = '40%';
                        strengthBar.classList.add('bg-warning');
                        strengthText.textContent = 'Lemah';
                        strengthText.className = 'small fw-bold text-warning';
                        break;
                    case 3:
                        strengthBar.style.width = '60%';
                        strengthBar.classList.add('bg-info');
                        strengthText.textContent = 'Sedang';
                        strengthText.className = 'small fw-bold text-info';
                        break;
                    case 4:
                        strengthBar.style.width = '80%';
                        strengthBar.classList.add('bg-success');
                        strengthText.textContent = 'Kuat';
                        strengthText.className = 'small fw-bold text-success';
                        break;
                    case 5:
                        strengthBar.style.width = '100%';
                        strengthBar.classList.add('bg-success');
                        strengthText.textContent = 'Sangat Kuat';
                        strengthText.className = 'small fw-bold text-success';
                        break;
                }
            }

            newPasswordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
            });

            confirmPasswordInput.addEventListener('input', function() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = this.value;
                
                if (confirmPassword === newPassword && confirmPassword.length > 0) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else if (confirmPassword.length > 0) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });

            const resetForm = document.querySelector('form');
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
