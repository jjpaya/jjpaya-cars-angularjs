export default class PageResetPwRequestCtrl {
	constructor(Auth, $scope) {
		this._Auth = Auth;
		this._$scope = $scope;

		this.requestErr = 'Error while requesting a password reset.';
		this.requestOk = null;
		this.requestInProgress = false;
		
		this.recoverForm = {};
		
		console.log(this);
	}
	
	async requestPasswordReset() {
		var res = {ok: false};
		var usr = null;
		
		this.requestInProgress = true;
		try {
			res = await this._Auth.recoverPasswordCreate(this.recoverForm.email);
		} catch (e) {
			if (e.message == '1644') { e.message = 'The user doesn\'t exist'; }
			this.requestErr = 'Couldn\'t request a password reset. ' + e.message;
		}

		this.requestInProgress = false;
		this.requestOk = res.ok;
		this._$scope.$apply();
	}
}
