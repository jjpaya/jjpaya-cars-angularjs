import PageCheckoutCtrl from './controller/PageCheckoutCtrl.class.js';

var pCheckoutMod = angular.module('jjcars.page.checkout', []);

pCheckoutMod.controller('PageCheckoutCtrl', [
	'Auth',
	'Cart',
	'$scope',
	'cartDetails',
	PageCheckoutCtrl
]);

pCheckoutMod.config(['$routeProvider', $routeProvider => {
	$routeProvider.when('/checkout', {
		templateUrl: '/modules/pages/checkout/view/checkout.html',
		controller: 'PageCheckoutCtrl',
		controllerAs: '$ctrl',
		title: 'Checkout',
		resolve: {
			cartNotEmptyGuard: ['Cart', Cart => {
				if (Cart.items.length == 0) {
					return Promise.reject('noitems');
				}
			}],
			cartDetails: ['Cart', Cart => {
				return Cart.getCartDetails().then(d => d.data);
			}]
		}
	});
}]);

export default pCheckoutMod;
