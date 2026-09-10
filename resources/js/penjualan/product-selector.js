export default class ProductSelector {
    constructor({ select, qtyInput, stockInfo, qtyWrapper, searchInput }) {
        this.select = select;
        this.qtyInput = qtyInput;
        this.stockInfo = stockInfo;
        this.qtyWrapper = qtyWrapper;
        this.searchInput = searchInput;
        this.maxQty = Infinity;

        this.bind();
    }

    bind() {
        this.select?.addEventListener("change", () => this.handleChange());
        this.bindQtyButtons();
        this.bindSearch();
    }

    bindSearch() {
        if (!this.searchInput || !this.select) return;
        this.searchInput.addEventListener("input", () => {
            const term = this.searchInput.value.toLowerCase();
            Array.from(this.select.options).forEach((opt, idx) => {
                if (idx === 0) return; // skip placeholder
                const text = (opt.textContent || "").toLowerCase();
                opt.hidden = term.length ? !text.includes(term) : false;
            });
        });
    }

    bindQtyButtons() {
        if (!this.qtyWrapper || !this.qtyInput) return;
        this.qtyWrapper.addEventListener("click", (e) => {
            const btn = e.target.closest("button[data-action]");
            if (!btn) return;

            const action = btn.dataset.action;
            const current = Number(this.qtyInput.value || 1);
            if (action === "inc") {
                this.setQty(current + 1);
            } else if (action === "dec") {
                this.setQty(current - 1);
            }
        });
    }

    setQty(value) {
        const min = 1;
        const max = this.maxQty ?? Infinity;
        const next = Math.min(Math.max(value, min), max);
        if (this.qtyInput) {
            this.qtyInput.min = String(min);
            if (Number.isFinite(max)) this.qtyInput.max = String(max);
            else this.qtyInput.removeAttribute("max");
        }
        this.qtyInput.value = next;
        this.updateQtyButtons();
    }

    updateQtyButtons() {
        const incBtn = this.qtyWrapper?.querySelector("button[data-action='inc']");
        const decBtn = this.qtyWrapper?.querySelector("button[data-action='dec']");
        if (!incBtn || !decBtn || !this.qtyInput) return;

        const val = Number(this.qtyInput.value || 1);
        const max = this.maxQty ?? Infinity;

        decBtn.disabled = val <= 1;
        incBtn.disabled = val >= max && Number.isFinite(max);
    }

    handleChange() {
        const option = this.select.options[this.select.selectedIndex];
        const stockRaw = option?.dataset?.stock;
        const stock = Number(stockRaw);
        const hasStockLimit = Number.isFinite(stock);

        this.maxQty = hasStockLimit ? Math.max(stock, 0) : Infinity;

        if (this.qtyWrapper) this.qtyWrapper.classList.remove("d-none");
        this.setQty(1);

        if (!this.stockInfo) return;

        if (hasStockLimit) {
            this.stockInfo.innerHTML = `
                <i class="fa-solid fa-circle-info me-1"></i>
                Stok tersedia: <strong>${stock}</strong>`;
            this.stockInfo.classList.remove("d-none");
        } else {
            this.stockInfo.classList.add("d-none");
            this.stockInfo.textContent = "";
        }
    }
}
