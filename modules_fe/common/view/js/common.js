'use strict';

// https://stackoverflow.com/a/6234804
function escapehtml(unsafe) {
	return unsafe
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
}

function mkHTML(tag, opts = {}) {
	var elm = document.createElement(tag);
	for (var i in opts) {
		elm[i] = opts[i];
	}

	return elm;
}

function showModal(data, title = '', opts = {}) {
	if (!opts.title) {
		opts.title = title;
	}

	console.log(data);
	var elems = '';

	if (typeof data === 'string') {
		elems = data;
	} else {
		for (const key in data) {
			var name = key.replace(/_/g, ' ')
					.replace(/^./, key[0].toUpperCase());

			elems +=
			`<div>
				<span>${escapehtml('' + name)}: </span>
				<span>${escapehtml('' + data[key])}</span>
			</div>`;
		}
	}

    $('#dialog')
    	.html(elems)
    	.dialog(opts);
}

function showErrorModal(e, title = 'Error', opts = {}) {
	if (!opts.title) {
		opts.title = title;
	}

	console.log(e);
	$('#dialog')
		.html(e)
		.dialog(opts);
}

window.onhashchange = () => {
	window.location.reload();
};

window.hashStorage = {
	data: {},
	save() {
		if (window.onhashchange) {
			var fhash = window.onhashchange;
			window.onhashchange = null;
			setTimeout(() => {window.onhashchange = fhash;}, 10);
		}

		location.hash = JSON.stringify(this.data);
		if (location.hash.length <= 3) {
			location.hash = "";
		}
	},
	del(key) {
		delete this.data[key];
		this.save();
	},
	set(key, val) {
		this.data[key] = val;
		this.save();
	},
	get(key) {
		return this.data[key];
	}
};

function tryLoadHashStorage() {
	try {
		hashStorage.data = JSON.parse(decodeURI(location.hash).slice(1));
	} catch (e) { }
}

// jQuery 2.0
function addfuncs(arr) {
	const bindEvt = (elm, evt, fn) => elm.addEventListener(evt, e => fn(e, elm));
	const bindEvtOnce = (elms, evt, fn) => {
		const fnref = e => {
			fn(e, e.currentTarget);
			
			// avoid leaks
			for (const el of elms) {
				el.removeEventListener(evt, fnref);
			}
		};
		
		for (const el of elms) {
			el.addEventListener(evt, fnref);
		}
	};
	
	var mkprom = initfn => new Promise(initfn);
	// bind fn to ev on all selected elems
	arr.on = (ev, fn) => (arr.forEach(elm => bindEvt(elm, ev, fn)), arr);
	arr.once = (ev, fn) => fn ? (bindEvtOnce(arr, ev, fn), arr) : mkprom(r => bindEvtOnce(arr, ev, r));
	arr.click = fn => fn ? arr.on('click', fn) : arr.once('click');
	arr.addClass = cl => (arr.forEach(elm => elm.classList.add(cl)), arr);
	arr.hasClass = cl => (arr.reduce((sum, elm) => sum + elm.classList.contains(cl), 0), arr);
	arr.delClass = cl => (arr.forEach(elm => elm.classList.remove(cl)), arr);
	arr.text = v => (arr.forEach(elm => elm.innerText = v), arr);
	arr.html = v => (arr.forEach(elm => elm.innerHTML = v), arr);

	return arr;
}

window.$$ = sel => addfuncs(document.querySelectorAll(sel));
window.$$.fetch = (...args) => fetch(...args);
window.$$.fjson = async (...args) => (await fetch(...args)).json();

var readyp = new Promise(res => window.addEventListener('load', res));
window.ready = f => f ? window.addEventListener('load', f) : readyp;

tryLoadHashStorage();
