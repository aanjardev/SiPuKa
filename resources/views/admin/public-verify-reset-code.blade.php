@extends('layouts.auth')

@section('title', 'Verifikasi Kode | Dinoyo Kamera')

@section('content')
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-2">Verifikasi Kode</h2>
        <p class="text-muted small">Masukkan kode 6 digit yang dikirim ke email Anda.</p>
        @if(session('reset_email'))
            <p class="text-muted small">
                <i class="fa-solid fa-envelope me-1"></i>
                {{ session('reset_email') }}
            </p>
        @endif
    </div>

    <form method="POST" action="{{ route('public.verify-reset-code.post') }}" id="verifyForm">
        @csrf

        <div class="mb-4">
            <label for="verification_code" class="form-label text-center d-block">Kode Verifikasi <span class="text-danger">*</span></label>
            <div class="code-inputs">
                <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required autofocus>
                <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
                <input type="text" class="form-control code-input" maxlength="1" pattern="[0-9]" required>
            </div>
            <input type="hidden" name="verification_code" id="verification_code" required>
            <div class="invalid-feedback">
                Kode verifikasi wajib diisi dengan 6 digit angka
            </div>
        </div>

        <div class="timer text-center mb-3" id="timer">
            <i class="fa-solid fa-clock me-1"></i>
            Kode berlaku selama <span id="countdown">30:00</span>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-check me-2"></i> Verifikasi Kode
            </button>
        </div>

        <div class="text-center">
            <p class="text-muted small mb-0">
                Tidak menerima kode?
                <button type="button" class="btn btn-link p-0 text-primary" id="resendBtn" disabled>
                    Kirim Ulang
                </button>
            </p>
        </div>

    </form>

    <div class="mt-4 text-center">
        <div class="border-top pt-3">
            <p class="text-muted small mb-0">
                <a href="{{ route('password.request') }}" class="text-primary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const codeInputs = document.querySelectorAll('.code-input');
            const hiddenInput = document.getElementById('verification_code');
            const verifyForm = document.getElementById('verifyForm');
            const resendBtn = document.getElementById('resendBtn');
            const timerElement = document.getElementById('countdown');

            codeInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;

                    if (!/^\d$/.test(value)) {
                        e.target.value = '';
                        return;
                    }

                    e.target.classList.add('filled');

                    if (value && index < codeInputs.length - 1) {
                        codeInputs[index + 1].focus();
                    }

                    updateHiddenInput();
                });
                
                input.addEventListener('keydown', function(e) {

                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        codeInputs[index - 1].focus();
                        codeInputs[index - 1].value = '';
                        codeInputs[index - 1].classList.remove('filled');
                        updateHiddenInput();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                    
                    for (let i = 0; i < pastedData.length; i++) {
                        if (codeInputs[index + i]) {
                            codeInputs[index + i].value = pastedData[i];
                            codeInputs[index + i].classList.add('filled');
                        }
                    }

                    const nextEmpty = Array.from(codeInputs).find(inp => !inp.value);
                    if (nextEmpty) {
                        nextEmpty.focus();
                    } else {
                        codeInputs[codeInputs.length - 1].focus();
                    }
                    
                    updateHiddenInput();
                });
            });
            
            function updateHiddenInput() {
                const code = Array.from(codeInputs).map(input => input.value).join('');
                hiddenInput.value = code;
            }

            let timeLeft = 30 * 60; // 30 minutes in seconds
            
            function updateTimer() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 60) {
                    document.getElementById('timer').classList.add('warning');
                }
                
                if (timeLeft <= 0) {
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Kirim Ulang Kode';
                    document.getElementById('timer').innerHTML = '<i class="fa-solid fa-exclamation-triangle me-1"></i> Kode telah kadaluarsa';
                    return;
                }
                
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
            
            updateTimer();

            resendBtn.addEventListener('click', function() {
                if (!this.disabled) {
                    window.location.href = "{{ route('public.resend-reset-code') }}";
                }
            });

            if (verifyForm && window.FormValidator) {
                FormValidator.initForm(verifyForm);
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
