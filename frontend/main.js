import AppConstants from './config/constants.js';
import './modules/services/auth/index.js';
import './modules/services/cars/index.js';
import './modules/components/footer/index.js';
import './modules/components/header/index.js';
import './modules/pages/main/index.js';

const requires = [
	'ngRoute',
	'ngSanitize',
	'jjcars.serv.auth',
	'jjcars.serv.cars',
	'jjcars.comp.footer',
	'jjcars.comp.header',
	'jjcars.page.main'
];

var jjcars = angular.module('jjcars', requires);

jjcars.constant('AppConstants', AppConstants);
jjcars.config(['$routeProvider', $routeProvider => {
	$routeProvider
			.otherwise('/main');
	/*		
			.when('/cars', {
				
			})
			
			.when('/cars/create', {
				
			})
			
			.when('/shop', { // $routeParams
				
			})
			
			.when('/shop/:filters', {
				
			})
			
			.when('/shop/view/:carId', {
				
			})
			
			.when('/contact', {
				
			})
			
			.when('/cart', {
				
			})
			
			.when('/verify/:uid/:token', {
				
			})
			
			.when('/resetpw', {
				
			})
			
			.when('/resetpw/:uid/:token', {
				
			});*/
}]);

jjcars.run(['AppConstants', '$rootScope', (AppConstants, $rootScope) => {
	// change title on route switch
	$rootScope.$on('$routeChangeSuccess', (event, newRoute) => {
		$rootScope.setPageTitle(newRoute.title);
	});
	
	// Helper method for setting the page's title
	$rootScope.setPageTitle = (title) => {
		$rootScope.title = '';
		
		if (title) {
			$rootScope.title += title + ' - ';
		}
		
		$rootScope.title += AppConstants.pageBrand;
	};
}]);