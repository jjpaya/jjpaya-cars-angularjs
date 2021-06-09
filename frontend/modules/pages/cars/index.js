import authGuard from '/modules/guards/auth_guard/authGuard.js';
import PageCarsCtrl from './controller/PageCarsCtrl.class.js';

var pCarsMod = angular.module('jjcars.page.cars', []);

pCarsMod.controller('PageCarsCtrl', [
	'Auth',
	'Cars',
	'$scope',
	'$route',
	'$filter',
	'initialCarsData',
	'brands',
	'totalCars',
	PageCarsCtrl
]);

pCarsMod.config(['$routeProvider', $routeProvider => {
	$routeProvider.when('/cars', {
		templateUrl: '/modules/pages/cars/view/cars.html',
		controller: 'PageCarsCtrl',
		controllerAs: '$ctrl',
		title: 'Cars',
		adminsOnly: true,
		resolve: {
			authGuard,
			initialCarsData: ['Cars', Cars => {
				return Cars.getCars({order: Cars.ORDER.DESC_ID});
			}],
			brands: ['Cars', Cars => {
				return Cars.getBrands({limit: 100});
			}],
			totalCars: ['Cars', Cars => {
				return Cars.getTotalCars();
			}]
		}
	});
}]);

export default pCarsMod;