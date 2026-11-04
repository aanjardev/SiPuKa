(() => {
    const dialog = document.getElementById('poster-dialog');
    const image = document.getElementById('poster-dialog-image');
    if (!dialog || !image) return;
    document.querySelectorAll('[data-original]').forEach((button) => {
        button.addEventListener('click', () => {
            image.src = button.dataset.original;
            image.alt = button.dataset.posterAlt;
            dialog.showModal();
            dialog.querySelector('.poster-dialog-image').scrollTo(0, 0);
        });
    });
    dialog.addEventListener('close', () => image.removeAttribute('src'));
    dialog.addEventListener('click', (event) => {
        if (event.target !== dialog) return;
        const rect = dialog.getBoundingClientRect();
        if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
    });
})();
