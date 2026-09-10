export default class ItemTable {
    constructor({ wrapper, kategoriMap, onEdit, onDelete }) {
        this.wrapper = wrapper;
        this.kategoriMap = kategoriMap;
        this.onEdit = onEdit;
        this.onDelete = onDelete;
    }

    render(items) {
        this.wrapper.innerHTML = "";

        if (!items.length) {
            this.wrapper.innerHTML = `
                <tr><td colspan="4" class="text-center py-5">
                    <div class="opacity-50">
                        <div class="bg-light rounded-circle p-3 mb-3 text-secondary">
                            <i class="fa-solid fa-box-open fa-2x"></i>
                        </div>
                        <h6 class="text-secondary fw-bold mb-1">Keranjang Kosong</h6>
                        <p class="small text-muted mb-0">Belum ada item ditambahkan.</p>
                    </div>
                </td></tr>`;
            return;
        }

        items.forEach(item => {
            const tr = document.createElement("tr");
            const summary = item.kondisi_fisik && item.serial_number
                ? `${item.kondisi_fisik} (SN: ${item.serial_number} - SNL: ${item.serial_lens})`
                : item.kondisi_fisik || item.serial_number || "-";

            const kategoriNama =
                item.kategori?.nama_kategori ||
                this.kategoriMap[item.kategori_id] ||
                "-";

            tr.innerHTML = `
                <td class="ps-4 py-3">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark">${item.nama_item}</span>
                        <span class="small text-muted">
                            <i class="fa-solid fa-circle-info me-1 text-info" style="font-size: 0.7rem;"></i>${summary}
                        </span>
                    </div>
                </td>

                <td class="py-3">
                    <span class="badge rounded-pill bg-white text-dark border border-secondary-subtle fw-normal px-3 py-2">${kategoriNama}</span>
                </td>

                <td class="text-center py-3">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn-action-icon" data-edit="${item.id}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button type="button" class="btn-action-icon" data-delete="${item.id}">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            `;

            this.wrapper.appendChild(tr);
        });

        this.bindEvents();
    }

    bindEvents() {
        this.wrapper.querySelectorAll("[data-edit]").forEach(btn => {
            btn.addEventListener("click", () =>
                this.onEdit(Number(btn.dataset.edit))
            );
        });

        this.wrapper.querySelectorAll("[data-delete]").forEach(btn => {
            btn.addEventListener("click", () =>
                this.onDelete(Number(btn.dataset.delete))
            );
        });
    }
}
