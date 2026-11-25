const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

(async () => {
    const source = fs.readFileSync(path.join(__dirname, '../../resources/js/pembelian/item-api.js'), 'utf8');
    const { default: ItemAPI } = await import(`data:text/javascript;base64,${Buffer.from(source).toString('base64')}`);
    const api = new ItemAPI({ routes: { updateItemDraft: '/items' }, csrf: 'token' });

    global.fetch = async () => ({
        ok: false, status: 422,
        json: async () => ({ errors: { kondisi_lain_lain: ['Catatan terlalu panjang.'], nama_item: ['Nama wajib diisi.'] } }),
    });
    await assert.rejects(api.update(1, {}), { message: 'Catatan terlalu panjang.\nNama wajib diisi.' });

    global.fetch = async () => ({ ok: false, status: 500, json: async () => ({ message: 'Gagal menyimpan data.' }) });
    await assert.rejects(api.update(1, {}), { message: 'Gagal menyimpan data.' });

    global.fetch = async () => ({ ok: false, status: 502, json: async () => { throw new SyntaxError(); } });
    await assert.rejects(api.update(1, {}), /502/);

    global.fetch = async () => ({ ok: true, json: async () => ({ success: true, item: { id: 1 } }) });
    assert.deepEqual(await api.update(1, {}), { success: true, item: { id: 1 } });

    const formSource = fs.readFileSync(path.join(__dirname, '../../resources/js/pembelian/item-form.js'), 'utf8');
    const { default: ItemForm } = await import(`data:text/javascript;base64,${Buffer.from(formSource).toString('base64')}`);
    const listeners = {};
    const feedback = {};
    const field = {
        value: '   ', required: true, maxLength: 50, dataset: { errorMessage: 'Nama item wajib diisi' },
        validity: { valid: true }, parentElement: { querySelector: selector => selector === 'label' ? { textContent: 'Nama Item *' } : feedback },
        classList: { add() {}, remove() {} }, setAttribute() {}, removeAttribute() {},
        addEventListener: (event, callback) => { listeners[event] = callback; },
        setCustomValidity(message) { this.message = message; },
        scrollIntoView() {}, focus() {}, reportValidity() {}, closest: () => null,
    };
    global.document = { getElementById: () => field };
    const form = new ItemForm({ nama_item: 'item_nama_item' });
    assert.equal(form.validate(), false);
    assert.equal(field.message, 'Nama item wajib diisi');
    field.value = 'K'.repeat(51);
    assert.equal(form.validate(), false);
    assert.equal(field.message, 'Nama Item maksimal 50 karakter.');
    field.value = 'K'.repeat(50);
    assert.equal(form.validate(), true);
    assert.equal(form.showErrors({ nama_item: ['Kategori tidak tersedia.'] }), true);
    assert.equal(field.message, 'Kategori tidak tersedia.');
    assert.equal(feedback.textContent, field.message);
    listeners.input();
    assert.equal(field.message, '');
    // Unknown fields must use alert fallback, rather than silently hiding server errors.
    assert.equal(form.showErrors({ customer_id: ['Customer tidak tersedia.'] }), false);

    const controller = fs.readFileSync(path.join(__dirname, '../../app/Http/Controllers/PembelianController.php'), 'utf8');
    const view = fs.readFileSync(path.join(__dirname, '../../resources/views/admin/inputPembelian.blade.php'), 'utf8');
    const rules = controller.slice(controller.indexOf('$itemRules = ['), controller.indexOf('$request->merge'));
    for (const [, key, max] of rules.matchAll(/'([^']+)' => '(?:required|nullable)\|string\|max:(\d+)'/g)) {
        const tag = view.match(new RegExp(`<(?:input|textarea)\\b[^>]*\\bid="item_${key}"[^>]*>`));
        assert.ok(tag, `Missing input: ${key}`);
        assert.ok(tag[0].includes(`maxlength="${max}"`), `Wrong maxlength: ${key}`);
    }
    console.log('purchase-item-api: OK');
})().catch(error => { console.error(error); process.exitCode = 1; });
