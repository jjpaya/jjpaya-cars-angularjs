export default class AuthService {
	constructor($http, $window) {
		this._$http = $http;
		this._$win = $window;
		this.currentUser = null;
		this.sessionType = 'none';
		this.tryLoadStoredUserInfo();
		
		this.routes = {
			get_user: '/api/auth/info',
			login_local: '/api/auth/login/local',
			login_google: '/api/auth/login/google',
			login_github: '/api/auth/login/github',
			register_local: '/api/auth/register',
			logout: '/api/auth/logout',
			recover_pass: '/api/auth/recover',
			verify_acc: '/api/auth/verify'
		};
	}
	
	tryLoadStoredUserInfo() {
		var w = this._$win;
		try {
			this.currentUser = w.JSON.parse(w.atob(w.localStorage.getItem('jwtuser').split('.')[1].replace(/_/g, '/')));
			this.sessionType = this.currentUser.stype;
		} catch (e) {
			this.sessionType = 'none';
		}
		
		if (this.sessionType === 'none') {
			this.currentUser = null;
		}
	}
	
	async updateCurrentUserInfo() {
		var res = (await this._$http({
			method: 'GET',
			url: this.routes.get_user
		}).catch(r => r)).data;
		
		localStorage.setItem('jwtuser', res.data);
		this.tryLoadStoredUserInfo();
		
		return this.currentUser;
	}
	
	async loginLocal(username, pass, persist) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.login_local,
			data: {username, pass, persist}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		localStorage.setItem('jwtuser', res.data);
		this.tryLoadStoredUserInfo();
		
		return res;
	}

	async loginGoogle(idToken) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.login_google,
			data: {idToken}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		localStorage.setItem('jwtuser', res.data);
		this.tryLoadStoredUserInfo();
		
		return res;
	}

	async loginGithub(idToken) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.login_github,
			data: {idToken}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		localStorage.setItem('jwtuser', res.data);
		this.tryLoadStoredUserInfo();
		
		return res;
	}
	
	async registerLocal(username, email, pass) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.register_local,
			data: {username, email, pass}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		localStorage.setItem('jwtuser', res.data);
		this.tryLoadStoredUserInfo();
		
		return res;
	}
	
	async logout() {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.logout,
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		localStorage.removeItem('jwtuser');
		this.tryLoadStoredUserInfo();
	}
	
	async recoverPasswordCreate(email) {
		var res = (await this._$http({
			method: 'PUT',
			url: this.routes.recover_pass,
			data: {email}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async recoverPasswordTest(uid, token) {
		var res = (await this._$http({
			method: 'GET',
			url: this.routes.recover_pass,
			params: {uid, token}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async recoverPasswordUse(uid, token, newpass) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.recover_pass,
			data: {uid, token, newpass}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async verifyAccountCreate() {
		if (!this.currentUser) {
			throw new Error('Not logged in!');
		}
		
		var res = (await this._$http({
			method: 'PUT',
			url: this.routes.verify_acc
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async verifyAccountTest(uid, token) {
		var res = (await this._$http({
			method: 'GET',
			url: this.routes.verify_acc,
			params: {uid, token}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async verifyAccountUse(uid, token) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.verify_acc,
			data: {uid, token}
		}).catch(r => r)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
}
