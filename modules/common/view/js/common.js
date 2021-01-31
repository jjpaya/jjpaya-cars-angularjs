'use strict';

// jQuery 2.0
window.$$ = sel => document.querySelectorAll(sel);
window.$$.ajax = (...args) => fetch(...args);
window.ready = f => window.addEventListener('load', f);
