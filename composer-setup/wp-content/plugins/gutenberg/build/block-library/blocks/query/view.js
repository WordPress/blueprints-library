/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 64:
/***/ ((module) => {

module.exports = window["wp"]["interactivityRouter"];

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
/******/ 	/* webpack/runtime/create fake namespace object */
/******/ 	(() => {
/******/ 		var getProto = Object.getPrototypeOf ? (obj) => (Object.getPrototypeOf(obj)) : (obj) => (obj.__proto__);
/******/ 		var leafPrototypes;
/******/ 		// create a fake namespace object
/******/ 		// mode & 1: value is a module id, require it
/******/ 		// mode & 2: merge all properties of value into the ns
/******/ 		// mode & 4: return value when already ns object
/******/ 		// mode & 16: return value when it's Promise-like
/******/ 		// mode & 8|1: behave like require
/******/ 		__webpack_require__.t = function(value, mode) {
/******/ 			if(mode & 1) value = this(value);
/******/ 			if(mode & 8) return value;
/******/ 			if(typeof value === 'object' && value) {
/******/ 				if((mode & 4) && value.__esModule) return value;
/******/ 				if((mode & 16) && typeof value.then === 'function') return value;
/******/ 			}
/******/ 			var ns = Object.create(null);
/******/ 			__webpack_require__.r(ns);
/******/ 			var def = {};
/******/ 			leafPrototypes = leafPrototypes || [null, getProto({}), getProto([]), getProto(getProto)];
/******/ 			for(var current = mode & 2 && value; typeof current == 'object' && !~leafPrototypes.indexOf(current); current = getProto(current)) {
/******/ 				Object.getOwnPropertyNames(current).forEach((key) => (def[key] = () => (value[key])));
/******/ 			}
/******/ 			def['default'] = () => (value);
/******/ 			__webpack_require__.d(ns, def);
/******/ 			return ns;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {

;// CONCATENATED MODULE: external ["wp","interactivity"]
const external_wp_interactivity_namespaceObject = window["wp"]["interactivity"];
;// CONCATENATED MODULE: ./packages/block-library/build-module/query/view.js
/**
 * WordPress dependencies
 */

const isValidLink = ref => ref && ref instanceof window.HTMLAnchorElement && ref.href && (!ref.target || ref.target === '_self') && ref.origin === window.location.origin;
const isValidEvent = event => event.button === 0 &&
// Left clicks only.
!event.metaKey &&
// Open in new tab (Mac).
!event.ctrlKey &&
// Open in new tab (Windows).
!event.altKey &&
// Download.
!event.shiftKey && !event.defaultPrevented;
(0,external_wp_interactivity_namespaceObject.store)('core/query', {
  state: {
    get startAnimation() {
      return (0,external_wp_interactivity_namespaceObject.getContext)().animation === 'start';
    },
    get finishAnimation() {
      return (0,external_wp_interactivity_namespaceObject.getContext)().animation === 'finish';
    }
  },
  actions: {
    *navigate(event) {
      const ctx = (0,external_wp_interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      const {
        queryRef
      } = ctx;
      const isDisabled = queryRef?.dataset.wpNavigationDisabled;
      if (isValidLink(ref) && isValidEvent(event) && !isDisabled) {
        event.preventDefault();

        // Don't announce the navigation immediately, wait 400 ms.
        const timeout = setTimeout(() => {
          ctx.message = ctx.loadingText;
          ctx.animation = 'start';
        }, 400);
        const {
          actions
        } = yield Promise.resolve(/* import() */).then(__webpack_require__.t.bind(__webpack_require__, 64, 23));
        yield actions.navigate(ref.href);

        // Dismiss loading message if it hasn't been added yet.
        clearTimeout(timeout);

        // Announce that the page has been loaded. If the message is the
        // same, we use a no-break space similar to the @wordpress/a11y
        // package: https://github.com/WordPress/gutenberg/blob/c395242b8e6ee20f8b06c199e4fc2920d7018af1/packages/a11y/src/filter-message.js#L20-L26
        ctx.message = ctx.loadedText + (ctx.message === ctx.loadedText ? '\u00A0' : '');
        ctx.animation = 'finish';
        ctx.url = ref.href;

        // Focus the first anchor of the Query block.
        const firstAnchor = `.wp-block-post-template a[href]`;
        queryRef.querySelector(firstAnchor)?.focus();
      }
    },
    *prefetch() {
      const {
        queryRef
      } = (0,external_wp_interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      const isDisabled = queryRef?.dataset.wpNavigationDisabled;
      if (isValidLink(ref) && !isDisabled) {
        const {
          actions
        } = yield Promise.resolve(/* import() */).then(__webpack_require__.t.bind(__webpack_require__, 64, 23));
        yield actions.prefetch(ref.href);
      }
    }
  },
  callbacks: {
    *prefetch() {
      const {
        url
      } = (0,external_wp_interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      if (url && isValidLink(ref)) {
        const {
          actions
        } = yield Promise.resolve(/* import() */).then(__webpack_require__.t.bind(__webpack_require__, 64, 23));
        yield actions.prefetch(ref.href);
      }
    },
    setQueryRef() {
      const ctx = (0,external_wp_interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,external_wp_interactivity_namespaceObject.getElement)();
      ctx.queryRef = ref;
    }
  }
});

})();

/******/ })()
;