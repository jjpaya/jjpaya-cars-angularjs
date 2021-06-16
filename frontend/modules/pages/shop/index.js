import PageShopCtrl from './controller/PageShopCtrl.class.js';
import PageShopDetailsCtrl from './controller/PageShopDetailsCtrl.class.js';

var pShopMod = angular.module('jjcars.page.shop', []);

pShopMod.controller('PageShopCtrl', [
	'Cars',
	'$scope',
	'$route',
	'$routeParams',
	'$location',
	'$window',
	'initialCarsData',
	'brands',
	'totalCars',
	PageShopCtrl
]);

pShopMod.controller('PageShopDetailsCtrl', [
	'$window',
	'info',
	PageShopDetailsCtrl
]);

pShopMod.config(['$routeProvider', $routeProvider => {
	$routeProvider
	
	.when('/shop', {
		templateUrl: '/modules/pages/shop/view/shop.html',
		controller: 'PageShopCtrl',
		controllerAs: '$ctrl',
		title: 'Shop',
		reloadOnSearch: false,
		resolve: {
			gmapsLoader: ['$window', ($window) => {
				// ignore load errors, just wait until it is loaded or not
				return $window.mapLoader.catch(e => e);
			}],
			initialCarsData: ['Cars', '$route', (Cars, $route) => {
				return Cars.getCars($route.current.params);
			}],
			brands: ['Cars', Cars => {
				return Cars.getBrands({limit: 100});
			}],
			totalCars: ['Cars', '$route', (Cars, $route) => {
				return Cars.getTotalCars($route.current.params);
			}]
		}
	})
	
	.when('/shop/view/:carId', {
		templateUrl: '/modules/pages/shop/view/shop_details.html',
		controller: 'PageShopDetailsCtrl',
		controllerAs: '$ctrl',
		title: 'Car details',
		resolve: {
			info: ['Cars', 'Related', '$route', (Cars, Related, $route) => {
				var car = Cars.getCarDetails($route.current.params.carId);
				var rel = car.then(d => Related.getRelatedCarBooks(d));
				return Promise.all([car, rel]).then(d => ({
					car: d[0],
					related: d[1]
				}));
			}]
		}
	});
}]);

export default pShopMod;