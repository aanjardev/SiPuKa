/**
 * UNIVERSAL PHONE INPUT HANDLER
 * Menggabungkan:
 * 1. Sanitizer numerik (input biasa)
 * 2. Display + Hidden Formatter (Cabang & Karyawan)
 */

(function () {
    'use strict';

    const MAX_DIGITS = 13;

    
    function formatPhone(digits) {
        if (!digits) return '';
        const p1 = digits.slice(0, 4);
        const p2 = digits.slice(4, 8);
        const rest = digits.slice(8);

        let out = p1;
        if (p2) out += '-' + p2;
        if (rest) out += '-' + rest;
        return out;
    }

    /* -------------------------------
     * Helper: Bersihkan angka & limit
     * ------------------------------- */
    function cleanDigits(value, max = MAX_DIGITS) {
        let digits = (value || '').replace(/\D/g, '');
        return digits.slice(0, max);
    }

    function getMaxDigits(input) {
        const max = parseInt(input?.dataset?.maxDigits || '', 10);
        return Number.isFinite(max) && max > 0 ? max : MAX_DIGITS;
    }

    
    function initBasicSanitizer() {
        const selectors =
            'input[data-phone-validation], input[data-numeric-only], input[name*="telepon"], input[name*="telp"], input[name="no_telp"], input[name="identitas"]';

        document.querySelectorAll(selectors).forEach(input => {
            const maxDigits = getMaxDigits(input);

            input.addEventListener('input', function () {
                this.value = cleanDigits(this.value, maxDigits);
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                this.value = cleanDigits(paste, maxDigits);
            });

            input.addEventListener('keypress', function (e) {
                const char = String.fromCharCode(e.which);
                const digits = cleanDigits(this.value, maxDigits);
                const selectionLength = Math.abs(this.selectionEnd - this.selectionStart);

                if (!/[0-9]/.test(char) && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    return;
                }

                if (digits.length >= maxDigits && selectionLength === 0 && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                }
            });
        });
    }

    
    function initFormattedPhonePair(displayId, hiddenId, options = {}) {
        const { required = true } = options;
        const displayInput = document.getElementById(displayId);
        const hiddenInput = document.getElementById(hiddenId);

        if (!displayInput || !hiddenInput) return;

        (function () {
            const raw = cleanDigits(hiddenInput.value, MAX_DIGITS);
            hiddenInput.value = raw;
            displayInput.value = formatPhone(raw);
        })();

        displayInput.addEventListener('input', function () {
            const digits = cleanDigits(this.value, MAX_DIGITS);
            hiddenInput.value = digits;
            displayInput.value = formatPhone(digits);
        });
        displayInput.addEventListener('keypress', function (e) {
            const char = String.fromCharCode(e.which);
            const digits = cleanDigits(displayInput.value, MAX_DIGITS);
            const selectionLength = Math.abs(displayInput.selectionEnd - displayInput.selectionStart);

            if (!/[0-9]/.test(char) && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                return;
            }

            if (digits.length >= MAX_DIGITS && selectionLength === 0 && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
            }
        });

        displayInput.addEventListener('blur', function () {
            const digits = hiddenInput.value;
            const formControl = displayInput;
            const feedback = formControl.closest('.input-group').nextElementSibling;

            formControl.classList.remove('is-invalid');

            if (!digits) {
                if (required) {
                    formControl.classList.add('is-invalid');
                    if (feedback?.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Nomor telepon wajib diisi.';
                    }
                    return;
                }
                return; // optional: allow empty
            }

            const lengthOk = digits.length >= 8 && digits.length <= MAX_DIGITS;
            const prefixOk = digits.startsWith('0') || digits.startsWith('62');
            if (!lengthOk || !prefixOk) {
                formControl.classList.add('is-invalid');
                if (feedback?.classList.contains('invalid-feedback')) {
                    feedback.textContent =
                        `Nomor telepon harus berupa angka, diawali 0/62, dan maksimal ${MAX_DIGITS} digit.`;
                }
            }
        });
    }

    
    document.addEventListener('DOMContentLoaded', function () {

        initBasicSanitizer();


        initFormattedPhonePair('nomor_telepon_display', 'nomor_telepon');

        initFormattedPhonePair('branch_nomor_telepon_display', 'branch_nomor_telepon');

        initFormattedPhonePair('contact_phone_display', 'contact_phone', { required: false });
    });
})();
