

(function () {
    function navigate(url, ev) {
        if (!url) return;
        try {
            if (
                ev &&
                (ev.ctrlKey || ev.metaKey || ev.shiftKey || ev.button === 1)
            ) {
                window.open(url, "_blank");
            } else {
                window.location.href = url;
            }
        } catch (e) {
            window.location.href = url;
        }
    }

    function initClickableRows(options = {}) {
        const selector = options.selector || "[data-detail-url]";
        const ignoreSelector =
            options.ignoreSelector ||
            "a, button, .no-row-navigation, input, select, textarea";

        document.addEventListener("click", function (e) {
            const row = e.target.closest(selector);
            if (!row) return;
            if (e.target.closest(ignoreSelector)) return; // clicked an interactive element
            const url =
                row.getAttribute("data-detail-url") || row.dataset.detailUrl;
            if (!url) return;
            e.preventDefault();
            navigate(url, e);
        });

        document.addEventListener("keydown", function (e) {
            if (e.key !== "Enter") return;
            const el = document.activeElement;
            if (!el) return;
            const row = el.closest(selector);
            if (!row) return;
            if (el.closest(ignoreSelector)) return;
            const url =
                row.getAttribute("data-detail-url") || row.dataset.detailUrl;
            if (!url) return;
            navigate(url, e);
        });

        document.querySelectorAll(selector).forEach((r) => {
            if (!r.hasAttribute("tabindex")) r.setAttribute("tabindex", "0");

            const cur = window.getComputedStyle(r).cursor;
            if (!cur || cur === "auto") r.style.cursor = "pointer";
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            initClickableRows();
        });
    } else {
        initClickableRows();
    }

    window.ClickableRows = { init: initClickableRows };
})();
