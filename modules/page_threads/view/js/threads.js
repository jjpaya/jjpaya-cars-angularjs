'use strict';

function showModal(data) {
	console.log(data);
	var elems = '';
	
	for (const key in data) {
		var name = key.replace(/_/g, ' ')
				.replace(/^./, key[0].toUpperCase());
		
		// escape html with option
		elems += `<div>
			<span>${new Option(name).innerHTML}: </span>
			<span>${new Option(data[key]).innerHTML}</span>
		</div>`;
	}
	
    $("#dialog").html(elems).dialog();
}

function showErrorModal(e) {
	console.log(e);
	$("#dialog").html(e).dialog();
}

function readModal(id, ev) {
	$$.ajax(`/threads/read/${id}`)
		.then(r => r.json())
		.then(showModal, showErrorModal);
}

function bindReadEvents() {
	for (const e of $$('.read-thr-btn')) {
		e.onclick = readModal.bind(null, e.getAttribute('data-tid'));
	}
}

ready(bindReadEvents);
