(() => {
    console.log("[ProductImageUploader] Script loaded");

    class ProductImageUploader {
        constructor(config) {
            this.grid = document.getElementById(config.gridId);
            this.form = config.formId
                ? document.getElementById(config.formId)
                : null;
            this.hiddenInput = config.hiddenInputId
                ? document.getElementById(config.hiddenInputId)
                : null;
            this.hiddenMainInput = config.hiddenMainInputId
                ? document.getElementById(config.hiddenMainInputId)
                : null;
            this.saveBtn = config.saveButtonId
                ? document.getElementById(config.saveButtonId)
                : null;
            this.statusElement = config.statusId
                ? document.getElementById(config.statusId)
                : null;
            this.removalInputContainer = config.removalInputContainerId
                ? document.getElementById(config.removalInputContainerId)
                : null;

            this.maxBoxes = parseInt(config.maxBoxes, 10) || 10;
            this.maxFileSize =
                parseInt(config.maxFileSize, 10) || 10 * 1024 * 1024;
            this.allowMainChoice = Boolean(config.allowMainChoice);
            this.requireFilesOnSubmit = Boolean(config.requireFilesOnSubmit);
            this.removalInputName =
                config.removalInputName || "remove_images[]";
            this.existingImages = Array.isArray(config.existingImages)
                ? config.existingImages
                : [];
            this.requireAtLeastOne = Boolean(config.requireAtLeastOne);
            this.initialEmptyBoxes =
                parseInt(config.initialEmptyBoxes, 10) || 1;

            this.queueIndex = 0;
            this.queuedFiles = {};
            this.removedExistingIds = new Set();
            this.lastTargetBox = null;

            this.initialized = false;
        }

        init() {
            console.log("[ProductImageUploader] init() called");

            if (this.initialized) {
                console.log(
                    "[ProductImageUploader] Already initialized, returning",
                );
                return;
            }

            console.log("[ProductImageUploader] Checking elements:", {
                grid: !!this.grid,
                form: !!this.form,
                hiddenInput: !!this.hiddenInput,
            });

            if (!this.grid || !this.form || !this.hiddenInput) {
                console.error(
                    "[ProductImageUploader] Missing required elements, cannot initialize",
                );
                return;
            }

            this.initialized = true;
            console.log("[ProductImageUploader] Initialization complete");

            this.renderExistingImages();
            this.ensureBoxes();

            if (this.saveBtn) {
                console.log(
                    "[ProductImageUploader] Attaching save button listener",
                );
                this.saveBtn.addEventListener("click", (event) =>
                    this.handleSaveButton(event),
                );
            }

            if (this.hiddenInput) {
                console.log(
                    "[ProductImageUploader] Attaching hidden input change listener",
                );
                this.hiddenInput.addEventListener("change", () =>
                    this.handlePickerChange(),
                );
            }

            // Intercept form submission to ensure populateHiddenInputs is called
            console.log(
                "[ProductImageUploader] Attaching form submit listener",
            );
            this.form.addEventListener("submit", (event) =>
                this.handleFormSubmit(event),
            );

            // IMPORTANT: Also wrap form.submit() to catch programmatic submissions
            console.log("[ProductImageUploader] Wrapping form.submit() method");
            const originalSubmit = this.form.submit;
            const self = this;
            this.form.submit = function () {
                console.log(
                    "[ProductImageUploader] form.submit() called (programmatic)",
                );
                self.populateHiddenInputs();
                originalSubmit.call(this);
            };
        }

        renderExistingImages() {
            if (
                !Array.isArray(this.existingImages) ||
                !this.existingImages.length
            ) {
                return;
            }

            this.existingImages.forEach((image) => {
                if (!image || !image.url) {
                    return;
                }
                const box = document.createElement("div");
                box.className = "upload-box has-image existing-image";
                box.dataset.existingId = image.id || "";

                const mainChoiceMarkup =
                    this.allowMainChoice && image.id
                        ? `
                    <div class="main-choice">
                        <label class="form-check-label">
                            <input type="radio" name="main_image_choice" value="existing_${image.id}" class="form-check-input mt-0" ${
                                image.is_main ? "checked" : ""
                            }>
                            <span>Utama</span>
                        </label>
                    </div>
                `
                        : "";

                box.innerHTML = `
                    <div class="preview">
                        <img src="${image.url}" alt="Foto produk">
                    </div>
                    <div class="controls">
                        <button type="button" class="btn btn-danger btn-remove-existing" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    ${mainChoiceMarkup}
                `;

                const removeBtn = box.querySelector(".btn-remove-existing");
                if (removeBtn) {
                    removeBtn.addEventListener("click", (event) => {
                        event.stopPropagation();
                        this.markExistingForRemoval(image.id, box);
                    });
                }

                if (this.allowMainChoice) {
                    box.addEventListener("click", (event) => {
                        if (
                            event.target.closest(".controls") ||
                            event.target.closest(".main-choice") ||
                            event.target.tagName === "INPUT"
                        ) {
                            return;
                        }
                        const radio = box.querySelector('input[type="radio"]');
                        if (radio) {
                            radio.checked = true;
                        }
                    });
                }

                this.grid.appendChild(box);
            });
        }

        markExistingForRemoval(imageId, box) {
            if (!imageId || this.removedExistingIds.has(imageId)) {
                box.remove();
                this.ensureBoxes();
                return;
            }

            if (this.requireAtLeastOne) {
                const remaining = this.getTotalImagesCount({
                    excludeExistingId: imageId,
                });
                if (remaining < 1) {
                    this.showStatus("Minimal harus ada 1 foto produk", "error");
                    return;
                }
            }

            this.removedExistingIds.add(imageId);

            if (this.removalInputContainer) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = this.removalInputName;
                input.value = imageId;
                this.removalInputContainer.appendChild(input);
            }

            box.remove();
            this.ensureBoxes();
        }

        makeEmptyBox() {
            const idx = ++this.queueIndex;
            console.log(
                `[ProductImageUploader] makeEmptyBox() creating box with index ${idx}, current queueIndex: ${this.queueIndex}`,
            );

            const box = document.createElement("div");
            box.className = "upload-box";
            box.dataset.boxIndex = idx;

            box.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                    <div style="font-size: 0.75rem; font-weight: 500;">Klik Upload</div>
                </div>
                <div class="preview d-none"></div>
                <div class="controls d-none">
                    <button type="button" class="btn btn-danger btn-remove-queue" data-index="${idx}" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${
                    this.allowMainChoice
                        ? `
                    <div class="main-choice d-none">
                        <label class="form-check-label">
                            <input type="radio" name="main_image_choice" value="new_${idx}" class="form-check-input mt-0">
                            <span>Utama</span>
                        </label>
                    </div>
                `
                        : ""
                }
            `;

            const removeBtn = box.querySelector(".btn-remove-queue");

            box.addEventListener("click", (event) => {
                if (
                    event.target.closest(".controls") ||
                    event.target.closest(".main-choice")
                ) {
                    return;
                }
                this.lastTargetBox = box;
                this.hiddenInput?.click();
            });

            removeBtn.addEventListener("click", (event) => {
                event.stopPropagation();

                if (this.requireAtLeastOne) {
                    const remaining = this.getTotalImagesCount({
                        excludeNewIndex: idx,
                    });
                    if (remaining < 1) {
                        this.showStatus(
                            "Minimal harus ada 1 foto produk",
                            "error",
                        );
                        return;
                    }
                }

                const radio = box.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = false;
                }

                delete this.queuedFiles[idx];
                box.remove();
                this.updateSaveButton();
                this.ensureBoxes();
            });

            return box;
        }

        handlePickerChange() {
            console.log("[ProductImageUploader] handlePickerChange called");

            if (!this.hiddenInput) {
                console.warn(
                    "[ProductImageUploader] hiddenInput not available",
                );
                return;
            }

            const files = Array.from(this.hiddenInput.files || []);
            console.log(
                `[ProductImageUploader] Files selected from picker: ${files.length}`,
                {
                    files: files.map((f) => ({ name: f.name, size: f.size })),
                },
            );

            if (!files.length) {
                console.log(
                    "[ProductImageUploader] No files selected, returning",
                );
                return;
            }

            // VALIDATION: Check if total will exceed maxBoxes
            const currentQueuedCount = Object.keys(this.queuedFiles).length;
            const currentExistingCount = this.getRemainingExistingCount();
            const totalCurrentCount = currentQueuedCount + currentExistingCount;
            const remainingSlots = this.maxBoxes - totalCurrentCount;

            console.log("[ProductImageUploader] Slot check:", {
                currentQueued: currentQueuedCount,
                currentExisting: currentExistingCount,
                totalCurrent: totalCurrentCount,
                maxBoxes: this.maxBoxes,
                remainingSlots: remainingSlots,
                filesSelected: files.length,
            });

            if (remainingSlots <= 0) {
                this.showStatus(
                    `Maksimal ${this.maxBoxes} gambar sudah tercapai. Hapus gambar terlebih dahulu untuk menambah.`,
                    "error",
                );
                this.hiddenInput.value = "";
                return;
            }

            // LIMIT: Only accept up to remainingSlots files
            const acceptableFiles = files.slice(0, remainingSlots);
            if (acceptableFiles.length < files.length) {
                const excessCount = files.length - acceptableFiles.length;
                this.showStatus(
                    `Hanya dapat menambah ${remainingSlots} gambar lagi (${excessCount} gambar diabaikan karena sudah mencapai maksimal).`,
                    "warning",
                );
            }

            const validFiles = acceptableFiles.filter((f) => {
                if (!f.type.startsWith("image/")) {
                    this.showStatus("File harus berupa gambar", "error");
                    return false;
                }
                if (f.size > this.maxFileSize) {
                    const maxMB = (this.maxFileSize / 1024 / 1024).toFixed(1);
                    const fileMB = (f.size / 1024 / 1024).toFixed(1);
                    this.showStatus(
                        `File ${f.name} terlalu besar (${fileMB}MB > ${maxMB}MB)`,
                        "error",
                    );
                    return false;
                }
                return true;
            });

            console.log(
                `[ProductImageUploader] Valid files after filter: ${validFiles.length}`,
            );

            if (!validFiles.length) {
                console.log(
                    "[ProductImageUploader] No valid files, clearing input",
                );
                this.hiddenInput.value = "";
                return;
            }

            const baseBox = this.lastTargetBox || this.makeEmptyBox();
            console.log(
                `[ProductImageUploader] Starting to process ${validFiles.length} files, base box created`,
            );

            let loadedCount = 0;

            validFiles.forEach((file, index) => {
                const targetBox = index === 0 ? baseBox : this.makeEmptyBox();
                if (index > 0) {
                    this.grid.appendChild(targetBox);
                }

                console.log(
                    `[ProductImageUploader] Processing file ${index + 1}/${validFiles.length}: ${file.name}`,
                );

                const reader = new FileReader();
                reader.onload = (event) => {
                    console.log(
                        `[ProductImageUploader] FileReader.onload for ${file.name}`,
                    );
                    const preview = targetBox.querySelector(".preview");
                    preview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                    preview.classList.remove("d-none");
                    targetBox
                        .querySelector(".empty-state")
                        .classList.add("d-none");
                    targetBox
                        .querySelector(".controls")
                        .classList.remove("d-none");
                    const mainChoice = targetBox.querySelector(".main-choice");
                    if (mainChoice) {
                        mainChoice.classList.remove("d-none");
                    }
                    targetBox.classList.add("has-image");

                    const targetIdx = targetBox.dataset.boxIndex;
                    this.queuedFiles[targetIdx] = file;
                    console.log(
                        `[ProductImageUploader] File queued at index ${targetIdx}, total queued: ${Object.keys(this.queuedFiles).length}`,
                    );

                    // COUNT loaded files
                    loadedCount++;
                    console.log(
                        `[ProductImageUploader] FileReader complete: ${loadedCount}/${validFiles.length}`,
                    );

                    // ONLY update button and ensure boxes AFTER ALL files are loaded
                    if (loadedCount === validFiles.length) {
                        console.log(
                            "[ProductImageUploader] ALL FileReaders complete, calling updateSaveButton and ensureBoxes",
                        );
                        this.updateSaveButton();
                        this.ensureBoxes();
                    }
                };

                reader.onerror = (error) => {
                    console.error(
                        `[ProductImageUploader] FileReader error for ${file.name}:`,
                        error,
                    );
                };

                reader.readAsDataURL(file);
            });

            this.hiddenInput.value = "";
            this.lastTargetBox = null;
            console.log("[ProductImageUploader] handlePickerChange complete");
        }

        getRemainingExistingCount(options = {}) {
            let count =
                this.existingImages.length - this.removedExistingIds.size;
            if (
                options.excludeExistingId &&
                !this.removedExistingIds.has(options.excludeExistingId)
            ) {
                count -= 1;
            }
            return Math.max(count, 0);
        }

        getQueuedCount(options = {}) {
            const excludeIndex =
                options.excludeNewIndex != null
                    ? String(options.excludeNewIndex)
                    : null;
            return Object.keys(this.queuedFiles).reduce((total, key) => {
                if (excludeIndex && String(key) === excludeIndex) {
                    return total;
                }
                return total + 1;
            }, 0);
        }

        getTotalImagesCount(options = {}) {
            return (
                this.getRemainingExistingCount(options) +
                this.getQueuedCount(options)
            );
        }

        ensureBoxes() {
            console.log("[ProductImageUploader] ensureBoxes() called");

            if (!this.grid) {
                console.warn("[ProductImageUploader] grid is null");
                return;
            }

            const filledBoxes = this.grid.querySelectorAll(
                ".upload-box.has-image",
            ).length;
            let emptyBoxes = Array.from(
                this.grid.querySelectorAll(".upload-box:not(.has-image)"),
            );

            console.log("[ProductImageUploader] ensureBoxes status:", {
                filledBoxes,
                emptyBoxes: emptyBoxes.length,
                maxBoxes: this.maxBoxes,
                totalBoxes: this.grid.querySelectorAll(".upload-box").length,
            });

            if (filledBoxes >= this.maxBoxes) {
                console.log(
                    "[ProductImageUploader] Reached max boxes, removing all empty boxes",
                );
                emptyBoxes.forEach((box) => box.remove());
                return;
            }

            let desiredEmpty = Math.max(
                this.initialEmptyBoxes - filledBoxes,
                0,
            );
            if (desiredEmpty === 0) {
                desiredEmpty = 1;
            }
            desiredEmpty = Math.min(
                desiredEmpty,
                Math.max(this.maxBoxes - filledBoxes, 0),
            );

            console.log(
                "[ProductImageUploader] Desired empty boxes:",
                desiredEmpty,
                "current empty:",
                emptyBoxes.length,
            );

            while (
                emptyBoxes.length < desiredEmpty &&
                filledBoxes + emptyBoxes.length < this.maxBoxes
            ) {
                const newBox = this.makeEmptyBox();
                this.grid.appendChild(newBox);
                emptyBoxes.push(newBox);
                console.log(
                    "[ProductImageUploader] Added new empty box, now have",
                    emptyBoxes.length,
                );
            }

            if (emptyBoxes.length > desiredEmpty) {
                const toRemove = emptyBoxes.length - desiredEmpty;
                console.log(
                    "[ProductImageUploader] Removing",
                    toRemove,
                    "extra empty boxes",
                );
                emptyBoxes.slice(desiredEmpty).forEach((box) => box.remove());
            }

            console.log(
                "[ProductImageUploader] ensureBoxes complete, total boxes now:",
                this.grid.querySelectorAll(".upload-box").length,
            );
        }

        updateSaveButton() {
            if (!this.saveBtn) {
                return;
            }

            const queueLength = Object.keys(this.queuedFiles).length;
            this.saveBtn.disabled = queueLength === 0;

            if (queueLength > 0) {
                this.saveBtn.innerHTML = `<i class="fas fa-save"></i> Simpan (${queueLength})`;
                return;
            }

            this.saveBtn.innerHTML =
                '<i class="fas fa-save"></i> Simpan Perubahan';
        }

        populateHiddenInputs() {
            if (!this.hiddenInput) {
                console.warn(
                    "[ProductImageUploader] hiddenInput not found, cannot populate",
                );
                return;
            }

            const queuedFilesList = Object.values(this.queuedFiles);
            console.log("[ProductImageUploader] populateHiddenInputs:", {
                totalQueued: queuedFilesList.length,
                files: queuedFilesList.map((f) => ({
                    name: f.name,
                    size: f.size,
                    type: f.type,
                })),
            });

            try {
                const dt = new DataTransfer();
                let addedCount = 0;
                let failedCount = 0;

                queuedFilesList.forEach((file) => {
                    try {
                        dt.items.add(file);
                        addedCount++;
                        console.log(
                            `[ProductImageUploader] Added file: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`,
                        );
                    } catch (e) {
                        failedCount++;
                        console.error(
                            `[ProductImageUploader] Failed to add file: ${file.name}`,
                            e.message,
                        );
                    }
                });

                console.log(
                    `[ProductImageUploader] DataTransfer result: ${addedCount} added, ${failedCount} failed out of ${queuedFilesList.length}`,
                );

                // CRITICAL: Check if assignment works
                console.log(
                    `[ProductImageUploader] DataTransfer.files length before assignment: ${dt.files.length}`,
                );
                this.hiddenInput.files = dt.files;
                console.log(
                    `[ProductImageUploader] Hidden input files count after assignment: ${this.hiddenInput.files.length}`,
                );

                if (this.hiddenInput.files.length !== addedCount) {
                    console.warn(
                        `[ProductImageUploader] WARNING: Files count mismatch! Expected ${addedCount}, got ${this.hiddenInput.files.length}`,
                    );
                }

                if (this.hiddenMainInput) {
                    const selected = this.form.querySelector(
                        'input[name="main_image_choice"]:checked',
                    );
                    this.hiddenMainInput.value = selected ? selected.value : "";
                    console.log(
                        `[ProductImageUploader] Main image selected: ${this.hiddenMainInput.value}`,
                    );
                }
            } catch (e) {
                console.error(
                    "[ProductImageUploader] populateHiddenInputs failed with exception:",
                    e,
                );
                throw e;
            }
        }

        getQueuedFiles() {
            return Object.values(this.queuedFiles);
        }

        handleSaveButton(event) {
            event.preventDefault();

            const queuedCount = this.getQueuedFiles().length;
            console.log("[ProductImageUploader] handleSaveButton clicked:", {
                queuedCount,
            });

            if (this.requireFilesOnSubmit && queuedCount === 0) {
                this.showStatus("Tidak ada gambar untuk disimpan", "error");
                return;
            }

            console.log(
                "[ProductImageUploader] Populating hidden inputs before submit...",
            );
            this.populateHiddenInputs();

            console.log(
                "[ProductImageUploader] Disabling save button and submitting form...",
            );
            this.disableSaveButton();
            this.form.submit();
        }

        handleFormSubmit(event) {
            console.log("[ProductImageUploader] handleFormSubmit called");
            this.populateHiddenInputs();

            const hasQueued = this.getQueuedFiles().length > 0;

            console.log("[ProductImageUploader] Form submit checks:", {
                hasQueued,
                requireFilesOnSubmit: this.requireFilesOnSubmit,
                removedCount: this.removedExistingIds.size,
                totalImages: this.getTotalImagesCount(),
            });

            if (
                this.requireFilesOnSubmit &&
                !hasQueued &&
                !this.removedExistingIds.size
            ) {
                event.preventDefault();
                this.showStatus("Tidak ada gambar untuk disimpan", "error");
                return;
            }

            if (this.requireAtLeastOne && this.getTotalImagesCount() < 1) {
                event.preventDefault();
                this.showStatus("Minimal harus ada 1 foto produk", "error");
                return;
            }

            console.log(
                "[ProductImageUploader] Form validation passed, allowing submit",
            );
            this.disableSaveButton();
        }

        disableSaveButton() {
            if (!this.saveBtn) {
                return;
            }

            this.saveBtn.disabled = true;
            this.saveBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        }

        showStatus(message, type = "success") {
            if (!this.statusElement) {
                return;
            }

            this.statusElement.className = `upload-status ${type}`;
            this.statusElement.textContent = message;
            this.statusElement.style.display = "block";

            window.clearTimeout(this.statusTimeout);
            this.statusTimeout = window.setTimeout(() => {
                this.statusElement.style.display = "none";
            }, 3000);
        }
    }

    function boot() {
        console.log(
            "[ProductImageUploader] boot() called, document.readyState:",
            document.readyState,
        );
        const configs = window.productImageConfigs || [];
        console.log("[ProductImageUploader] Found configs:", configs.length);

        configs.forEach((config, idx) => {
            console.log(
                `[ProductImageUploader] Initializing config ${idx}:`,
                config,
            );
            try {
                new ProductImageUploader(config).init();
                console.log(
                    `[ProductImageUploader] Config ${idx} initialized successfully`,
                );
            } catch (error) {
                console.error(
                    `ProductImageUploader config ${idx} init failed`,
                    error,
                );
            }
        });
    }

    if (document.readyState === "loading") {
        console.log(
            "[ProductImageUploader] Document still loading, adding DOMContentLoaded listener",
        );
        document.addEventListener("DOMContentLoaded", boot);
    } else {
        console.log(
            "[ProductImageUploader] Document already loaded, calling boot() immediately",
        );
        boot();
    }
})();
