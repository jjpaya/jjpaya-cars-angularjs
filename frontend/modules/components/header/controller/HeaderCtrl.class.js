export default class HeaderCtrl {
	constructor(AppConstants, Auth, $scope, $location) {
		this.pageBrand = AppConstants.pageBrand;
		this.user = Auth.currentUser;
		this._Auth = Auth;
		this._$loc = $location;

		this.curModal = null;
		this.loginForm = {};
		this.loginFailed = false;
		this.registerForm = {};
		this.registerFailed = false;
		
		$scope.$watch('Auth.currentUser', user => {
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
		console.log(this);
	}
	
	logInLocal() {
		this._Auth.logInLocal();
	}
	
	registerLocal() {
		
	}
	
	logOff() {
		
	}
}
