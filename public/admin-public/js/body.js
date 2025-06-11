/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/s-event.js/index.min.js":
/*!**********************************************!*\
  !*** ./node_modules/s-event.js/index.min.js ***!
  \**********************************************/
/***/ (() => {

// author : LY SARI
// s-event version: 1.0.0
// release date: 2022-02-09
(function () {
  const PREFIX = "s";
  const EVENT = [
    "click",
    "mouseover",
    "mouseout",
    "mousedown",
    "mouseup",
    "mousemove",
    "focus",
    "Keydown",
    "Keyup",
  ];
  const TYPE = ["fn", "link", "open"];
  let FULL_EVENT_ATTRIBUTES = [];
  EVENT.map((event) => {
    TYPE.map((type) => {
      FULL_EVENT_ATTRIBUTES.push(`${PREFIX}-${event}-${type}`);
    });
  });
  const elements = document.querySelectorAll("*");
  elements.forEach((item) => {
    item.getAttributeNames().map((attr) => {
      if (FULL_EVENT_ATTRIBUTES.includes(attr)) {
        const [prefix, event, type] = attr.split("-");
        const value = item.getAttribute(attr);
        item.addEventListener(event, () => {
          switch (type) {
            case "fn":
              value ? Function(value)() : false;
              break;
            case "link":
              value ? (window.location.href = value) : false;
              break;
            case "open":
              value ? window.open(value, "_blank") : false;
              break;
          }
        });
        item.removeAttribute(attr);
      }
    });
  });
})();


/***/ }),

/***/ "./node_modules/s-mask.js/index.min.js":
/*!*********************************************!*\
  !*** ./node_modules/s-mask.js/index.min.js ***!
  \*********************************************/
/***/ (() => {

// author : LY SARI
// s-event version: 1.0.0
// release date: 2022-03-07
(function () {
  "use strict";
  const element = document.querySelectorAll("[s-mask]");
  const maskConvert = (value, arg) => {
    if (value && value.length > 0 && isFinite(value)) {
      let mask = value.toString();
      let mask_result = "";
      let mask_index = 0;
      let arg_mask = arg.match(/#/gm);
      let mask_symbol =
        mask.length >= arg_mask.length ? mask.length : arg_mask.length;
      for (let i = 0; i < mask_symbol; i++) {
        if (arg[i].toLowerCase() == "#") {
          if (mask[i - mask_index]) {
            mask_result += mask[i - mask_index];
          }
        } else {
          mask_symbol++;
          mask_index++;
          mask_result += arg[i];
        }
      }
      return mask_result;
    }
    return value;
  };
  element.forEach((el) => {
    const value = el.innerHTML;
    const mask = el.getAttribute("s-mask");
    el.innerHTML = maskConvert(value, mask);
  });
})();


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./resources/admin/ts/body.js ***!
  \************************************/
feather.replace();
Alpine.start();
$(document).ready(function () {
  // Scroll To Active
  var menu_active = $(".sidebar .sidebar-wrapper .menu-list .menu-item.active");
  var menu_list = menu_active.parents(".menu-list")[0];
  menu_list === null || menu_list === void 0 ? void 0 : menu_list.scrollTo({
    top: menu_active[0].offsetTop - menu_list.clientHeight / 2,
    left: 0,
    behavior: "smooth"
  });
});

__webpack_require__(/*! s-event.js */ "./node_modules/s-event.js/index.min.js");

__webpack_require__(/*! s-mask.js */ "./node_modules/s-mask.js/index.min.js");

window.reloadUrl = function (url) {
  window.location.href = url;
};
})();

/******/ })()
;