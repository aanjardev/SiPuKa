const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

function formFixture(withExistingScreenshot = true) {
    const handlers = {};
    const input = { files: [], validationMessage: '', setCustomValidity(message) { this.validationMessage = message; }, reportValidity() {}, addEventListener(name, callback) { handlers[name] = callback; } };
    const remove = withExistingScreenshot ? { checked: false, addEventListener(name, callback) { handlers.remove = callback; } } : null;
    const form = { querySelector(selector) { return selector === '#screenshot' ? input : remove; }, checkValidity() { return !input.validationMessage; }, reportValidity() {}, addEventListener(name, callback, capture) { assert.equal(capture, true); handlers.submit = callback; } };
    vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../../public/js/testimonial-form.js'), 'utf8'), { document: { querySelectorAll() { return [form]; } }, window: { addEventListener(name, callback) { handlers.pageshow = callback; } } });
    return { handlers, input, remove };
}

test('text-only testimonials can submit without a screenshot', () => {
    const { handlers, input } = formFixture(false);
    let prevented = false;
    handlers.submit({ preventDefault() { prevented = true; } });
    assert.equal(prevented, false);
    assert.equal(input.validationMessage, '');
});

test('screenshots above two MB are blocked before the submit lock', () => {
    const { handlers, input } = formFixture();
    input.files = [{ size: 2 * 1024 * 1024 + 1 }];
    let prevented = false;
    handlers.submit({ preventDefault() { prevented = true; } });
    assert.equal(prevented, true);
    input.files = [{ size: 2 * 1024 * 1024 }];
    handlers.change();
    assert.equal(input.validationMessage, '');
});

test('replacement and removal are mutually exclusive and clearing a file restores validity', () => {
    const { handlers, input, remove } = formFixture();
    remove.checked = true;
    input.files = [{ size: 100 }];
    handlers.remove();
    assert.ok(input.validationMessage.includes('bukan keduanya'));
    input.files = [];
    handlers.change();
    assert.equal(input.validationMessage, '');
    handlers.pageshow();
    assert.equal(input.validationMessage, '');
});
