export default class PageResetPwUseCtrl {
	constructor(Auth, $routeParams, $scope, tokenOk) {
		this._Auth = Auth;
		this._$routeParams = $routeParams;
		this._$scope = $scope;
		this.tokenOk = tokenOk;
		
		this.resetErr = 'Error while resetting password.';
		this.resetOk = null;
		this.requestInProgress = false;
		
		this.resetForm = {};
		console.log(this);
	}
	
	async confirmResetPassword() {
		var res = {ok: false};
		var usr = null;
		
		this.requestInProgress = true;
		try {
			res = await this._Auth.recoverPasswordUse(
					this._$routeParams.uid,
					this._$routeParams.token,
					this.resetForm.password);
		} catch (e) {
			this.resetErr = 'Couldn\'t reset the password: ' + e.message;
		}
		
		this.requestInProgress = false;
		this.resetOk = res.ok;
		this._$scope.$apply();
	}
}
