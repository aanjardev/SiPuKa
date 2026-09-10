document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('settingsForm');
    if (!form) return;

    const hasInlineErrors = !!form.querySelector('.is-invalid, .invalid-feedback.d-block');
    const shouldHideButtons = hasInlineErrors;

    const hasCardError = (card) => {
        if (!card) return false;
        return !!card.querySelector('.is-invalid, .invalid-feedback.d-block');
    };

    const hideCardButton = (btn) => {
        if (!btn) return;
        btn.classList.remove('show-save');
        btn.style.display = 'none';
        btn.disabled = true;
    };

    const hideCardButtons = () => {
        form.querySelectorAll('.card-save-btn, .card-save-btn-logo').forEach(hideCardButton);
    };

    const showButtonIfClean = (card, btn) => {
        if (!btn) return;
        if (hasCardError(card)) return hideCardButton(btn);
        btn.style.display = '';
        btn.disabled = false;
        btn.classList.add('btn-primary', 'show-save');
    };

    if (shouldHideButtons) {
        hideCardButtons();
    } else {
        form.querySelectorAll('.card').forEach((card) => {
            const saveBtn = card.querySelector('.card-save-btn');
            const logoBtn = card.querySelector('.card-save-btn-logo');
            const targetBtn = logoBtn || saveBtn;
            if (!targetBtn) return;

            const markDirty = () => showButtonIfClean(card, targetBtn);
            card.addEventListener('input', markDirty);
            card.addEventListener('change', markDirty);
        });
    }

    const partnerRouteTemplate = form.dataset.routePartnerDestroy;
    const bannerRouteTemplate = form.dataset.routeBannerDestroy;
    const galleryRouteTemplate = form.dataset.routeGalleryDestroy;

    const pushUnique = (arr, value) => {
        if (!arr.includes(value)) arr.push(value);
    };

    let deletedPartnersArr = [];
    let deletedBannersArr = [];
    let deletedGalleriesArr = [];

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const container = btn.closest('[data-id][data-type]');
        const id = container?.getAttribute('data-id');
        const type = container?.getAttribute('data-type');
        if (!container || !id || !type) return;

        const confirmFn = typeof window.confirmDelete === 'function'
            ? window.confirmDelete
            : (msg, title) => Promise.resolve({ isConfirmed: window.confirm(msg || title || 'Hapus data ini?') });

        confirmFn('Apakah Anda yakin ingin menghapus item ini?', 'Konfirmasi Hapus')
            .then((result) => {
                if (!result?.isConfirmed) return;

        const urlMap = {
            partner: partnerRouteTemplate,
            banner: bannerRouteTemplate,
            gallery: galleryRouteTemplate,
        };
                const endpointTemplate = urlMap[type];
                if (!endpointTemplate) return;
                const finalUrl = endpointTemplate.replace(':id', id);

                btn.disabled = true;

                if (type === 'partner') {
                    pushUnique(deletedPartnersArr, id);
                    const field = document.getElementById('deletedPartners');
                    if (field) field.value = JSON.stringify(deletedPartnersArr);
                } else if (type === 'banner') {
                    pushUnique(deletedBannersArr, id);
                    const field = document.getElementById('deletedBanners');
                    if (field) field.value = JSON.stringify(deletedBannersArr);
                } else if (type === 'gallery') {
                    pushUnique(deletedGalleriesArr, id);
                    const field = document.getElementById('deletedGalleries');
                    if (field) field.value = JSON.stringify(deletedGalleriesArr);
                }

                container.style.transition = 'all 0.3s';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 300);

                if (form) {
                    form.requestSubmit();
                }

                if (finalUrl) {
                    fetch(finalUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        },
                    }).catch(() => {});
                }
            });
    });

    const fileInputs = Array.from(form.querySelectorAll('input[type="file"][data-max-bytes]'));

    const setInvalid = (input, message) => {
        input.classList.add('is-invalid');
        input.setCustomValidity(message || 'File tidak valid.');
        const feedback = input.nextElementSibling;
        if (feedback?.classList?.contains('invalid-feedback') && message) {
            feedback.textContent = message;
        }
    };

    const clearInvalid = (input) => {
        input.classList.remove('is-invalid');
        input.setCustomValidity('');
    };

    const validateFile = (input) => {
        const maxBytes = parseInt(input.dataset.maxBytes || '0', 10);
        const maxLabel = input.dataset.maxLabel || null;
        const file = input.files?.[0] || null;

        clearInvalid(input);

        if (!file || !maxBytes) return true;

        if (file.size > maxBytes) {
            const msg = `Ukuran file terlalu besar. Maksimal ${maxLabel || Math.round(maxBytes / 1024 / 1024) + 'MB'}.`;
            setInvalid(input, msg);
            return false;
        }

        if (file.type && !file.type.startsWith('image/')) {
            setInvalid(input, 'Format file harus berupa gambar.');
            return false;
        }

        return true;
    };

    fileInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const card = input.closest('.card');
            const btn = card?.querySelector('.card-save-btn, .card-save-btn-logo');
            const ok = validateFile(input);
            if (!ok) {
                hideCardButton(btn);
            } else {
                showButtonIfClean(card, btn);
            }
        });
    });

    form.addEventListener('input', (e) => {
        if (e.target.classList.contains('is-invalid')) {
            e.target.classList.remove('is-invalid');
            e.target.setCustomValidity('');
        }
    });

    form.addEventListener('submit', (e) => {
        let firstInvalid = null;
        fileInputs.forEach((input) => {
            const ok = validateFile(input);
            if (!ok && !firstInvalid) firstInvalid = input;
        });

        const otherInvalid = firstInvalid || form.querySelector('.is-invalid');

        if (otherInvalid) {
            e.preventDefault();
            e.stopPropagation();
            otherInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            otherInvalid.focus();
        }
    });
});
