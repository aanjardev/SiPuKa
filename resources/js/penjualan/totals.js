import { formatRupiah, cleanNumber } from "../utils/rupiah.js";

export default class Totals {
    constructor({ checkout, elements }) {
        this.checkout = checkout;
        this.el = elements;
    }

    getSubtotal() {
        return this.checkout.cartItems.reduce((sum, item) => {
            const product = this.checkout.products[item.id];
            return sum + item.qty * (Number(product.harga_jual) || 0);
        }, 0);
    }

    recalc() {
        const subtotal = this.getSubtotal();
        const diskon = cleanNumber(this.el.diskon.value);
        const depresiasi = cleanNumber(this.el.depresiasi.value);
        const biaya = cleanNumber(this.el.biaya.value);

        const total = Math.max(0, subtotal - diskon + biaya);

        this.el.subtotal.textContent = formatRupiah(subtotal);
        this.el.total.textContent = formatRupiah(total);

        this.toggleRow(this.el.diskonRow, diskon > 0, "-" + formatRupiah(diskon));


        this.toggleRow(this.el.biayaRow, biaya > 0, formatRupiah(biaya));

        this.el.submit.disabled = this.checkout.cartItems.length === 0;
    }

    toggleRow(row, show, text) {
        if (!row) return;
        row.style.display = show ? "flex" : "none";
        const valueEl = row.querySelector(".value") || row.querySelector("span:last-child");
        if (valueEl) valueEl.textContent = text;
    }
}
