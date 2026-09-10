import { formatRupiah } from "../utils/rupiah.js";

export default class Checkout {
    constructor({ itemsInput, tableBody, products }) {
        this.itemsInput = itemsInput;
        this.tableBody = tableBody;
        this.products = this.mapProducts(products);
        this.cartItems = this.loadItems();
    }

    mapProducts(products) {
        const map = {};
        products.forEach(p => map[p.id] = p);
        return map;
    }

    loadItems() {
        try {
            return JSON.parse(this.itemsInput.value || "[]") || [];
        } catch {
            return [];
        }
    }

    save() {
        this.itemsInput.value = JSON.stringify(this.cartItems);
    }

    findIndex(id) {
        return this.cartItems.findIndex(i => String(i.id) === String(id));
    }

    getLimitQty(productId) {
        const product = this.products[productId];
        const stock = product?.stok_produk;

        if (stock === null || stock === undefined) return Infinity;

        const parsed = Number(stock);
        return Number.isFinite(parsed) ? Math.max(parsed, 0) : Infinity;
    }

    add(productId, qty = 1) {
        if (!this.products[productId]) return;

        const limit = this.getLimitQty(productId);
        if (limit === 0) return;

        const idx = this.findIndex(productId);
        const currentQty = idx === -1 ? 0 : this.cartItems[idx].qty;
        const nextQty = limit === Infinity ? currentQty + qty : Math.min(currentQty + qty, limit);

        if (idx === -1) this.cartItems.push({ id: Number(productId), qty: nextQty });
        else this.cartItems[idx].qty = nextQty;

        this.save();
        this.render();
    }

    update(productId, delta) {
        const idx = this.findIndex(productId);
        if (idx === -1) return;

        const next = this.cartItems[idx].qty + delta;

        if (next < 1) this.cartItems.splice(idx, 1);
        else {
            const limit = this.getLimitQty(productId);
            this.cartItems[idx].qty = limit === Infinity ? next : Math.min(next, limit);
        }

        this.save();
        this.render();
    }

    render() {
        if (!this.cartItems.length) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="fa-solid fa-cart-plus fa-2x mb-2 opacity-50"></i>
                        <p class="small">Belum ada item yang dipilih.</p>
                    </td>
                </tr>`;
            return;
        }

        this.tableBody.innerHTML = this.cartItems.map(item => {
            const product = this.products[item.id];
            const price = Number(product.harga_jual || 0);
            const total = item.qty * price;
            const limit = this.getLimitQty(item.id);
            const atLimit = Number.isFinite(limit) && item.qty >= limit;

            return `
                <tr data-product-id="${item.id}">
                    <td class="ps-3">
                        <div class="d-flex align-items-center">
                            ${product.image_url ? `
                                <img src="${product.image_url}" class="rounded-3 shadow-sm me-2" style="width:45px;height:45px;object-fit:cover;">` : ""}
                            <div>
                                <div class="fw-semibold text-dark">${product.nama_produk}</div>
                                <small class="text-muted font-monospace">${product.kode_sku ?? "-"}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="input-group input-group-sm qty-control justify-content-center" data-product-id="${item.id}" style="width:100px;margin:auto;">
                            <button class="btn btn-light border btn-qty-dec" data-product-id="${item.id}">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="input-group-text bg-white border-start-0 border-end-0 fw-bold qty-value" style="min-width:30px;">${item.qty}</span>
                            <button class="btn btn-light border btn-qty-inc" data-product-id="${item.id}" ${atLimit ? "disabled" : ""}>
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-end text-muted small">${formatRupiah(price)}</td>
                    <td class="text-end pe-3 fw-medium text-dark">${formatRupiah(total)}</td>
                </tr>`;
        }).join("");
    }
}
