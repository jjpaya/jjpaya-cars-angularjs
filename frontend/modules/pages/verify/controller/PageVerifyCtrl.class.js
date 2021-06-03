export default class PageVerifyCtrl {
	constructor(Auth, $routeParams, $scope, tokenOk) {
		this._Auth = Auth;
		this._$routeParams = $routeParams;
		this._$scope = $scope;
		this.tokenOk = tokenOk;
		this.verifyErr = 'Error while verifying account.';
		this.verifyOk = null;
		this.requestInProgress = false;
		console.log(this);
	}
	
	async confirmVerifyToken() {
		var res = {ok: false};
		var usr = null;
		
		this.requestInProgress = true;
		try {
			res = await this._Auth.verifyAccountUse(this._$routeParams.uid, this._$routeParams.token);
		} catch (e) {
			this.verifyErr = 'Error occured while verifying token! It may have expired.';
		}
		
		if (res.ok) {
			usr = await this._Auth.updateCurrentUserInfo();
		}
		
		this.requestInProgress = false;
		this.verifyOk = res.ok;
		this._$scope.$apply();
	}
}
