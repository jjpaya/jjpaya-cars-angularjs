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

window.hashStorage = {
	data: {},
	save() {
		location.hash = JSON.stringify(this.data);
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
		hashStorage.data = JSON.parse(location.hash.slice(1));
	} catch (e) { }
}

function deselectable() {
	setTimeout(checked => this.checked = !checked, 0, this.checked);
}

// jQuery 2.0
function addfuncs(arr) {
	const bindEvt = (elm, evt, fn) => elm.addEventListener(evt, e => fn(e, elm));

	// bind fn to ev on all selected elems
	arr.on = (ev, fn) => (arr.forEach(elm => bindEvt(elm, ev, fn)), arr);
	arr.click = fn => arr.on('click', fn);
	
	return arr;
}

window.$$ = sel => addfuncs(document.querySelectorAll(sel));
window.ready = f => window.addEventListener('load', f);

window.$$.fetch = (...args) => fetch(...args);
window.$$.fjson = async (...args) => (await fetch(...args)).json();

tryLoadHashStorage();
