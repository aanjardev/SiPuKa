export default class CustomerModal {
    constructor({ onSuccess, storeUrl = "/admin/customers" }) {
        this.btn = document.getElementById("btnSimpanCustomer");
        this.form = document.getElementById("formTambahCustomer");
        this.modalEl = document.getElementById("modalTambahCustomer");
        this.modal = this.modalEl ? new bootstrap.Modal(this.modalEl) : null;
        this.onSuccess = onSuccess;
        this.storeUrl = storeUrl;
        this.requiredIds = [
            "customer_nama_modal",
            "customer_no_telp_modal",
            "customer_jenis_kelamin_modal",
        ];

        this.phoneInputs = [];
        this.init();
    }

    init() {
        if (!this.btn || !this.form) return;

        if (this.modalEl) {
            this.modalEl.addEventListener("shown.bs.modal", () => {
                const firstInput = document.getElementById(this.requiredIds[0]);
                if (firstInput) {
                    setTimeout(() => {
                        firstInput.focus();
                    }, 100);
                }
            });
        }

        this.requiredIds.forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            const eventType = el.tagName === "SELECT" ? "change" : "input";
            el.addEventListener(eventType, () => {
                if (el.value) {
                    el.classList.remove("is-invalid");
                }
            });
        });

        this.initPhoneValidation();

        this.btn.addEventListener("click", (e) => {
            e.preventDefault();
            this.save();
        });
    }

    async save() {
        if (!this.form || !this.btn) return;

        const invalid = [];
        this.requiredIds.forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            const isEmpty = !el.value;

            if (isEmpty) {
                if (window.FormValidator) {
                    FormValidator.setInvalid(el);
                } else {
                    el.classList.add("is-invalid");
                }
                invalid.push(id);
            } else {
                if (window.FormValidator) {
                    FormValidator.clearValidation(el);
                } else {
                    el.classList.remove("is-invalid");
                }
            }
        });

        if (invalid.length) {
            const firstInvalid = document.getElementById(invalid[0]);
            firstInvalid?.focus();
            this.showAlert(
                "Lengkapi semua kolom bertanda * sebelum menyimpan customer.",
                "warning"
            );
            return;
        }

        this.sanitizePhoneInputs();
        const data = Object.fromEntries(new FormData(this.form).entries());

        this.btn.disabled = true;
        this.btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Menyimpan...`;

        let result;
        let responseOk = false;
        let handled = false;

        try {
            const res = await fetch(this.storeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(data)
            });
            responseOk = res.ok;
            result = await res.json();
        } catch (error) {
            this.showAlert("Terjadi kesalahan jaringan. Silakan coba lagi.");
            handled = true;
        }

        if (!handled && responseOk && result?.success) {
            if (this.onSuccess) this.onSuccess(result.customer);

            this.form.reset();
            this.requiredIds.forEach((id) => {
                document.getElementById(id)?.classList.remove("is-invalid");
            });
            this.hideModalSafely();
            this.showAlert("Customer berhasil disimpan.", "success");
        } else if (!handled && result?.errors) {
            const messages = Object.values(result.errors)
                .flat()
                .filter(Boolean);
            const message = messages.length ? messages[0] : "Gagal menyimpan customer.";
            this.showAlert(message, "error");
        } else if (!handled && result?.message) {
            this.showAlert(result.message, "error");
        } else if (!handled) {
            this.showAlert("Gagal menyimpan customer. Silakan cek data Anda.", "error");
        }

        this.btn.disabled = false;
        this.btn.innerHTML = `Simpan`;
    }

    initPhoneValidation() {
        if (!this.form) return;
        this.phoneInputs = Array.from(
            this.form.querySelectorAll("[data-phone-validation]")
        );

        this.phoneInputs.forEach((input) => {
            const maxDigits = this.getMaxDigits(input);
            const formatted = this.formatPhone(
                (input.value || "").replace(/\D/g, "").slice(0, maxDigits)
            );
            input.value = formatted;

            input.addEventListener("input", () => {
                let digits = input.value.replace(/\D/g, "");
                if (digits.length > maxDigits) {
                    digits = digits.slice(0, maxDigits);
                }
                input.value = this.formatPhone(digits);
            });

            input.addEventListener("blur", () => {
                const digits = input.value.replace(/\D/g, "");
                if (!digits) {
                    input.classList.add("is-invalid");
                    const feedback = input.nextElementSibling;
                    if (
                        feedback &&
                        feedback.classList.contains("invalid-feedback")
                    ) {
                        feedback.textContent =
                            feedback.dataset.defaultMessage ||
                            "Nomor telepon wajib diisi.";
                    }
                    return;
                }

                const regex = /^(?:0|62)[0-9]{8,15}$/;
                if (!regex.test(digits)) {
                    input.classList.add("is-invalid");
                    const feedback = input.nextElementSibling;
                    if (
                        feedback &&
                        feedback.classList.contains("invalid-feedback")
                    ) {
                        feedback.textContent =
                            "Nomor telepon harus diawali 0 atau 62 dan hanya berisi angka.";
                    }
                    return;
                }

                input.classList.remove("is-invalid");
                const feedback = input.nextElementSibling;
                if (
                    feedback &&
                    feedback.classList.contains("invalid-feedback")
                ) {
                    feedback.textContent =
                        feedback.dataset.defaultMessage ||
                        "Nomor telepon wajib diisi.";
                }
            });
        });
    }

    sanitizePhoneInputs() {
        if (!this.phoneInputs.length) return;
        this.phoneInputs.forEach((input) => {
            let digits = input.value.replace(/\D/g, "");
            const maxDigits = this.getMaxDigits(input);
            digits = digits.slice(0, maxDigits);
            input.value = digits;
        });
    }

    getMaxDigits(input) {
        const max = parseInt(input.dataset.maxDigits || "20", 10);
        return Number.isFinite(max) && max > 0 ? max : 20;
    }

    formatPhone(digits = "") {
        if (!digits) return "";
        const part1 = digits.slice(0, 4);
        const part2 = digits.slice(4, 8);
        const rest = digits.slice(8);
        let formatted = part1;
        if (part2) formatted += "-" + part2;
        if (rest) formatted += "-" + rest;
        return formatted;
    }

    hideModalSafely() {
        if (!this.modalEl) return;
        const instance =
            bootstrap.Modal.getInstance(this.modalEl) ||
            this.modal ||
            new bootstrap.Modal(this.modalEl);
        instance?.hide();
        setTimeout(() => {
            document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.removeProperty("overflow");
            document.body.style.removeProperty("padding-right");
        }, 350);
    }

    showAlert(message, type = "error") {
        const uiTypeMap = {
            success: "success",
            warning: "warning",
            info: "info",
            error: "danger"
        };

        const titleMap = {
            success: "Berhasil",
            warning: "Perhatian",
            info: "Informasi",
            error: "Terjadi Kesalahan"
        };

        if (window.UiAlert?.push) {
            window.UiAlert.push({
                type: uiTypeMap[type] || "info",
                title: titleMap[type] || "Informasi",
                message,
                autoDismiss: type !== "error"
            });
            return;
        }

        const fallbackMap = {
            success: window.showSuccess,
            warning: window.showWarning,
            info: window.showInfo,
            error: window.showError
        };

        if (typeof fallbackMap[type] === "function") {
            fallbackMap[type](message);
            return;
        }

        if (typeof fallbackMap.error === "function") {
            fallbackMap.error(message);
            return;
        }

        alert(message);
    }
}
