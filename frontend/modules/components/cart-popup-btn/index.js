import CartPopupBtnCtrl from './controller/CartPopupBtnCtrl.class.js';

var cartPopupBtnMod = angular.module('jjcars.comp.cart-popup-btn', []);
cartPopupBtnMod.component('jjcCartPopupBtn', {
	controller: [
		'Cart',
		'$location',
		'toastr',
		CartPopupBtnCtrl
	],
	templateUrl: '/modules/components/cart-popup-btn/view/cart_popup_btn.html'
});

export default cartPopupBtnMod;
