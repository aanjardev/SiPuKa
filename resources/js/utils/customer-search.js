import debounce from "./debounce.js";

export default class CustomerSearch {
    constructor({
        input,
        hiddenInput,
        suggestions,
        searchUrl,
        onSelect,
        onInput,
        minLength = 3,
    }) {
        this.input = input;
        this.hiddenInput = hiddenInput;
        this.suggestions = suggestions;
        this.searchUrl = searchUrl;
        this.onSelect = onSelect;
        this.onInput = onInput;
        this.minLength = minLength;
        this.fetchDebounced = debounce((q) => this.fetch(q), 220);

        this.bindEvents();
    }

    formatPhone(s) {
        if (!s) return "";
        const d = (s || "").toString().replace(/\D/g, "");
        if (!d) return "";
        if (d.length <= 4) return d;
        if (d.length <= 8) return d.slice(0, 4) + "-" + d.slice(4);
        return d.slice(0, 4) + "-" + d.slice(4, 8) + "-" + d.slice(8);
    }

    bindEvents() {
        if (!this.input || !this.suggestions) return;

        this.input.addEventListener("input", () => {
            if (this.hiddenInput) this.hiddenInput.value = "";
            if (this.onInput) this.onInput();
            this.fetchDebounced(this.input.value);
        });

        document.addEventListener("click", (e) => {
            if (
                !e.target.closest("#customer_search") &&
                !e.target.closest("#customer_suggestions")
            ) {
                this.hide();
            }
        });

        this.suggestions.addEventListener("click", (e) => {
            const el = e.target.closest("[data-id]");
            if (!el) return;

            e.preventDefault();
            const data = {
                id: el.dataset.id,
                text: el.textContent.trim(),
            };

            this.input.value = data.text;
            if (this.hiddenInput) this.hiddenInput.value = data.id;
            this.hide();

            if (this.onSelect) this.onSelect(data);
        });
    }

    hide() {
        if (!this.suggestions) return;
        this.suggestions.style.display = "none";
    }

    normalizeItem(item) {
        if (!item) return null;
        if (typeof item.text !== "undefined")
            return { id: item.id, text: item.text };

        const name = item.nama || item.name || item.full_name || "";
        const phone = item.no_telp || item.phone || item.telepon || "";
        const formattedPhone = this.formatPhone(phone);
        let text = name || formattedPhone || phone || "";

        if (name && (formattedPhone || phone))
            text = `${name} (${formattedPhone || phone})`;

        return { id: item.id, text };
    }

    async fetch(query) {
        if (!this.searchUrl) return this.hide();
        if (!query || query.length < this.minLength) return this.hide();

        try {
            const res = await fetch(
                `${this.searchUrl}?q=${encodeURIComponent(query)}`
            );
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const json = await res.json();

            const items = (Array.isArray(json) ? json : [])
                .map((item) => this.normalizeItem(item))
                .filter(Boolean);

            this.render(items);
        } catch (err) {
            console.error("Customer search failed", err);
            this.hide();
        }
    }

    render(items) {
        if (!this.suggestions) return;
        if (!items.length) return this.hide();

        this.suggestions.innerHTML = "";
        items.forEach((it) => {
            const a = document.createElement("a");
            a.href = "#";
            a.className = "dropdown-item";
            a.dataset.id = it.id;
            a.textContent = it.text;
            this.suggestions.appendChild(a);
        });

        this.suggestions.style.display = "block";
    }
}
