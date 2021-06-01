export default class PageContactCtrl {
	constructor(Auth, Contact, $scope) {
		this._Auth = Auth;
		this._Contact = Contact;
		this._$scope = $scope;
		
		this.requestInProgress = false;
		
		this.contactForm = {};
		this.sendState = null;
	}
	
	get viewPath() {
		return '/modules/pages/contact/view';
	}
	
	async sendContactMsg() {
		this.requestInProgress = true;
		this.sendState = null;
		
		var res = await this._Contact.sendContactMsg(this.contactForm.message);
		this.sendState = res.ok ? 'ok' : 'fail';

		this.requestInProgress = false;
		this._$scope.$apply();
	}
}
