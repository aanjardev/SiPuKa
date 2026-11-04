(() => {
    document.querySelectorAll('[data-poster-upload]').forEach((form) => {
        const input = form.querySelector('[data-poster-files]');
        const list = form.querySelector('[data-poster-alt-fields]');
        const validate = () => {
            const files = Array.from(input.files || []);
            let error = '';
            if (files.length > 5) error = 'Pilih maksimal 5 poster per unggahan.';
            else if (files.some((file) => file.size > 2 * 1024 * 1024)) error = 'Setiap poster maksimal 2 MB.';
            else if (files.reduce((sum, file) => sum + file.size, 0) > 6 * 1024 * 1024) error = 'Total ukuran poster maksimal 6 MB.';
            input.setCustomValidity(error);
        };
        input.addEventListener('change', () => {
            validate();
            if (list) {
                list.replaceChildren();
                Array.from(input.files || []).slice(0, 5).forEach((file, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'mb-3';
                    const label = document.createElement('label');
                    label.className = 'form-label';
                    label.htmlFor = `poster-alt-${index}`;
                    label.textContent = `Teks alternatif: ${file.name} (opsional)`;
                    const field = document.createElement('input');
                    field.id = label.htmlFor;
                    field.name = `alt_texts[${index}]`;
                    field.maxLength = 255;
                    field.className = 'form-control';
                    wrapper.append(label, field);
                    list.append(wrapper);
                });
            }
            if (input.validationMessage) input.reportValidity();
        });
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
