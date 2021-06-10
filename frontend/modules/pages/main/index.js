import PageMainCtrl from './controller/PageMainCtrl.class.js';

var pMainMod = angular.module('jjcars.page.main', []);

pMainMod.controller('PageMainCtrl', [
	'Cars',
	'$window',
	'$scope',
	'$location',
	'carouselCarData',
	'scrollerBrandData',
	PageMainCtrl
]);

pMainMod.config(['$routeProvider', $routeProvider => {
	$routeProvider.when('/main', {
		templateUrl: '/modules/pages/main/view/main.html',
		controller: 'PageMainCtrl',
		controllerAs: '$ctrl',
		title: 'Main',
		resolve: {
			carouselCarData: ['Cars', Cars => {
				return Cars.getCars({order: Cars.ORDER.DESC_VIEWS});
			}],
			scrollerBrandData: ['Cars', Cars => {
				return Cars.getBrands();
			}]
		}
	}).when('/', {
		redirectTo: '/main'
	});
}]);

export default pMainMod;