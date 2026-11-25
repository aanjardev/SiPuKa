export default class ItemForm {
    constructor(fieldMap) {
        this.fieldMap = fieldMap;
        Object.keys(fieldMap).forEach(key => {
            const el = this.getEl(key);
            el?.addEventListener("input", () => {
                el.setCustomValidity("");
                el.classList.remove("is-invalid");
                el.removeAttribute("aria-invalid");
            });
        });
    }

    getEl(key) {
        return this.fieldMap[key] ? document.getElementById(this.fieldMap[key]) : null;
    }

    collect() {
        const data = {};
        Object.keys(this.fieldMap).forEach(key => {
            const el = this.getEl(key);
            data[key] = el ? el.value : "";
        });
        return data;
    }

    populate(data = {}) {
        Object.keys(this.fieldMap).forEach(key => {
            const el = this.getEl(key);
            if (el) {
                el.value = data[key] ?? "";
                el.setCustomValidity("");
                el.classList.remove("is-invalid");
                el.removeAttribute("aria-invalid");
            }
        });
    }

    clear() {
        this.populate({});
    }

    validate() {
        const errors = {};
        Object.keys(this.fieldMap).forEach(key => {
            const el = this.getEl(key);
            if (!el) return;
            el.setCustomValidity("");
            el.classList.remove("is-invalid");
            el.removeAttribute("aria-invalid");
            const label = el.parentElement.querySelector("label")?.textContent.replace("*", "").trim() || key;
            if (el.required && !el.value.trim()) {
                errors[key] = [el.dataset.errorMessage || `${label} wajib diisi.`];
            } else if (el.maxLength >= 0 && Array.from(el.value).length > el.maxLength) {
                errors[key] = [`${label} maksimal ${el.maxLength} karakter.`];
            } else if (!el.validity.valid) {
                errors[key] = [`${label} tidak valid.`];
            }
        });
        return !this.showErrors(errors);
    }

    showErrors(errors = {}) {
        let first;
        Object.entries(errors).forEach(([key, messages]) => {
            const el = this.getEl(key);
            if (!el) return;
            const message = [].concat(messages).join("\n");
            el.setCustomValidity(message);
            el.classList.add("is-invalid");
            el.setAttribute("aria-invalid", "true");
            const feedback = el.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.textContent = message;
            first ||= el;
        });
        if (!first) return false;
        const reveal = () => {
            first.scrollIntoView({ behavior: "smooth", block: "center" });
            first.focus();
            first.reportValidity();
        };
        const section = first.closest(".accordion-collapse");
        if (section && !section.classList.contains("show")) {
            section.addEventListener("shown.bs.collapse", reveal, { once: true });
            bootstrap.Collapse.getOrCreateInstance(section, { toggle: false }).show();
        } else {
            reveal();
        }
        return true;
    }
}
