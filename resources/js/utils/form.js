export function syncHiddenRaw(input) {
    const clean = input.dataset.raw || input.value;
    input.value = clean.toString().replace(/\D/g, "");
}
