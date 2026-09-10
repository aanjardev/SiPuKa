export default class DealButtons {
    constructor({ btnDraft, btnNoDeal, btnDeal, hiddenHargaDeal }) {
        this.btnDraft = btnDraft;
        this.btnNoDeal = btnNoDeal;
        this.btnDeal = btnDeal;
        this.hiddenHargaDeal = hiddenHargaDeal;
    }

    sync(itemsCount) {
        const hasItems = itemsCount > 0;

        if (this.btnDraft) this.btnDraft.disabled = !hasItems;
        if (this.btnNoDeal) this.btnNoDeal.disabled = !hasItems;

        if (!this.btnDeal) return;

        if (!hasItems) {
            this.btnDeal.disabled = true;
            return;
        }

        const val = Number(this.hiddenHargaDeal?.value || 0);
        this.btnDeal.disabled = val <= 0;
    }
}
