'use strict';

window.CarsBE = window.CarsBE || {};

window.CarsBE.auth = {
	routes: {
		get_user: '/api/auth/info',
		login_local: '/api/auth/login/local',
		register_local: '/api/auth/register/local'
	},
	
	async getUser() {
		try {
			return await $$.fjson(this.routes.get_user);
		} catch (e) {
			console.error(e);
			return null;
		}
	},
	
	async loginLocal(user, pass) {
		try {
			return await $$.fjson(this.routes.login_local, {
				method: 'POST',
				body: JSON.stringify({user, pass})
			});
		} catch (e) {
			console.error(e);
			return {res: 'err', err: e};
		}
	},
	
	async registerLocal(user, email, pass) {
		try {
			return await $$.fjson(this.routes.register_local, {
				method: 'POST',
				body: JSON.stringify({user, email, pass})
			});
		} catch (e) {
			console.error(e);
			return {res: 'err', err: e};
		}
	} 
};
