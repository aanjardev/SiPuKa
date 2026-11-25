export default class ItemAPI {
    constructor({ routes, csrf }) {
        this.routes = routes;
        this.csrf = csrf;
    }

    async create(payload) {
        return this._send(this.routes.storeItemDraft, "POST", payload);
    }

    async update(id, payload) {
        return this._send(`${this.routes.updateItemDraft}/${id}`, "PUT", payload);
    }

    async delete(id) {
        return this._send(`${this.routes.deleteItemDraft}/${id}`, "DELETE");
    }

    async _send(url, method, body = null) {
        const res = await fetch(url, {
            method,
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": this.csrf
            },
            body: body ? JSON.stringify(body) : null
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            const messages = Object.values(data.errors || {}).flat();
            const error = new Error(messages.join("\n") || data.message || `Gagal memproses permintaan (${res.status}). Silakan coba lagi.`);
            error.errors = data.errors || {};
            throw error;
        }

        return res.json();
    }
}
