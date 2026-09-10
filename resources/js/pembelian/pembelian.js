import { maskRupiah } from "../utils/rupiah.js";
import CustomerSearch from "../utils/customer-search.js";
import CustomerModal from "../utils/modal-customer.js";
import CustomerEditModal from "../utils/modal-customer-edit.js";
import ItemForm from "./item-form.js";
import ItemTable from "./item-table.js";
import ItemAPI from "./item-api.js";
import DealButtons from "./deal-buttons.js";

function parseJSONFromScript(id) {
    try {
        const el = document.getElementById(id);
        if (!el) return {};
        return JSON.parse(el.textContent || "{}");
    } catch (e) {
        console.error("Gagal parse JSON dari", id, e);
        return {};
    }
}

class PurchaseDraftController {
    constructor({ data, elements, routes }) {

        this.currentPembelianId =
            data.currentPembelianId ||
            elements.hiddenPembelianIdInput?.value ||
            "";
        this.items = Array.isArray(data.initialItems) ? data.initialItems : [];
        this.kategoriMap = data.kategoriMap || {};
        this.routes = routes;

        this.el = elements;
        this.editingItemId = null;
        this.isDirty = false;

        this.itemForm = new ItemForm({
            nama_item: "item_nama_item",
            kategori_id: "item_kategori_id",
            serial_number: "item_serial_number",
            serial_lens: "item_serial_lens",
            kondisi_fisik: "item_kondisi_fisik",
            kondisi_baut: "item_kondisi_baut",
            kondisi_tutup_usb: "item_kondisi_tutup_usb",
            kondisi_grip: "item_kondisi_grip",
            kondisi_jamur_lensa: "item_kondisi_jamur_lensa",
            kondisi_jamur_sensor: "item_kondisi_jamur_sensor",
            kondisi_af_lensa: "item_kondisi_af_lensa",
            kondisi_diafragma_lensa: "item_kondisi_diafragma_lensa",
            kondisi_zoom_lensa: "item_kondisi_zoom_lensa",
            kondisi_kalibrasi_fokus: "item_kondisi_kalibrasi_fokus",
            kondisi_mounting: "item_kondisi_mounting",
            kondisi_slot_memori: "item_kondisi_slot_memori",
            kondisi_lcd: "item_kondisi_lcd",
            kondisi_tombol: "item_kondisi_tombol",
            kondisi_flash: "item_kondisi_flash",
            kondisi_sound_mic: "item_kondisi_sound_mic",
            kondisi_view_finder: "item_kondisi_view_finder",
            kondisi_lain_lain: "item_kondisi_lain_lain",
            kelengkapan: "item_kelengkapan",
        });

        this.itemTable = new ItemTable({
            wrapper: this.el.itemListWrapper,
            kategoriMap: this.kategoriMap,
            onEdit: (id) => this.handleEdit(id),
            onDelete: (id) => this.handleDelete(id),
        });

        this.itemAPI = new ItemAPI({
            routes: this.routes,
            csrf: this.routes.csrf,
        });

        this.dealButtons = new DealButtons({
            btnDraft: this.el.btnDraft,
            btnNoDeal: this.el.btnNoDeal,
            btnDeal: this.el.btnDeal,
            hiddenHargaDeal: this.el.hiddenHargaDeal,
        });

        this.bootstrapModal = this.el.modalTambahItemEl
            ? new bootstrap.Modal(this.el.modalTambahItemEl)
            : null;

        this.init();
    }

    markDirty() {
        this.isDirty = true;
    }

    init() {
        if (this.currentPembelianId && this.el.hiddenPembelianIdInput) {
            this.el.hiddenPembelianIdInput.value = this.currentPembelianId;
        }

        this.setItemModalMode("add");

        if (this.el.modalTambahItemEl) {
            this.el.modalTambahItemEl.addEventListener("shown.bs.modal", () => {
                this.el.itemNamaInput?.focus();
            });
            this.el.modalTambahItemEl.addEventListener(
                "hidden.bs.modal",
                () => {
                    this.prepareNewItemForm();
                }
            );
        }

        if (this.el.cabangSelect) {
            this.el.cabangSelect.addEventListener("change", () => {
                this.el.cabangSelect.classList.remove("is-invalid");
            });
        }

        this.el.btnBukaModalItem?.addEventListener("click", () =>
            this.handleOpenItemModal()
        );

        this.el.btnSimpanItem?.addEventListener("click", () =>
            this.handleSaveItem()
        );

        this.renderItemList();
        this.dealButtons.sync(this.items.length);
    }

    setItemModalMode(mode = "add") {
        const isEdit = mode === "edit";
        if (this.el.modalTambahItemTitleText) {
            this.el.modalTambahItemTitleText.textContent = isEdit
                ? "Edit Item Pembelian"
                : "Tambah Item Pembelian";
        }
        if (this.el.btnSimpanItem) {
            this.el.btnSimpanItem.innerHTML = isEdit
                ? '<i class="fas fa-save me-1"></i> Update Item'
                : '<i class="fas fa-save me-1"></i> Simpan Item';
        }
    }

    prepareNewItemForm() {
        this.editingItemId = null;
        this.itemForm.clear();
        this.setItemModalMode("add");
    }

    handleOpenItemModal() {
        let hasError = false;

        if (!this.el.customerIdInput.value) {
            this.showCustomerSelectionError();
            this.el.customerSearchInput?.focus();
            hasError = true;
        }

        if (!this.el.cabangSelect?.value) {
            this.el.cabangSelect?.classList.add("is-invalid");
            if (!hasError && this.el.cabangSelect) this.el.cabangSelect.focus();
            hasError = true;
        }

        if (hasError || !this.bootstrapModal) return;
        this.prepareNewItemForm();
        this.bootstrapModal.show();
    }

    showCustomerSelectionError(message) {
        const msg = message || "Customer wajib dipilih sebelum menambah item.";
        if (!this.el.customerSearchInput) return;
        this.el.customerSearchInput.classList.add("is-invalid");
        if (this.el.customerSearchError) {
            this.el.customerSearchError.textContent = msg;
            this.el.customerSearchError.style.display = "block";
        }
    }

    clearCustomerSelectionError() {
        if (!this.el.customerSearchInput) return;
        this.el.customerSearchInput.classList.remove("is-invalid");
        if (this.el.customerSearchError) {
            this.el.customerSearchError.style.display = "none";
        }
    }

    renderItemList() {
        this.itemTable.render(this.items);
        this.dealButtons.sync(this.items.length);
    }

    handleEdit(id) {
        const item = this.items.find((it) => it.id === id);
        if (!item || !this.bootstrapModal) return;

        this.editingItemId = id;
        this.itemForm.populate(item);
        this.setItemModalMode("edit");
        this.bootstrapModal.show();
    }

    async handleDelete(id) {
        if (!confirm("Yakin ingin menghapus item ini?")) return;

        try {
            const result = await this.itemAPI.delete(id);
            if (!result.success)
                throw new Error(result.message || "Gagal menghapus");

            this.items = this.items.filter((it) => it.id !== id);
            this.renderItemList();
            this.markDirty();
        } catch (err) {
            console.error(err);
            alert(err.message || "Gagal menghapus item.");
        }
    }




    async handleSaveItem() {
        const formValues = this.itemForm.collect();
        const namaItem = (formValues.nama_item || "").trim();
        const kategoriId = formValues.kategori_id;

        const namaInput = document.getElementById("item_nama_item");
        const kategoriSelect = document.getElementById("item_kategori_id");

        let hasError = false;

        if (!namaItem) {
            if (window.FormValidator) {
                FormValidator.setInvalid(namaInput, "Nama item wajib diisi");
            } else {
                namaInput?.classList.add("is-invalid");
            }
            hasError = true;
        } else {
            if (window.FormValidator) {
                FormValidator.clearValidation(namaInput);
            } else {
                namaInput?.classList.remove("is-invalid");
            }
        }

        if (!kategoriId) {
            if (window.FormValidator) {
                FormValidator.setInvalid(
                    kategoriSelect,
                    "Kategori wajib dipilih"
                );
            } else {
                kategoriSelect?.classList.add("is-invalid");
            }
            hasError = true;
        } else {
            if (window.FormValidator) {
                FormValidator.clearValidation(kategoriSelect);
            } else {
                kategoriSelect?.classList.remove("is-invalid");
            }
        }

        if (hasError) {

            const firstError = document.querySelector(
                "#modalTambahItem .is-invalid"
            );
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                firstError.focus();
            }
            return;
        }

        formValues.nama_item = namaItem;

        const pembelianId =
            this.currentPembelianId ||
            this.el.hiddenPembelianIdInput?.value ||
            "";

        const isEditing = Boolean(this.editingItemId);
        const payload = isEditing
            ? formValues
            : {
                  pembelian_id: pembelianId,
                  customer_id: this.el.customerIdInput.value,
                  perusahaan_cabang_id: this.el.cabangSelect.value,
                  user_id: this.el.userIdInput?.value || "",
                  ...formValues,
                  kelengkapan_awal: formValues.kelengkapan,
              };

        if (!this.el.btnSimpanItem) return;
        this.el.btnSimpanItem.disabled = true;
        this.el.btnSimpanItem.innerHTML = isEditing
            ? '<i class="fas fa-spinner fa-spin"></i> Mengupdate...'
            : '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const result = isEditing
                ? await this.itemAPI.update(this.editingItemId, payload)
                : await this.itemAPI.create(payload);

            if (!result.success) {
                throw new Error(result.message || "Gagal menyimpan data.");
            }

            if (isEditing) {
                this.items = this.items.map((it) =>
                    it.id === this.editingItemId ? result.item : it
                );
            } else {
                this.currentPembelianId = result.pembelian_id;
                if (this.el.hiddenPembelianIdInput) {
                    this.el.hiddenPembelianIdInput.value = result.pembelian_id;
                }
                this.items.push(result.item);
            }

            this.renderItemList();
            this.bootstrapModal?.hide();
            this.prepareNewItemForm();
            this.markDirty();
        } catch (err) {
            console.error(err);
            alert(err.message || "Gagal menyimpan data.");
        } finally {
            this.el.btnSimpanItem.disabled = false;
            this.setItemModalMode(isEditing ? "edit" : "add");
        }
    }
}



document.addEventListener("DOMContentLoaded", () => {
    const mainForm = document.getElementById("formPembelian");
    if (!mainForm) return;

    const data = parseJSONFromScript("pembelian-data") || {};
    const csrf =
        document.querySelector('meta[name="csrf-token"]')?.content || "";

    const routes = {
        storeItemDraft: data.routes?.storeItemDraft,
        updateItemDraft: data.routes?.updateItemDraftPrefix,
        deleteItemDraft: data.routes?.deleteItemDraftPrefix,
        customerSearch: data.routes?.customerSearch,
        customerStore: data.routes?.customerStore,
        customerShow: data.routes?.customerShow,
        customerUpdate: data.routes?.customerUpdate,
        csrf,
    };

    if (!data.currentPembelianId) {
        setTimeout(() => {
            document.getElementById("customer_search")?.focus();
        }, 120);
    }

    const customerSearchInput = document.getElementById("customer_search");
    const customerIdInput = document.getElementById("customer_id");
    const customerSuggestions = document.getElementById("customer_suggestions");
    const customerSearchError = document.getElementById(
        "customer_search_error"
    );
    const customerEditBtn = document.getElementById("btnEditCustomer");

    let controller;

    const updateEditButtonState = () => {
        if (!customerEditBtn) return;
        const hasCustomer = Boolean(customerIdInput?.value);
        customerEditBtn.classList.toggle("d-none", !hasCustomer);
        customerEditBtn.disabled = !hasCustomer;
        if (hasCustomer) {
            customerEditBtn.dataset.customerId = customerIdInput.value;
        } else {
            customerEditBtn.removeAttribute("data-customer-id");
        }
    };

    new CustomerSearch({
        input: customerSearchInput,
        hiddenInput: customerIdInput,
        suggestions: customerSuggestions,
        searchUrl: routes.customerSearch,
        onSelect: () => {
            if (customerSearchInput) {
                customerSearchInput.classList.remove("is-invalid");
            }
            if (customerSearchError) {
                customerSearchError.style.display = "none";
            }
            updateEditButtonState();
            controller?.markDirty();
        },
        onInput: () => {
            if (customerSearchInput) {
                customerSearchInput.classList.remove("is-invalid");
            }
            if (customerSearchError) {
                customerSearchError.style.display = "none";
            }
            updateEditButtonState();
            controller?.markDirty();
        },
    });

    controller = new PurchaseDraftController({
        data,
        routes,
        elements: {
            btnBukaModalItem: document.getElementById("btnBukaModalItem"),
            btnSimpanItem: document.getElementById("btnSimpanItem"),
            itemListWrapper: document.getElementById("item-list-wrapper"),
            modalTambahItemEl: document.getElementById("modalTambahItem"),
            modalTambahItemTitleText: document
                .getElementById("modalTambahItemTitle")
                ?.querySelector(".modal-title-text"),
            hiddenPembelianIdInput: document.getElementById(
                "pembelian_id_hidden"
            ),
            customerIdInput,
            customerSearchInput,
            customerSearchError,
            cabangSelect: document.getElementById("perusahaan_cabang_id"),
            itemNamaInput: document.getElementById("item_nama_item"),
            userIdInput: mainForm.querySelector('input[name="user_id"]'),
            hiddenHargaDeal: document.getElementById("harga_deal"),
            btnDraft: document.getElementById("btnDraft"),
            btnNoDeal: document.getElementById("btnNoDeal"),
            btnDeal: document.getElementById("btnDeal"),
        },
    });

    document.querySelectorAll(".rupiah-mask").forEach((input) => {

        const clean = maskRupiah(input);
        const hiddenId = input.id.replace("display_", "");
        const hidden = document.getElementById(hiddenId);
        if (hidden) hidden.value = clean;

        const isDealInput = input.id === "display_harga_deal";

        input.addEventListener("keyup", () => {
            const cleanVal = maskRupiah(input);
            const hidden = document.getElementById(hiddenId);
            if (hidden) hidden.value = cleanVal;

            if (isDealInput) {
                controller.dealButtons.sync(controller.items.length);
            }
            controller.markDirty();
        });
    });

    new CustomerModal({
        storeUrl: routes.customerStore || "/admin/customers",
        onSuccess: (customer) => {
            if (customerSearchInput && customerIdInput) {
                customerSearchInput.value = `${customer.nama} (${customer.no_telp})`;
                customerIdInput.value = customer.id;
            }
            if (customerSuggestions) customerSuggestions.style.display = "none";
            updateEditButtonState();
            controller.markDirty();
        },
    });

    if (customerEditBtn) {
        customerEditBtn.dataset.fetchUrlTemplate =
            routes.customerShow || "";
        customerEditBtn.dataset.updateUrlTemplate =
            routes.customerUpdate || "";
    }

    new CustomerEditModal({
        button: customerEditBtn,
        customerIdInput,
        onSuccess: (customer) => {
            if (customerSearchInput && customerIdInput) {
                customerSearchInput.value = `${customer.nama} (${customer.no_telp})`;
                customerIdInput.value = customer.id;
            }
            if (customerSuggestions) customerSuggestions.style.display = "none";
            updateEditButtonState();
            controller.markDirty();
        },
    });

    updateEditButtonState();
});
