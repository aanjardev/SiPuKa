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
            throw new Error(`Request failed with status ${res.status}`);
        }

        return res.json();
    }
}
