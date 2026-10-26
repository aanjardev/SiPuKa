(() => {
    let pickerOpen = false;
    function recover() {
        if (!pickerOpen) return;
        // Wait for the native dialog to return focus, without a timed overlay.
        requestAnimationFrame(() => {
            if (!document.hasFocus()) return;
            pickerOpen = false;
            const modalOpen = document.querySelector('.modal.show, .modal[aria-modal="true"], dialog[open]');
            if (!modalOpen) {
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
            }
            const sidebar = document.getElementById('sidebar');
            if (!sidebar?.classList.contains('show')) {
                document.getElementById('sidebarOverlay')?.classList.remove('show');
            }
            if (!modalOpen && !sidebar?.classList.contains('show') && document.body.style.overflow === 'hidden') {
                document.body.style.removeProperty('overflow');
            }
        });
    }
    document.addEventListener('click', event => {
        if (event.target.matches?.('input[type="file"]')) pickerOpen = true;
    }, true);
    for (const eventName of ['change', 'cancel']) {
        document.addEventListener(eventName, event => {
            if (event.target.matches?.('input[type="file"]')) recover();
        }, true);
    }
    window.addEventListener('focus', recover);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) recover();
    });
})();
