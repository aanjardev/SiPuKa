export default class ItemForm {
    constructor(fieldMap) {
        this.fieldMap = fieldMap;
    }

    getEl(key) {
        return document.getElementById(this.fieldMap[key]);
    }

    collect() {
        const data = {};
        Object.keys(this.fieldMap).forEach(key => {
            const el = this.getEl(key);
            data[key] = el ? el.value : "";
        });
        return data;
    }

    populate(data = {}) {
        Object.keys(this.fieldMap).forEach(key => {
            const el = this.getEl(key);
            if (el) el.value = data[key] ?? "";
        });
    }

    clear() {
        this.populate({});
    }
}
