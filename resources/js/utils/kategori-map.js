export default function loadKategoriMap() {
    try {
        const raw = document.getElementById("kategori-map")?.textContent;
        const arr = JSON.parse(raw || "[]");
        const map = {};
        arr.forEach(k => { map[k.id] = k.nama_kategori; });
        return map;
    } catch {
        return {};
    }
}
