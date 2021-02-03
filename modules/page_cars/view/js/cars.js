'use strict';

function readModal(id, ev) {
	$$.fjson(`/cars/read/${id}`)
		.then(d => showModal(d, 'Read Car Listing'))
		.catch(d => showErrorModal(d, 'Error'));
}

function bindReadEvents() {
	$$('.read-car-btn')
		.click((e, elm) => readModal(elm.getAttribute('data-tid'), e));
}

function setupTable() {
	$('#car-table').DataTable();
}

ready(() => {
	bindReadEvents();
	setupTable();
});
