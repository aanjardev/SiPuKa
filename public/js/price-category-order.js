(() => {
    const list = document.getElementById('price-category-order');
    if (!list) return;
    const status = document.getElementById('price-order-status');
    const editButton = document.getElementById('price-order-edit');
    const saveButton = document.getElementById('price-order-save');
    const cancelButton = document.getElementById('price-order-cancel');
    const help = document.getElementById('price-order-help');
    const rows = () => [...list.querySelectorAll('[data-category-id]')];
    const ids = () => rows().map(row => Number(row.dataset.categoryId));
    let saved = ids();
    let saving = false;
    let editing = false;
    let drag = null;

    function restore() {
        const current = rows();
        saved.forEach(id => list.append(current.find(row => Number(row.dataset.categoryId) === id)));
    }

    function updateControls() {
        editButton.hidden = editing;
        editButton.disabled = saving || rows().length < 2;
        editButton.setAttribute('aria-expanded', String(editing));
        saveButton.hidden = cancelButton.hidden = !editing;
        saveButton.disabled = cancelButton.disabled = saving;
        list.querySelectorAll('.price-order-handle').forEach(handle => handle.disabled = !editing || saving);
        help.textContent = editing
            ? 'Geser pegangan di kiri, lalu klik Simpan Urutan. Dengan keyboard, gunakan panah atas/bawah pada pegangan.'
            : 'Klik Ubah Urutan untuk mengaktifkan drag and drop kategori harga.';
    }

    editButton.addEventListener('click', () => {
        editing = true;
        status.textContent = '';
        updateControls();
        rows()[0]?.querySelector('.price-order-handle').focus();
    });

    cancelButton.addEventListener('click', () => {
        restore();
        editing = false;
        status.textContent = 'Perubahan urutan dibatalkan.';
        status.className = 'small text-muted';
        updateControls();
        editButton.focus();
    });

    saveButton.addEventListener('click', async () => {
        if (!editing || saving || drag) return;
        const order = ids();
        if (JSON.stringify(order) === JSON.stringify(saved)) {
            editing = false;
            status.textContent = 'Urutan tidak berubah.';
            status.className = 'small text-muted';
            updateControls();
            editButton.focus();
            return;
        }
        saving = true;
        updateControls();
        status.textContent = 'Menyimpan urutan...';
        status.className = 'small text-muted';
        try {
            const response = await fetch(list.dataset.orderUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ ids: order }),
            });
            if (response.redirected) throw new Error('Sesi berakhir. Muat ulang halaman dan masuk kembali.');
            const data = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(data.message || 'Urutan gagal disimpan. Silakan coba lagi.');
            saved = order;
            editing = false;
            status.textContent = data.message || 'Urutan kategori harga tersimpan.';
            status.className = 'small text-success';
        } catch (error) {
            status.textContent = error.message || 'Koneksi terputus. Perubahan belum tersimpan; silakan coba lagi.';
            status.className = 'small text-danger';
        } finally {
            saving = false;
            updateControls();
            (editing ? saveButton : editButton).focus();
        }
    });

    updateControls();

    list.addEventListener('pointerdown', event => {
        const handle = event.target.closest('.price-order-handle');
        if (!handle || !editing || saving || event.button !== 0) return;
        event.preventDefault();
        handle.focus();
        drag = { row: handle.closest('tr'), pointerId: event.pointerId, startY: event.clientY, moved: false, before: rows() };
    });

    document.addEventListener('pointermove', event => {
        if (!drag || event.pointerId !== drag.pointerId) return;
        if (!drag.moved && Math.abs(event.clientY - drag.startY) < 5) return;
        event.preventDefault();
        drag.moved = true;
        drag.row.classList.add('price-order-dragging');
        const others = rows().filter(row => row !== drag.row);
        const next = others.find(row => {
            const rect = row.getBoundingClientRect();
            return event.clientY < rect.top + rect.height / 2;
        });
        list.insertBefore(drag.row, next || null);
        if (event.clientY < 80) window.scrollBy(0, -12);
        if (event.clientY > window.innerHeight - 80) window.scrollBy(0, 12);
    }, { passive: false });

    document.addEventListener('pointerup', event => {
        if (!drag || event.pointerId !== drag.pointerId) return;
        drag.row.classList.remove('price-order-dragging');
        const handle = drag.row.querySelector('.price-order-handle');
        drag = null;
        handle.focus({ preventScroll: true });
    });

    document.addEventListener('pointercancel', () => {
        if (!drag) return;
        drag.row.classList.remove('price-order-dragging');
        drag.before.forEach(row => list.append(row));
        drag = null;
    });

    list.addEventListener('keydown', event => {
        const handle = event.target.closest('.price-order-handle');
        if (!handle || !editing || saving || drag || !['ArrowUp', 'ArrowDown'].includes(event.key)) return;
        event.preventDefault();
        const row = handle.closest('tr');
        if (event.key === 'ArrowUp' && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
        if (event.key === 'ArrowDown' && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
        handle.focus();
        handle.focus({ preventScroll: true });
    });
})();
