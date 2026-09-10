(function () {
    'use strict';

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-action-delete');
        if (!btn) return;

        const message = btn.dataset.message || 'Lanjutkan tindakan ini?';
        const title = btn.dataset.title || 'Konfirmasi Tindakan';
        const confirmText = btn.dataset.confirmText || 'Lanjutkan';
        const cancelText = btn.dataset.cancelText || 'Batal';

        if (!btn.form) {
            console.error("Error: Tombol delete tidak berada di dalam form.");
            return;
        }

        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete(message, title, confirmText, cancelText)
                .then(result => {
                    if (result.isConfirmed) {
                        btn.form.submit();
                    }
                });

            return;
        }

        if (confirm(message)) {
            btn.form.submit();
        }
    });
})();
