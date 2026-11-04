(() => {
    document.querySelectorAll('[data-testimonial-form]').forEach((form) => {
        const input = form.querySelector('#screenshot');
        const remove = form.querySelector('#remove_screenshot');
        const validate = () => {
            const file = input.files?.[0];
            let message = file && file.size > 2 * 1024 * 1024 ? 'Screenshot maksimal 2 MB.' : '';
            if (file && remove?.checked) message = 'Pilih ganti screenshot atau hapus screenshot, bukan keduanya.';
            input.setCustomValidity(message);
        };
        input.addEventListener('change', () => { validate(); input.reportValidity(); });
        remove?.addEventListener('change', validate);
        form.addEventListener('submit', (event) => {
            validate();
            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
            }
        }, true);
        window.addEventListener('pageshow', validate);
    });
})();
