import * as bootstrap from "bootstrap";
import { initGlobalInputMasks } from "./utils/input-masks.js";
import "./utils/clickable-rows.js";
import "./utils/handle-delete.js";
import "./utils/phone-input-validation.js";
import "./utils/search.js";

if (typeof window !== "undefined") {
    window.bootstrap = bootstrap;
}

document.addEventListener("DOMContentLoaded", () => {
    initGlobalInputMasks();
});

export { bootstrap, initGlobalInputMasks };
