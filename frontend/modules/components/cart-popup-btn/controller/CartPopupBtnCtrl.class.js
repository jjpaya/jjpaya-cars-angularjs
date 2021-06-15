export default class CartPopupBtnCtrl {
	constructor(Cart, $location, toastr) {
		this._Cart = Cart;
		this._$loc = $location;
		this._toastr = toastr;
		
		this.curModal = null;
		console.log(this);
	}
	
	get items() {
		return this._Cart.items;
	}
	
	modal(mdl) {
		this.curModal = mdl;
	}
	
	goToCheckout() {
		this._$loc.url('/checkout');
		this.modal(null);
	}
	
	clearCart() {
		this._Cart.emptyCart();
	}
	
	addQty(car, qty) {
		this._Cart.addCarQty(car, qty);
	}
	
	removeCar(car) {
		this._Cart.delCarFromCart(car);
	}
	
	getTotal() {
		return this._Cart.getTotal();
	}
}
