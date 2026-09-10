export function formatRupiah(value) {
    const number = Number(value || 0);
    return "Rp" + number.toLocaleString("id-ID");
}

export function cleanNumber(value = "") {
    const clean = value.toString().replace(/\D/g, "");
    return Number(clean || 0);
}

export function maskRupiah(input) {
    const clean = cleanNumber(input.value);
    input.value = clean ? new Intl.NumberFormat("id-ID").format(clean) : "";
    input.dataset.raw = clean;
    return clean;
}
