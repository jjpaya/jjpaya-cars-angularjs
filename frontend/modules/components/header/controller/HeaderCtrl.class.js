export default class HeaderCtrl {
	constructor(AppConstants, Auth, Cars, $scope, $location, $route, $window, toastr) {
		this.pageBrand = AppConstants.pageBrand;
		this.user = Auth.currentUser;
		this._Auth = Auth;
		this._Cars = Cars;
		this._$loc = $location;
		this._$scope = $scope;
		this._$route = $route;
		this._$win = $window;
		this._toastr = toastr;
		
		if ('firebase' in $window) {
			this.fbaseGoogle = new firebase.auth.GoogleAuthProvider();
			this.fbaseGhub = new firebase.auth.GithubAuthProvider();
		} else {
			this.fbaseGoogle = null;
			this.fbaseGhub = null;
		}

		this.curModal = null;
		this.loginForm = {};
		this.loginFailed = false;
		this.registerForm = {};
		this.registerFailed = false;
		this.searchQuery = '';
		this.carSuggestions = [];
		this.requestInProgress = false;
		this.verifySent = null;
		this.verifyErr = null;
		
		console.log(this, Auth.currentUser);
		
		$scope.$watch(() => Auth.currentUser, user => {
			console.log('usrupd', user);
			this.user = user;
		});
	}
	
	get viewPath() {
		return '/modules/components/header/view';
	}
	
	get userAvatarImg() {
		return (this.user || {}).img || this.viewPath + '/img/user-ph.png';
	}
	
	currentp(route) {
		return {current: this._$loc.path().startsWith('/' + route)};
	}
	
	modal(name) {
		this.curModal = name;
	}
	
	async updateCarSearchSuggestions() {
		if (!this.searchQuery) {
			this.carSuggestions = [];
			return;
		}
		
		if (this.requestInProgress) {
			return;
		}
		
		this.requestInProgress = true;
		this.carSuggestions = await this._Cars.getCarSearchSuggestions(this.searchQuery);
		
		// search query can change before request is finished
		if (!this.searchQuery) {
			this.carSuggestions = [];
		}
		
		this.requestInProgress = false;
		
		this._$scope.$apply();
	}
	
	carSearch(query) {
		this.searchQuery = query;
		this.carSuggestions = [];
		if (!this.searchQuery) {
			this.searchQuery = undefined;
		}
		
		if (this._$loc.path() == '/shop') {
			this._$route.updateParams({containing: this.searchQuery});
		} else {
			this._$loc.url(
				this.searchQuery !== undefined
				? '/shop?containing=' + this._$win.encodeURIComponent(this.searchQuery)
				: '/shop');
		}
	}
	
	async loginGithub() {
		this.requestInProgress = true;
		
		try {
			var result = await firebase.auth().signInWithPopup(this.fbaseGhub);
			var idToken = await result.user.getIdToken();
			
			await this._Auth.loginGithub(idToken);
			this.modal(null);
		} catch (e) {
			this._toastr.error(e.message, 'Error while signing in to Github:');
			console.log(e);
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
	
	async loginGoogle() {
		this.requestInProgress = true;
		
		try {
			var result = await firebase.auth().signInWithPopup(this.fbaseGoogle);
			var idToken = await result.user.getIdToken();
			
			await this._Auth.loginGoogle(idToken);
			this.modal(null);
		} catch (e) {
			this._toastr.error(e.message, 'Error while signing in to Google:');
			console.log(e);
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
	
	async loginLocal() {
		this.loginFailed = false;
		this.requestInProgress = true;
		
		try {
			await this._Auth.loginLocal(
					this.loginForm.username,
					this.loginForm.password,
					this.loginForm.persist);
			this.modal(null);
			this.verifyErr = null;
			this.loginForm.password = "";
		} catch (e) {
			this.loginFailed = e.message;
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
	
	async registerLocal() {
		this.registerFailed = false;
		this.requestInProgress = true;
		
		try {
			await this._Auth.registerLocal(
				this.registerForm.username,
				this.registerForm.email,
				this.registerForm.password);
			this.modal(null);
		} catch (e) {
			this.registerFailed = e.message;
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();		
	}
	
	async logout() {
		this.requestInProgress = true;
		await this._Auth.logout();

		this.requestInProgress = false;
		this.modal(null);
		this._$scope.$apply();
	}
	
	async requestMailVerification() {
		this.requestInProgress = true;
		var res = {ok: false};
		try {
			res = await this._Auth.verifyAccountCreate();
		} catch (err) {
			this.verifyErr = err.message;
		}
		
		this.verifySent = res.ok;
		this.requestInProgress = false;
		this._$scope.$apply();
	}
}
