const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

const classes = (...names) => {
    const values = new Set(names);
    return { add: (...items) => items.forEach(item => values.add(item)), remove: (...items) => items.forEach(item => values.delete(item)), contains: item => values.has(item) };
};
const document = { addEventListener() {}, getElementById: () => null, querySelectorAll: () => [] };
const context = { window: {}, document, setTimeout: () => 0, clearTimeout() {} };
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../../public/js/form-validation.js'), 'utf8'), context);
const validator = context.window.FormValidator;
function field(options = {}) {
    const attrs = {};
    const feedback = { id: 'feedback', classList: classes('invalid-feedback') };
    return {
        type: 'text', tagName: 'INPUT', value: '', dataset: {}, validity: {},
        classList: classes(), nextElementSibling: feedback, offsetWidth: 100,
        minLength: -1, maxLength: -1,
        hasAttribute(name) { return Object.hasOwn(attrs, name); },
        setAttribute(name, value) { attrs[name] = value; },
        getAttribute(name) { return attrs[name] || null; },
        removeAttribute(name) { delete attrs[name]; },
        closest: () => null, addEventListener() {},
        ...options,
    };
}
function invalid(input, message) {
    assert.equal(validator.validateField(input), false);
    assert.match(input.nextElementSibling.textContent, message);
    assert.equal(input.getAttribute('aria-invalid'), 'true');
}

invalid(field({ value: 'a'.repeat(51), maxLength: 50 }), /Maksimal 50 karakter/);
assert.equal(validator.validateField(field({ value: '' })), true);
invalid(field({ value: '', classList: classes('required-field'), dataset: { validate: 'email' } }), /wajib diisi/);
invalid(field({ type: 'email', value: 'bad', validity: { typeMismatch: true } }), /email tidak valid/);
invalid(field({ type: 'url', value: 'bad', validity: { typeMismatch: true } }), /URL tidak valid/);
invalid(field({ value: 'abc', validity: { patternMismatch: true }, dataset: { patternMessage: 'NIK hanya boleh berisi angka.' } }), /NIK hanya/);
invalid(field({ type: 'number', value: '', validity: { badInput: true } }), /angka/);
invalid(field({ type: 'number', value: '1.5', validity: { stepMismatch: true } }), /kelipatan 1/);
invalid(field({ type: 'date', value: '2099-01-01', max: '2026-11-25', validity: { rangeOverflow: true } }), /2026-11-25/);
invalid(field({ value: '12xyz', dataset: { validate: 'number' } }), /angka/);
invalid(field({ value: '-1', classList: classes('rupiah-mask') }), /minimal 0/);
assert.equal(validator.validateField(field({ value: '1.000.000.000', maxLength: 11, classList: classes('rupiah-mask') })), true);
assert.equal(validator.validateField(field({ value: '0', classList: classes('rupiah-mask') })), true);
invalid(field({ type: 'checkbox', value: '1', classList: classes('required-field'), validity: { valueMissing: true } }), /wajib/);
assert.equal(validator.validateField(field({ value: '', disabled: true, classList: classes('required-field') })), true);
invalid(field({ type: 'file', value: 'photo.jpg', dataset: { maxBytes: 2097152 }, files: [{ name: 'photo.jpg', type: 'image/jpeg', size: 2097153 }] }), /2 MB/);
invalid(field({ type: 'file', value: 'doc.pdf', accept: 'image/jpeg,image/png', files: [{ name: 'doc.pdf', type: 'application/pdf', size: 10 }] }), /Format file/);
invalid(field({ value: 'bad', validity: { customError: true }, validationMessage: 'Screenshot terlalu besar.' }), /Screenshot terlalu besar/);
invalid(field({ type: 'date', value: '2026-01-01', dataset: { afterField: 'tanggal_masuk' }, form: { elements: { namedItem: () => ({ value: '2026-02-01' }) } } }), /sebelum tanggal masuk/);
invalid(field({ type: 'number', value: '100', dataset: { greaterThan: 'harga_minimum' }, form: { elements: { namedItem: () => ({ value: '100' }) } } }), /lebih besar/);
invalid(field({ type: 'password', name: 'new_password_confirmation', value: 'different', form: { elements: { namedItem: name => {
    assert.equal(name, 'new_password');
    return { value: 'secret' };
} } } }), /Konfirmasi password tidak cocok/);
const phone = field({ value: '0812-3456-7890', maxLength: 12, validity: { patternMismatch: true }, dataset: { maxDigits: 12 } });
phone.setAttribute('data-phone-validation', '');
assert.equal(validator.validateField(phone), true);
phone.value = '0812-3456-78901';
invalid(phone, /maksimal 12 digit/);

const optional = field({ value: 'too long', maxLength: 3 });
const form = { querySelectorAll: selector => {
    assert.equal(selector, 'input, select, textarea');
    return [optional];
} };
assert.equal(validator.validateForm(form, { scrollToError: false }), false);
optional.value = 'OK';
assert.equal(validator.validateForm(form), true);
assert.equal(optional.getAttribute('aria-invalid'), null);

let submitHandler;
let submitRegistrations = 0;
const submittingForm = {
    setAttribute() {},
    querySelectorAll: selector => selector === 'input, select, textarea' ? [optional] : [],
    addEventListener(event, handler, capture) {
        if (event === 'submit') {
            assert.equal(capture, true);
            submitHandler = handler;
            submitRegistrations++;
        }
    },
};
validator.initForm(submittingForm, { scrollToError: false });
validator.initForm(submittingForm);
assert.equal(submitRegistrations, 1);
optional.value = 'too long';
let prevented = false;
let stopped = false;
submitHandler.call(submittingForm, {
    preventDefault() { prevented = true; },
    stopImmediatePropagation() { stopped = true; },
});
assert.equal(prevented, true);
assert.equal(stopped, true);

// Frontend limits must preserve the long purchase notes when edited in QC.
const qcView = fs.readFileSync(path.join(__dirname, '../../resources/views/admin/dataQC_form.blade.php'), 'utf8');
assert.match(qcView, /name="kondisi_lain_lain" maxlength="5000"/);
console.log('form-validation: OK');
