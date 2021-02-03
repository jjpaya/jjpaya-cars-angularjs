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

function showModal(data, title = '') {
	console.log(data);
	var elems = '';
	
	for (const key in data) {
		var name = key.replace(/_/g, ' ')
				.replace(/^./, key[0].toUpperCase());
		
		elems +=
		`<div>
			<span>${escapehtml('' + name)}: </span>
			<span>${escapehtml('' + data[key])}</span>
		</div>`;
	}
	
    $('#dialog')
    	.attr('title', title)
    	.html(elems)
    	.dialog();
}

function showErrorModal(e, title = 'Error') {
	console.log(e);
	$('#dialog')
		.attr('title', title)
		.html(e)
		.dialog();
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
