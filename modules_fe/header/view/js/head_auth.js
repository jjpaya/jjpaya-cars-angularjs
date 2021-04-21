'use strict';

function hookPopups() {
	$$('.head-auth *[data-target-popup]').click(async (e, elm) => {
		const pps = $$(`.head-auth .ha-popup.${elm.getAttribute('data-target-popup')}-popup`);
		pps.addClass('show');
		await $$('.ha-popup-dismisser').click();
		pps.delClass('show');
	});
}

function initAuthNavbar() {
	hookPopups();
}

ready().then(async () => {
	await CarsFE.auth.ready;
	initAuthNavbar();
});
