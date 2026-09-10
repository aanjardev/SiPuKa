@extends('layouts.auth')

@section('title', 'Verifikasi Kode - SiDiKa')

@section('content')
    <div class="auth-logo">
        <i class="fas fa-shield-alt"></i>
    </div>

    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">Verifikasi Kode Reset</h2>
        <p class="text-muted small">Masukkan kode 6 digit yang dikirim ke email Anda</p>
    </div>

    <form method="POST" action="{{ route('admin.profile.verify-reset-code.post') }}" id="verifyForm" class="needs-validation" novalidate>
        @csrf

        <div class="mb-4">
            <label for="verification_code" class="form-label fw-medium">Kode Verifikasi <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-key"></i>
                </span>
                <input type="text"
                    class="form-control verification-code-input required-field"
                    id="verification_code"
                    name="verification_code"
                    placeholder="123456"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    required
                    autocomplete="off">
            </div>
            <div class="form-text text-muted">
                Masukkan 6 digit angka dari email Anda
            </div>
            <div class="invalid-feedback">
                Kode verifikasi harus 6 digit angka
            </div>
        </div>

        <div class="alert alert-info mb-4">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle me-2"></i>
                Informasi Kode
            </h6>
            <ul class="mb-0 small">
                <li>Kode berlaku selama 30 menit</li>
                <li>Periksa folder spam jika tidak menerima email</li>
                <li>Jangan bagikan kode kepada siapa pun</li>
            </ul>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check-circle me-2"></i>
                Verifikasi Kode
            </button>
        </div>
    </form>

    <div class="text-center mb-3">
        <p class="text-muted mb-2">
            <i class="fas fa-clock me-1"></i>
            Kode berlaku selama 30 menit
        </p>
        <p class="mb-0">
            Tidak menerima kode?
            <button type="button" class="btn btn-link p-0 text-primary text-decoration-none" id="resendBtn">
                <i class="fas fa-redo me-1"></i>Kirim Ulang Kode
            </button>
        </p>
    </div>

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
            const verificationInput = document.getElementById('verification_code');

            verificationInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.remove('is-valid');
                    if (this.value.length > 0) {
                        this.classList.add('is-invalid');
                    }
                }
            });

            verificationInput.addEventListener('paste', function(e) {
                e.preventDefault();
                let pastedData = e.clipboardData.getData('text');
                pastedData = pastedData.replace(/[^0-9]/g, '').substring(0, 6);
                this.value = pastedData;

                if (this.value.length === 6) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                }
            });

            const resendBtn = document.getElementById('resendBtn');
            resendBtn.addEventListener('click', function() {
                window.location.href = "{{ route('admin.profile.resend-reset-code') }}";
            });

            const verifyForm = document.getElementById('verifyForm');
            if (verifyForm && window.FormValidator) {
                FormValidator.initForm(verifyForm);
            }
        });
    </script>
@endpush
