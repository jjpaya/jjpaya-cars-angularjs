export default class AddToCartBtnCtrl {
	constructor(Cart, $scope, toastr) {
		this._Cart = Cart;
		this._$scope = $scope;
		this._toastr = toastr;
		
		//this.carObj = null;
	}
	
	$onInit() {
		//this.loadState();
	}
	
	addToCart(ev) {
		ev.stopPropagation();
		
		this._Cart.addCarToCart(this.carObj);
		this._toastr.success('Item added to cart!');
	}
}
