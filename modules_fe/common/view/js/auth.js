'use strict';

window.CarsFE = window.CarsFE || {};
window.CarsFE.auth = {
	currentUser: null,
	
	ready: ready().then(async e => {
		this.currentUser = await CarsBE.auth.getUser();
		
		if (!this.currentUser) {
			document.body.classList.add('priv-guest');
			return;
		}
		
		document.body.classList.add('priv-logged');
		
		if (this.currentUser.priv === 'admin') {
			document.body.classList.add('priv-admin');
		}
	})
};
