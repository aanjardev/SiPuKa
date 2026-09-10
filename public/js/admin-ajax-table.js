/**
 * Lightweight helper to handle search/filter/pagination via AJAX
 * Usage:
 *   TableAjax.init({
 *     formSelector: '#filterForm',
 *     containerSelector: '#purchase-list-container',
 *     tableBodySelector: '#purchase-table-body',
 *     paginationSelector: '#pagination-links-container',
 *     baseUrl: '.../route',
 *     searchInputSelector: '#search-input',
 *     filterSelectors: ['#filter-status', '#filter-sort'],
 *     rowClick: { selector: '.purchase-row', urlFrom: row => row.dataset.detailUrl, ignoreSelector: '.no-row-navigation' }
 *   });
 */
(function (window, document) {
    const defaultDebounce = (fn, delay) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    };

    class TableAjax {
        constructor(options) {
            this.container = document.querySelector(options.containerSelector);
            this.tableBody = document.querySelector(options.tableBodySelector);
            this.pagination = document.querySelector(
                options.paginationSelector
            );
            this.form = document.querySelector(options.formSelector);
            this.baseUrl = options.baseUrl;
            this.searchInput = options.searchInputSelector
                ? document.querySelector(options.searchInputSelector)
                : null;
            this.filterSelectors = options.filterSelectors || [];
            this.rowClick = options.rowClick;
            this.updateUrl = options.updateUrl !== false;
            this.debounceMs = options.debounceMs || 450;
            this.isFetching = false;
        }

        init() {
            if (!this.form || !this.tableBody || !this.pagination) return;
            this.bindForm();
            this.bindFilters();
            this.bindPagination();
            this.bindRowClick();
        }

        bindForm() {
            this.form.addEventListener("submit", (e) => {
                e.preventDefault();
                this.fetch(this.buildUrl());
            });

            if (this.searchInput) {
                const handler = defaultDebounce(
                    () => this.fetch(this.buildUrl()),
                    this.debounceMs
                );
                this.searchInput.addEventListener("keyup", handler);
            }
        }

        bindFilters() {
            this.filterSelectors.forEach((selector) => {
                const el = document.querySelector(selector);
                if (!el) return;
                el.addEventListener("change", () =>
                    this.fetch(this.buildUrl())
                );
            });
        }

        bindPagination() {
            this.pagination
                .querySelectorAll(".pagination a")
                .forEach((link) => {
                    link.addEventListener("click", (e) => {
                        e.preventDefault();
                        this.fetch(link.href);
                    });
                });
        }

        bindRowClick() {
            if (!this.rowClick || !this.tableBody) return;
            this.tableBody.addEventListener("click", (e) => {
                if (
                    this.rowClick.ignoreSelector &&
                    e.target.closest(this.rowClick.ignoreSelector)
                )
                    return;
                const row = e.target.closest(this.rowClick.selector);
                if (!row) return;
                const url = this.rowClick.urlFrom(row);
                if (url) {
                    window.location.href = url;
                }
            });
        }

        buildUrl() {
            const params = new URLSearchParams(new FormData(this.form));
            const query = params.toString();
            return query ? `${this.baseUrl}?${query}` : this.baseUrl;
        }

        fetch(url) {
            if (this.isFetching) return;
            this.isFetching = true;

            if (this.container) {
                this.container.style.opacity = "0.6";
                this.container.style.transition = "opacity 0.2s";
            }

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {

                    const incomingHtml = data.table_html || "";
                    let rowsHtml = "";
                    try {
                        const tmp = document.createElement("tbody");
                        tmp.innerHTML = incomingHtml;
                        const trs = tmp.querySelectorAll("tr");
                        if (trs.length) {
                            rowsHtml = Array.from(trs)
                                .map((t) => t.outerHTML)
                                .join("");
                        } else {

                            rowsHtml = incomingHtml;
                        }
                    } catch (e) {
                        rowsHtml = incomingHtml;
                    }

                    this.tableBody.innerHTML = rowsHtml;
                    this.pagination.innerHTML = data.pagination_html
                        ? `<div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">${data.pagination_html}</div>`
                        : "";

                    if (this.updateUrl) {
                        window.history.pushState(null, "", url);
                    }

                    this.bindPagination();
                })
                .catch((err) => {
                    console.error("Failed to fetch table data:", err);
                })
                .finally(() => {
                    this.isFetching = false;
                    if (this.container) {
                        this.container.style.opacity = "1";
                    }
                });
        }
    }

    window.TableAjax = {
        init(options) {
            const instance = new TableAjax(options);
            instance.init();
            return instance;
        },
    };

    window.confirmDeleteRecord = function (button, message) {
        const text = message || "Apakah Anda yakin ingin menghapus data ini?";
        if (confirm(text)) {
            button.form.submit();
        }
    };
})(window, document);
