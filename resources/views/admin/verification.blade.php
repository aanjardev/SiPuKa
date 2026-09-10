@extends('layouts.auth')

@section('title', 'Verifikasi Email | Dinoyo Kamera')

@section('content')
    <div class="auth-logo">
        <i class="fa-solid fa-envelope-circle-check"></i>
    </div>

    <div class="text-center mb-4">
        <h3 class="fw-bold mb-2">Verifikasi Email</h3>
        <p class="text-muted mb-0">Masukkan kode verifikasi yang telah dikirim ke {{ session('activation_email') }}</p>
    </div>

    @if(session('expiry'))
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-clock me-2"></i>
            <div>
                <small><strong>{{ session('expiry') }}</strong></small>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('activation.verify-code') }}" id="verificationForm">
        @csrf

        <div class="mb-4">
            <label for="verification_code" class="form-label fw-medium text-secondary small">
                Kode Verifikasi <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="text"
                    class="form-control verification-code-input required-field @error('verification_code') is-invalid @enderror"
                    id="verification_code"
                    name="verification_code"
                    maxlength="6"
                    placeholder="000000"
                    required
                    autofocus
                    data-error-message="Kode verifikasi wajib diisi">
            </div>
            <div class="invalid-feedback">
                @error('verification_code')
                    {{ $message }}
                @else
                    Kode verifikasi wajib diisi (6 digit)
                @enderror
            </div>
            <div class="form-text small text-muted mt-2">
                Masukkan 6 digit kode verifikasi yang dikirim ke email Anda.
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-check-circle me-2"></i> Verifikasi Kode
            </button>
        </div>

        <div class="text-center">
            <form method="POST" action="{{ route('activation.resend-code') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-primary text-decoration-none p-0 small">
                    <i class="fa-solid fa-redo me-1"></i> Kirim ulang kode
                </button>
            </form>
            <br>
            <small class="text-muted">
                <a href="{{ route('activation.form') }}" class="text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </small>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const verificationForm = document.getElementById('verificationForm');
            const codeInput = document.getElementById('verification_code');

            if (verificationForm && window.FormValidator) {
                FormValidator.initForm(verificationForm);
            }

            if (codeInput) {
                codeInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 6) {
                        value = value.slice(0, 6);
                    }
                    e.target.value = value;
                });
            }
        });
    </script>
@endpush
