export default class PageCheckoutCtrl {
	constructor(Auth, Cart, $scope, cartDetails) {
		this._Auth = Auth;
		this._Cart = Cart;
		this._$scope = $scope;
		this.cart = cartDetails;
		
		this.requestInProgress = false;
		this.checkoutError = false;
		this.checkoutOk = null;
		this.iid = null;
		
		console.log(this);
	}
	
	get mailVerified() {
		var u = this._Auth.currentUser;
		return 'email_verified' in u ? u.email_verified : true;
	}
	
	get logged() {
		return !!this._Auth.currentUser;
	}
	
	getError() {
		if (!this.logged) {
			return 'You must log in to proceed with the checkout.';
		}
	
		if (!this.mailVerified) {
			return 'You must verify your email to proceed with the checkout.';
		}
		
		if (this.checkoutError) {
			return this.checkoutError;
		}
		
		return null;
	}
	
	async checkout() {
		this.requestInProgress = true;
		
		try {
			var res = await this._Cart.cartCheckout();
			this.checkoutOk = res.ok;
			this.iid = res.iid;
			
			this._Cart.emptyCart();
		} catch (e) {
			this.checkoutError = e.message;
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
}