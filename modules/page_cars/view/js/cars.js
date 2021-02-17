'use strict';

function readModal(id, ev) {
	$$.fjson(`/cars/read/${id}`)
		.then(d => showModal(d, 'Read Car Listing'))
		.catch(d => showErrorModal(d, 'Error'));
}

function bindButtonEvents() {
	$$('.read-car-btn')
	.click((e, elm) => readModal(elm.getAttribute('data-tid'), e));
	
	$$('.del-car-btn')
	.click((e, elm) => {
		if (elm.classList.contains('deletion-confirmed')) {
			return true;
		}
		
		var val = '';
		var idelm = elm.parentElement.querySelector('[name=id]');
		if (idelm) {
			val = idelm.value;
		}
		
		showModal(`Do you really want to delete this item?: ${val}`, 'Confirm deletion', {
			buttons: {
				Yes() {
					elm.classList.add('deletion-confirmed');
					$(this).dialog('close');
					elm.submit();
				},
				No() {
					$(this).dialog('close');
				}
			}
		});
		
		e.preventDefault();
		return false;
	});
}

function setupTable() {
	$('#car-table').DataTable();
}

ready(() => {
	bindButtonEvents();
	setupTable();
});
