export default class HeaderCtrl {
	constructor(AppConstants, Auth, $scope, $location) {
		this.pageBrand = AppConstants.pageBrand;
		this.user = Auth.currentUser;
		this._Auth = Auth;
		this._$loc = $location;
		this._$scope = $scope;

		this.curModal = null;
		this.loginForm = {};
		this.loginFailed = false;
		this.registerForm = {};
		this.registerFailed = false;
		this.requestInProgress = false;
		
		console.log(this, Auth.currentUser);
		
		$scope.$watch(() => Auth.currentUser, user => {
			console.log('usrupd', user);
			this.user = user;
		});
	}
	
	get viewPath() {
		return '/modules/components/header/view';
	}
	
	currentp(route) {
		return {current: this._$loc.path().startsWith('/' + route)};
	}
	
	modal(name) {
		this.curModal = name;
	}
	
	async loginLocal() {
		this.loginFailed = false;
		this.requestInProgress = true;
		
		try {
			await this._Auth.loginLocal(this.loginForm.username, this.loginForm.password);
			this.modal(null);
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
}
