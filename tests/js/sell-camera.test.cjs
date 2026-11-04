const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const script = (name) => fs.readFileSync(path.join(__dirname, '../../public/js', name), 'utf8');

test('poster dialog loads originals only on click and releases them when closed', () => {
    const listeners = {};
    const image = { removeAttribute(name) { delete this[name]; } };
    const dialog = {
        open: false,
        addEventListener(name, callback) { listeners[name] = callback; },
        showModal() { this.open = true; },
        close() { this.open = false; listeners.close(); },
        querySelector() { return { scrollTo() {} }; },
        getBoundingClientRect() { return { left: 20, right: 200, top: 20, bottom: 200 }; },
    };
    let click;
    const poster = { dataset: { original: '/original.png', posterAlt: 'Alur jual kamera' }, addEventListener(name, callback) { click = callback; } };
    vm.runInNewContext(script('sell-camera.js'), { document: { getElementById(id) { return id === 'poster-dialog' ? dialog : image; }, querySelectorAll() { return [poster]; } } });
    assert.equal(image.src, undefined);
    click();
    assert.equal(dialog.open, true);
    assert.equal(image.src, '/original.png');
    assert.equal(image.alt, 'Alur jual kamera');
    listeners.click({ target: dialog, clientX: 100, clientY: 100 });
    assert.equal(dialog.open, true);
    listeners.click({ target: dialog, clientX: 0, clientY: 0 });
    assert.equal(dialog.open, false);
    assert.equal(image.src, undefined);
});

function uploadForm() {
    const handlers = {};
    const input = { files: [], validationMessage: '', setCustomValidity(message) { this.validationMessage = message; }, reportValidity() {}, addEventListener(name, callback) { handlers[name] = callback; } };
    const list = { children: [], replaceChildren() { this.children = []; }, append(node) { this.children.push(node); } };
    const form = { querySelector(selector) { return selector === '[data-poster-files]' ? input : list; }, checkValidity() { return !input.validationMessage; }, reportValidity() {}, addEventListener(name, callback, capture) { handlers.submit = callback; assert.equal(capture, true); } };
    const document = { querySelectorAll() { return [form]; }, createElement() { return { children: [], append(...nodes) { this.children.push(...nodes); } }; } };
    vm.runInNewContext(script('sell-camera-admin.js'), { document, window: { addEventListener() {} } });
    return { input, handlers, list };
}

test('upload limits reject too many, too large, and over-total files before submission', () => {
    const { input, handlers } = uploadForm();
    const mb = 1024 * 1024;
    for (const files of [Array(6).fill({ name: 'poster.png', size: 100 }), [{ name: 'large.png', size: 2 * mb + 1 }], Array(4).fill({ name: 'poster.png', size: 1.8 * mb })]) {
        input.files = files;
        let prevented = false;
        handlers.submit({ preventDefault() { prevented = true; } });
        assert.equal(prevented, true);
        assert.notEqual(input.validationMessage, '');
    }
    input.files = [{ name: 'poster.png', size: 100 }];
    let prevented = false;
    handlers.submit({ preventDefault() { prevented = true; } });
    assert.equal(prevented, false);
    assert.equal(input.validationMessage, '');
});

test('file names create text labels and optional alternate fields without HTML insertion', () => {
    const { input, handlers, list } = uploadForm();
    input.files = [{ name: '<img src=x>.png', size: 100 }];
    handlers.change();
    assert.equal(list.children.length, 1);
    const [label, field] = list.children[0].children;
    assert.ok(label.textContent.includes('<img src=x>.png'));
    assert.equal(field.name, 'alt_texts[0]');
    assert.equal(field.maxLength, 255);
    input.files = [];
    handlers.change();
    assert.equal(list.children.length, 0);
});
