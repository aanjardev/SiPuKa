document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("qcForm");


    let btnKembali = document.querySelector('a[href*="quality-control"]');
    if (!btnKembali) {
        const candidates = Array.from(document.querySelectorAll("a, button"));
        btnKembali =
            candidates.find((el) => {
                try {
                    const txt = (el.textContent || "").trim().toLowerCase();

                    const style = window.getComputedStyle(el);
                    const visible =
                        style &&
                        style.display !== "none" &&
                        style.visibility !== "hidden" &&
                        el.offsetParent !== null;
                    return visible && txt.includes("kembali");
                } catch (e) {
                    return false;
                }
            }) || null;
    }
    const smartActionBtn = document.getElementById("smartActionBtn");
    const smartActionText = document.getElementById("smartActionText");
    const smartActionIcon = document.getElementById("smartActionIcon");
    const smartActionHint = document.getElementById("smartActionHint");
    const statusQcSelect = form?.querySelector('[name="status_qc"]');
    let isFormDirty = false;

    const statusToAction = {
        menunggu_qc: {
            action: "draft",
            text: "Simpan Draft",
            iconClass: "fa-pen-to-square",
            btnClass: "btn-light border text-secondary",
            hint: "Menyimpan perubahan sebagai draft (status: Menunggu QC)",
            requiresValidation: false, // <-- Tambah property baru
        },
        lolos_qc: {
            action: "save",
            text: "Simpan & Loloskan",
            iconClass: "fa-check-circle",
            btnClass: "btn-success",
            hint: "Menyimpan dan menandai produk lolos QC",
            requiresValidation: true, // <-- Hanya ini yang perlu validasi
        },
        gagal_qc: {
            action: "archive",
            text: "Arsipkan Produk",
            iconClass: "fa-archive",
            btnClass: "btn-danger",
            hint: "Menyimpan dan mengarsipkan produk (Gagal QC)",
            requiresValidation: false, // <-- Tidak perlu validasi
        },
    };
    function updateSmartButton() {
        if (!statusQcSelect) return;
        const status = statusQcSelect.value;
        const config = statusToAction[status] || statusToAction["menunggu_qc"];

        smartActionText.textContent = config.text;
        smartActionIcon.className = `fa-solid ${config.iconClass} me-2`;
        smartActionBtn.className = `btn fw-medium py-2 ${config.btnClass}`;
        smartActionBtn.setAttribute("data-action", config.action);
        smartActionHint.textContent = config.hint;

        toggleRequiredFields(status === "lolos_qc");

        if (statusQcSelect) {
            statusQcSelect.classList.toggle(
                "status-highlight",
                status !== "menunggu_qc"
            );
        }
    }

    if (statusQcSelect) {
        statusQcSelect.addEventListener("change", updateSmartButton);

        updateSmartButton();
    }

    if (smartActionBtn) {
        smartActionBtn.addEventListener("click", function (e) {
            const action = this.getAttribute("data-action") || "save";

            let actionInput = form.querySelector('input[name="action"]');
            if (!actionInput) {
                actionInput = document.createElement("input");
                actionInput.type = "hidden";
                actionInput.name = "action";
                form.appendChild(actionInput);
            }
            actionInput.value = action;
        });
    }


    function toggleRequiredFields(isLolosQc) {
        const requiredFields = [
            "nama_item",
            "kategori_id",
            "kode_sku",
            "deskripsi_produk",
            "harga_jual",
        ];

        requiredFields.forEach((fieldName) => {
            const field = form?.querySelector(`[name="${fieldName}"]`);
            if (field) {
                if (isLolosQc) {
                    field.setAttribute("required", "required");
                    field.classList.add("required-field");
                } else {
                    field.removeAttribute("required");
                    field.classList.remove("required-field");
                }
            }
        });
    }

    function markFormAsDirty() {
        if (!isFormDirty) {
            isFormDirty = true;
        }
    }

    if (form) {

        form.querySelectorAll("input, select, textarea").forEach((field) => {
            // Skip hidden inputs dan submit buttons
            if (field.type === "hidden" || field.type === "submit") return;

            const isRequiredField = [
                "nama_item",
                "kategori_id",
                "kode_sku",
                "deskripsi_produk",
                "harga_jual",
            ].includes(field.name);

            if (isRequiredField) {
                field.addEventListener("blur", function () {

                    const currentStatus =
                        statusQcSelect?.value || "menunggu_qc";
                    if (currentStatus !== "lolos_qc") return;

                    const value = this.value.trim();

                    if (field.name === "harga_jual") {
                        const numericValue = value.replace(/\./g, "");
                        if (
                            !numericValue ||
                            !/^\d+$/.test(numericValue) ||
                            parseInt(numericValue) <= 0
                        ) {
                            if (window.FormValidator) {
                                FormValidator.setInvalid(
                                    this,
                                    "Harga Jual wajib diisi dan harus berupa angka lebih dari 0"
                                );
                            } else {
                                this.classList.add("is-invalid");
                            }
                        } else {
                            if (window.FormValidator) {
                                FormValidator.clearValidation(this);
                            } else {
                                this.classList.remove("is-invalid");
                            }
                        }
                    }

                    else if (!value) {
                        if (window.FormValidator) {
                            FormValidator.setInvalid(this);
                        } else {
                            this.classList.add("is-invalid");
                        }
                    } else {
                        if (window.FormValidator) {
                            FormValidator.clearValidation(this);
                        } else {
                            this.classList.remove("is-invalid");
                        }
                    }
                });

                field.addEventListener("input", function () {
                    if (this.classList.contains("is-invalid")) {
                        if (window.FormValidator) {
                            FormValidator.clearValidation(this);
                        } else {
                            this.classList.remove("is-invalid");
                        }
                    }
                });
            }
        });
    }




    // will NOT show this modal (browser limitation).
    const unsavedModalEl = document.getElementById("unsavedChangesModal");
    let unsavedModal = null;
    let pendingNavigation = null;
    if (unsavedModalEl && typeof bootstrap !== "undefined") {
        unsavedModal = new bootstrap.Modal(unsavedModalEl, {
            backdrop: "static",
            keyboard: false,
        });
    }

    function openUnsavedModal(href) {

        pendingNavigation = href && href !== "" ? href : null;
        if (unsavedModal) {
            unsavedModal.show();
        } else {


            if (pendingNavigation) {
                window.location.href = pendingNavigation;
            } else if (window.history && window.history.length > 1) {
                window.history.back();
            }
        }
    }

    if (btnKembali) {
        btnKembali.addEventListener("click", function (e) {
            if (isFormDirty) {
                e.preventDefault();
                window
                    .confirmAction(
                        "Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?",
                        "Konfirmasi",
                        "Ya, tinggalkan",
                        "Batal"
                    )
                    .then((result) => {
                        if (result.isConfirmed) {
                            isFormDirty = false; // Reset flag sebelum redirect
                            window.location.href = btnKembali.href;
                        }
                    });
            }
        });
    }

    const sidebar = document.querySelector(".sidebar");
    if (sidebar) {
        sidebar.querySelectorAll("a[href]").forEach((link) => {

            if (link.closest("form")) return;

            link.addEventListener("click", function (e) {
                if (isFormDirty) {
                    e.preventDefault();

                    window
                        .confirmAction(
                            "Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?",
                            "Konfirmasi",
                            "Ya, tinggalkan",
                            "Batal"
                        )
                        .then((result) => {
                            if (result.isConfirmed) {
                                isFormDirty = false; // Reset flag sebelum redirect
                                window.location.href = link.href;
                            }
                        });
                }
            });
        });
    }

    const rupiahInputs = document.querySelectorAll(".rupiah-mask");

    function formatRupiah(angka) {
        if (!angka) return "";

        let number_string = String(angka).replace(/[^0-9]/g, "");
        if (number_string === "") return "";
        return number_string.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    rupiahInputs.forEach(function (input) {

        input.value = formatRupiah(input.value);

        input.addEventListener("input", function (e) {
            const pos = this.selectionStart;
            this.value = formatRupiah(this.value);

            this.selectionStart = this.selectionEnd = this.value.length;
        });
        input.addEventListener("blur", function () {
            this.value = formatRupiah(this.value);
        });
    });

    if (!form) return;


    form.addEventListener("submit", function (e) {
        let actionInput = form.querySelector('input[name="action"]');
        let action = actionInput?.value;

        // Jika action belum di-set (misal submit via Enter), turunkan dari select status_qc
        if (!action && statusQcSelect) {
            const status = statusQcSelect.value;
            const config =
                statusToAction[status] || statusToAction["menunggu_qc"];
            action = config.action;
            if (!actionInput) {
                actionInput = document.createElement("input");
                actionInput.type = "hidden";
                actionInput.name = "action";
                form.appendChild(actionInput);
            }
            actionInput.value = action;
        }

        // strip formatting to plain digits before submit
        rupiahInputs.forEach(function (input) {
            input.value = input.value ? input.value.replace(/\./g, "") : "";
        });

        form.querySelectorAll(".required-field").forEach((field) => {
            if (window.FormValidator) {
                FormValidator.clearValidation(field);
            } else {
                field.classList.remove("is-invalid");
            }
        });

        const currentStatus = statusQcSelect?.value || "menunggu_qc";
        const requiresValidation =
            statusToAction[currentStatus]?.requiresValidation || false;

        // Jika tidak perlu validasi (draft atau gagal), langsung submit
        if (!requiresValidation) {
            return true; // Lanjutkan submit tanpa validasi
        }

        if (requiresValidation) {
            let isValid = true;

            function setFieldInvalid(field) {
                if (window.FormValidator) {
                    FormValidator.setInvalid(field);
                } else {
                    field.classList.add("is-invalid");
                }
            }

            const namaField = form.querySelector('[name="nama_item"]');
            const nama = namaField?.value.trim() || "";
            if (!nama) {
                setFieldInvalid(namaField);
                isValid = false;
            }

            const kategoriField = form.querySelector('[name="kategori_id"]');
            const kategori = kategoriField?.value || "";
            if (!kategori) {
                setFieldInvalid(kategoriField);
                isValid = false;
            }

            const kodeSkuField = form.querySelector('[name="kode_sku"]');
            const kodeSku = kodeSkuField?.value.trim() || "";
            if (!kodeSku) {
                setFieldInvalid(kodeSkuField);
                isValid = false;
            }

            const deskripsiField = form.querySelector(
                '[name="deskripsi_produk"]'
            );
            const deskripsi = deskripsiField?.value.trim() || "";
            if (!deskripsi) {
                setFieldInvalid(deskripsiField);
                isValid = false;
            }

            const hargaJualField = form.querySelector('[name="harga_jual"]');
            const hargaJual = hargaJualField?.value.trim() || "";
            if (
                !hargaJual ||
                !/^\d+$/.test(hargaJual) ||
                parseInt(hargaJual) <= 0
            ) {
                setFieldInvalid(hargaJualField);
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();

                const firstError = form.querySelector(".is-invalid");
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    setTimeout(() => firstError.focus(), 300);
                }
                return false;
            }
        }
    });

    form.querySelectorAll(".required-field").forEach((field) => {
        field.addEventListener("blur", function () {
            if (this.value.trim() === "") {
                if (window.FormValidator) {
                    FormValidator.setInvalid(this);
                } else {
                    this.classList.add("is-invalid");
                }
            } else {
                if (window.FormValidator) {
                    FormValidator.clearValidation(this);
                } else {
                    this.classList.remove("is-invalid");
                }
            }
        });

        field.addEventListener("input", function () {
            if (this.classList.contains("is-invalid")) {
                if (window.FormValidator) {
                    FormValidator.clearValidation(this);
                } else {
                    this.classList.remove("is-invalid");
                }
            }
        });

        if (field.name === "harga_jual") {
            field.addEventListener("blur", function () {
                const value = this.value.replace(/\./g, "");
                if (!value || !/^\d+$/.test(value) || parseInt(value) <= 0) {
                    if (window.FormValidator) {
                        FormValidator.setInvalid(
                            this,
                            "Harga Jual wajib diisi dan harus berupa angka lebih dari 0"
                        );
                    } else {
                        this.classList.add("is-invalid");
                    }
                } else {
                    if (window.FormValidator) {
                        FormValidator.clearValidation(this);
                    } else {
                        this.classList.remove("is-invalid");
                    }
                }
            });
        }
    });

    const unsavedConfirmBtn = document.getElementById("unsavedConfirmBtn");
    function closeUnsavedModal() {
        if (unsavedModal) {
            try {
                unsavedModal.hide();
            } catch (e) {
                
            }
        }
        pendingNavigation = null;
    }

    if (unsavedConfirmBtn) {
        unsavedConfirmBtn.addEventListener("click", function () {

            isFormDirty = false;

            closeUnsavedModal();
            if (pendingNavigation) {

                window.location.href = pendingNavigation;
            } else if (window.history && window.history.length > 1) {

                window.history.back();
            }
        });
    }

    if (statusQcSelect) {
        const initialStatus = statusQcSelect.value;
        toggleRequiredFields(initialStatus === "lolos_qc");
    }

});
