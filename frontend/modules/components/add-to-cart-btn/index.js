import AddToCartBtnCtrl from './controller/AddToCartBtnCtrl.class.js';

var addToCartBtnMod = angular.module('jjcars.comp.add-to-cart-btn', []);
addToCartBtnMod.component('jjcAddToCartBtn', {
	controller: [
		'Cart',
		'$location',
		'toastr',
		AddToCartBtnCtrl
	],
	templateUrl: '/modules/components/add-to-cart-btn/view/add_to_cart_btn.html',
	bindings: {
		carObj: '<'
	}
});

export default addToCartBtnMod;
