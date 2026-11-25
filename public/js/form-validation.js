/**
 * Form Validation Module
 * Provides consistent validation feedback across all forms
 *
 * Usage:
 * 1. Add class "required-field" to required inputs
 * 2. Add data-error-message="Custom message" for custom error messages
 * 3. Add data-validate="type" for special validation (email, phone, number, etc.)
 * 4. Place <div class="invalid-feedback">Error message</div> after input or input-group
 * 5. Call FormValidator.init() or use data-validate-form on the form
 */

window.FormValidator = (function () {
    "use strict";
    let feedbackId = 0;
    const initializedForms = new WeakSet();

    const defaultMessages = {
        required: "Field ini wajib diisi",
        email: "Format email tidak valid",
        phone: "Format nomor telepon tidak valid",
        number: "Harus berupa angka",
        min: "Nilai minimal adalah {min}",
        max: "Nilai maksimal adalah {max}",
        minlength: "Minimal {minlength} karakter",
        maxlength: "Maksimal {maxlength} karakter",
        select: "Silakan pilih salah satu opsi",
        customer: "Customer wajib dipilih",
        price: "Harga harus berupa angka lebih dari 0",
    };

    /**
     * Set field as invalid
     */
    function setInvalid(field, message) {
        field.setAttribute("aria-invalid", "true");
        field.classList.add("is-invalid");
        field.classList.remove("is-valid");

        const inputGroup = field.closest(".input-group");
        if (inputGroup) {
            inputGroup.classList.add("has-validation-error");
        }

        let feedback = findFeedbackElement(field);
        if (!feedback) {
            feedback = document.createElement("div");
            feedback.className = "invalid-feedback";
            (inputGroup || field).insertAdjacentElement("afterend", feedback);
        }
        if (!feedback.id) feedback.id = `validation-error-${++feedbackId}`;
        const describedBy = new Set((field.getAttribute("aria-describedby") || "").split(/\s+/).filter(Boolean));
        describedBy.add(feedback.id);
        field.setAttribute("aria-describedby", [...describedBy].join(" "));
        if (feedback) {
            feedback.textContent = message || getDefaultMessage(field);
            feedback.classList.add("d-block");
        }

        field.classList.add("shake-error");
        setTimeout(() => field.classList.remove("shake-error"), 500);
    }

    /**
     * Set field as valid
     */
    function setValid(field) {
        field.removeAttribute("aria-invalid");
        field.classList.remove("is-invalid");



        const inputGroup = field.closest(".input-group");
        if (inputGroup) {
            inputGroup.classList.remove("has-validation-error");
        }

        let feedback = findFeedbackElement(field);
        if (feedback) {
            feedback.classList.remove("d-block");
        }
    }

    /**
     * Clear validation state
     */
    function clearValidation(field) {
        field.removeAttribute("aria-invalid");
        field.classList.remove("is-invalid", "is-valid");

        const inputGroup = field.closest(".input-group");
        if (inputGroup) {
            inputGroup.classList.remove("has-validation-error");
        }

        let feedback = findFeedbackElement(field);
        if (feedback) {
            feedback.classList.remove("d-block");
        }
    }

    /**
     * Find the feedback element for a field
     */
    function findFeedbackElement(field) {

        let feedback = field.nextElementSibling;
        while (feedback && !feedback.classList.contains("invalid-feedback")) {
            feedback = feedback.nextElementSibling;
        }

        if (!feedback) {
            const inputGroup = field.closest(".input-group");
            if (inputGroup) {
                feedback = inputGroup.nextElementSibling;
                while (
                    feedback &&
                    !feedback.classList.contains("invalid-feedback")
                ) {
                    feedback = feedback.nextElementSibling;
                }
            }
        }

        if (!feedback && field.id) {
            feedback = document.getElementById(field.id + "_error");
        }

        if (!feedback && field.name) {
            feedback = document.getElementById(field.name + "_error");
        }

        return feedback;
    }

    /**
     * Get default error message for a field
     */
    function getDefaultMessage(field) {

        if (field.dataset.errorMessage) {
            return field.dataset.errorMessage;
        }

        const validateType = field.dataset.validate;

        if (validateType && defaultMessages[validateType]) {
            let msg = defaultMessages[validateType];

            if (field.min) msg = msg.replace("{min}", field.min);
            if (field.max) msg = msg.replace("{max}", field.max);
            if (field.minLength)
                msg = msg.replace("{minlength}", field.minLength);
            if (field.maxLength)
                msg = msg.replace("{maxlength}", field.maxLength);
            return msg;
        }

        if (field.tagName === "SELECT") {
            return defaultMessages.select;
        }

        return defaultMessages.required;
    }

    /**
     * Validate a single field
     */
    function validateField(field) {
        if (field.disabled || field.readOnly || field.type === "hidden") return true;
        const maskedAmount = field.classList.contains("rupiah-mask");
        const maskedPhone = field.hasAttribute("data-phone-validation");
        const value = maskedAmount ? field.value.trim().replace(/\./g, "") : maskedPhone ? field.value.trim().replace(/-/g, "") : field.value.trim();
        const validateType = field.dataset.validate || (maskedAmount ? "amount" : "");

        const validity = field.validity || {};
        if (validity.badInput) {
            setInvalid(field, defaultMessages.number);
            return false;
        }
        if (validity.customError) {
            setInvalid(field, field.validationMessage);
            return false;
        }

        if (
            !field.classList.contains("required-field") &&
            !field.hasAttribute("required") &&
            !value && !validity.valueMissing
        ) {
            clearValidation(field);
            return true;
        }

        if (
            (field.classList.contains("required-field") ||
                field.hasAttribute("required")) &&
            (!value || validity.valueMissing)
        ) {
            setInvalid(field, field.dataset.errorMessage || defaultMessages.required);
            return false;
        }

        if (maskedPhone && value && (!/^\d+$/.test(value) || value.length > Number(field.dataset.maxDigits || 20))) {
            setInvalid(field, `Nomor telepon hanya boleh berisi angka, maksimal ${field.dataset.maxDigits || 20} digit.`);
            return false;
        }
        if (validity.typeMismatch || (validity.patternMismatch && !maskedPhone) || validity.stepMismatch || validity.rangeUnderflow || validity.rangeOverflow) {
            const message = validity.typeMismatch
                ? (field.type === "email" ? defaultMessages.email : "Format URL tidak valid")
                : validity.patternMismatch ? (field.dataset.patternMessage || "Format isian tidak sesuai")
                : validity.stepMismatch ? `Nilai harus sesuai kelipatan ${field.step || 1}`
                : validity.rangeUnderflow ? defaultMessages.min.replace("{min}", field.min)
                : defaultMessages.max.replace("{max}", field.max);
            setInvalid(field, message);
            return false;
        }
        const after = field.dataset.afterField && field.form?.elements.namedItem(field.dataset.afterField);
        if (value && after?.value && value < after.value) {
            setInvalid(field, "Tanggal keluar tidak boleh sebelum tanggal masuk.");
            return false;
        }
        if (field.name?.endsWith("_confirmation")) {
            const original = field.form?.elements.namedItem(field.name.slice(0, -13));
            if (original && field.value !== original.value) {
                setInvalid(field, "Konfirmasi password tidak cocok.");
                return false;
            }
        }
        const lower = field.dataset.greaterThan && field.form?.elements.namedItem(field.dataset.greaterThan);
        if (value && lower?.value && Number(value) <= Number(lower.value)) {
            setInvalid(field, "Harga maksimum harus lebih besar dari harga minimum.");
            return false;
        }
        if (field.type === "file") {
            const accepted = (field.accept || "").split(",").map(type => type.trim().toLowerCase()).filter(Boolean);
            for (const file of field.files || []) {
                if (field.dataset.maxBytes && file.size > Number(field.dataset.maxBytes)) {
                    setInvalid(field, `Ukuran file maksimal ${Number(field.dataset.maxBytes) / 1024 / 1024} MB.`);
                    return false;
                }
                if (accepted.length && !accepted.some(type => type.startsWith(".") ? file.name.toLowerCase().endsWith(type) : type.endsWith("/*") ? file.type.startsWith(type.slice(0, -1)) : file.type === type)) {
                    setInvalid(field, `Format file harus sesuai: ${field.accept}.`);
                    return false;
                }
            }
        }

        if (
            field.tagName === "SELECT" &&
            (field.classList.contains("required-field") ||
                field.hasAttribute("required"))
        ) {
            if (
                !value ||
                value === "" ||
                (field.selectedIndex === 0 && field.options[0].disabled)
            ) {
                setInvalid(field, defaultMessages.select);
                return false;
            }
        }

        if (validateType && value) {
            switch (validateType) {
                case "email":
                    if (!isValidEmail(value)) {
                        setInvalid(field, defaultMessages.email);
                        return false;
                    }
                    break;
                case "phone":
                    if (!isValidPhone(value)) {
                        setInvalid(field, defaultMessages.phone);
                        return false;
                    }
                    break;
                case "number":
                    if (!Number.isFinite(Number(value))) {
                        setInvalid(field, defaultMessages.number);
                        return false;
                    }
                    break;
                case "amount":
                    if (!/^\d+$/.test(value)) {
                        setInvalid(field, "Nilai harus berupa bilangan bulat minimal 0.");
                        return false;
                    }
                    break;
                case "price":
                    const numValue = parseFloat(
                        value.replace(/\./g, "").replace(/,/g, "")
                    );
                    if (isNaN(numValue) || numValue <= 0) {
                        setInvalid(field, defaultMessages.price);
                        return false;
                    }
                    break;
            }
        }

        if (field.type !== "date" && field.type !== "time" && field.min && Number(value) < Number(field.min)) {
            setInvalid(field, defaultMessages.min.replace("{min}", field.min));
            return false;
        }

        if (field.type !== "date" && field.type !== "time" && field.max && Number(value) > Number(field.max)) {
            setInvalid(field, defaultMessages.max.replace("{max}", field.max));
            return false;
        }

        if (
            field.minLength &&
            field.minLength > 0 &&
            value.length < field.minLength
        ) {
            setInvalid(
                field,
                defaultMessages.minlength.replace(
                    "{minlength}",
                    field.minLength
                )
            );
            return false;
        }

        if (
            !maskedPhone && field.maxLength &&
            field.maxLength > 0 &&
            value.length > field.maxLength
        ) {
            setInvalid(
                field,
                defaultMessages.maxlength.replace(
                    "{maxlength}",
                    field.maxLength
                )
            );
            return false;
        }

        setValid(field);
        return true;
    }

    /**
     * Validate entire form
     */
    function validateForm(form, options = {}) {
        const fields = form.querySelectorAll("input, select, textarea");
        let isValid = true;
        let firstInvalid = null;

        fields.forEach((field) => {

            if (
                field.type === "hidden" ||
                field.disabled ||
                ["submit", "button", "reset"].includes(field.type) ||
                (!isVisible(field) && !field.closest(".accordion-collapse, .tab-pane"))
            ) {
                return;
            }

            if (!validateField(field)) {
                isValid = false;
                if (!firstInvalid) {
                    firstInvalid = field;
                }
            }
        });

        if (!isValid && firstInvalid && options.scrollToError !== false) {
            scrollToElement(firstInvalid);
        }

        return isValid;
    }

    /**
     * Check if element is visible
     */
    function isVisible(element) {
        return !!(
            element.offsetWidth ||
            element.offsetHeight ||
            element.getClientRects().length
        );
    }

    /**
     * Scroll to element smoothly
     */
    function scrollToElement(element) {
        const collapsed = element.closest(".accordion-collapse:not(.show)");
        if (collapsed && window.bootstrap?.Collapse) {
            collapsed.addEventListener("shown.bs.collapse", () => scrollToElement(element), { once: true });
            bootstrap.Collapse.getOrCreateInstance(collapsed, { toggle: false }).show();
            return;
        }
        const tab = element.closest(".tab-pane:not(.active)");
        if (tab && window.bootstrap?.Tab) {
            const toggle = [...document.querySelectorAll('[data-bs-toggle="tab"]')].find(el => el.getAttribute("data-bs-target") === `#${tab.id}` || el.getAttribute("href") === `#${tab.id}`);
            if (toggle) {
                toggle.addEventListener("shown.bs.tab", () => scrollToElement(element), { once: true });
                bootstrap.Tab.getOrCreateInstance(toggle).show();
                return;
            }
        }
        const rect = element.getBoundingClientRect();
        const isInViewport = rect.top >= 0 && rect.bottom <= window.innerHeight;

        if (!isInViewport) {
            element.scrollIntoView({ behavior: "smooth", block: "center" });
        }

        setTimeout(() => element.focus(), 300);
    }

    /**
     * Email validation
     */
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * Phone validation (Indonesian format)
     */
    function isValidPhone(phone) {
        const cleaned = phone.replace(/[\s\-\(\)]/g, "");

        const re = /^(\+?62|0)8[1-9][0-9]{7,11}$/;
        return re.test(cleaned);
    }

    /**
     * Initialize validation on a form
     */
    function initForm(form, options = {}) {
        if (!form || initializedForms.has(form)) return;
        initializedForms.add(form);

        form.setAttribute("novalidate", "true");

        const fields = form.querySelectorAll("input, select, textarea");

        let isNavigating = false;
        let navigationTimeout = null;

        const setNavigating = () => {
            isNavigating = true;

            if (navigationTimeout) clearTimeout(navigationTimeout);
            navigationTimeout = setTimeout(() => {
                isNavigating = false;
            }, 300);
        };

        const cancelButtons = form.querySelectorAll(
            'button[type="button"], a[href]'
        );
        cancelButtons.forEach((btn) => {
            btn.addEventListener("mousedown", setNavigating);
            btn.addEventListener("click", setNavigating);
        });

        document
            .querySelectorAll(
                '#btnKembali, a[href].btn, button.btn:not([type="submit"])'
            )
            .forEach((el) => {

                if (form.contains(el)) return;

                const isNavigationButton =
                    el.id === "btnKembali" ||
                    el.textContent.toLowerCase().includes("kembali") ||
                    el.textContent.toLowerCase().includes("batal") ||
                    (el.tagName === "A" && el.hasAttribute("href"));

                if (isNavigationButton) {
                    el.addEventListener("mousedown", setNavigating);
                    el.addEventListener("click", setNavigating);
                }
            });

        fields.forEach((field) => {

            if (field.type === "hidden") return;

            field.addEventListener("blur", function (e) {

                if (isNavigating) {
                    return;
                }

                const relatedTarget = e.relatedTarget;
                if (relatedTarget) {
                    const isCancelButton =
                        relatedTarget.matches(
                            'button[type="button"], a[href], button:not([type="submit"])'
                        ) ||
                        relatedTarget.id === "btnKembali" ||
                        relatedTarget.textContent
                            ?.toLowerCase()
                            .includes("kembali") ||
                        relatedTarget.textContent
                            ?.toLowerCase()
                            .includes("batal");
                    if (isCancelButton) {
                        return;
                    }
                }
                validateField(this);
            });

            field.addEventListener("input", function () {
                if (this.classList.contains("is-invalid")) {
                    clearValidation(this);
                }
            });

            if (field.tagName === "SELECT") {
                field.addEventListener("change", function () {
                    validateField(this);
                });
            }
        });

        // Form submit validation
        form.addEventListener("submit", function (e) {

            const submitBtn = e.submitter;
            if (submitBtn && submitBtn.dataset.skipValidation === "true") {
                return true;
            }

            if (!validateForm(this, options)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, true);
    }

    /**
     * Auto-initialize forms with data-validate-form attribute
     */
    function init() {
        document
            .querySelectorAll("form[data-validate-form]")
            .forEach((form) => {
                initForm(form);
            });
    }

    /**
     * Reset form validation state
     */
    function resetForm(form) {
        const fields = form.querySelectorAll(".is-invalid, .is-valid");
        fields.forEach((field) => clearValidation(field));
    }

    function showErrors(form, errors) {
        let first;
        Object.entries(errors).forEach(([name, messages]) => {
            const field = form.elements.namedItem(name);
            if (!field?.classList || field.type === "hidden") return;
            setInvalid(field, [].concat(messages).join("\n"));
            first ||= field;
        });
        if (first) scrollToElement(first);
    }

    return {
        init: init,
        initForm: initForm,
        validateForm: validateForm,
        validateField: validateField,
        setInvalid: setInvalid,
        setValid: setValid,
        clearValidation: clearValidation,
        resetForm: resetForm,
        showErrors: showErrors,
        isValidEmail: isValidEmail,
        isValidPhone: isValidPhone,
    };
})();

document.addEventListener("DOMContentLoaded", function () {
    FormValidator.init();
});
