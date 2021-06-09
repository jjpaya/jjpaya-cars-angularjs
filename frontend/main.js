import AppConstants from './config/constants.js';

import './modules/services/auth/index.js';
import './modules/services/cars/index.js';
import './modules/services/contact/index.js';

import './modules/components/footer/index.js';
import './modules/components/header/index.js';

import './modules/pages/main/index.js';
import './modules/pages/contact/index.js';
import './modules/pages/verify/index.js';
import './modules/pages/resetpw/index.js';
import './modules/pages/cars/index.js';
import './modules/pages/err404/index.js';

const requires = [
	'ngRoute',
	'ngSanitize',
	'toastr',
	
	'jjcars.serv.auth',
	'jjcars.serv.cars',
	'jjcars.serv.contact',
	
	'jjcars.comp.footer',
	'jjcars.comp.header',
	
	'jjcars.page.main',
	'jjcars.page.contact',
	'jjcars.page.verify',
	'jjcars.page.resetpw',
	'jjcars.page.cars',
	'jjcars.page.err404'
];

var jjcars = angular.module('jjcars', requires);

jjcars.constant('AppConstants', AppConstants);
/*jjcars.config(['$routeProvider', $routeProvider => {
	$routeProvider
			
			.when('/shop', { // $routeParams
				
			})

			.when('/shop/view/:carId', {
				
			})
			
			.when('/cart', {
				
			})
			
			.when('/checkout', {
				
			})
	
}]);*/

jjcars.run(['AppConstants', '$rootScope', '$location', 'toastr',
		(AppConstants, $rootScope, $location, toastr) => {
	// Helper method for setting the page's title
	$rootScope.setPageTitle = (title) => {
		$rootScope.title = '';
		
		if (title) {
			$rootScope.title += title + ' - ';
		}
		
		$rootScope.title += AppConstants.pageBrand;
	};
	
	// change title on route switch
	$rootScope.$on('$routeChangeSuccess', (event, newRoute) => {
		$rootScope.setPageTitle(newRoute.title);
	});
	
	// Check reason on route change error and redirect to main page
	$rootScope.$on('$routeChangeError', (event, current, previous, rejection) => {
		$location.path('/');
		
		switch (rejection) {
			case 'unauthorized':
				toastr.error('You need to log in to access that page!', 'Unauthorized');
				break;

			case 'noperms':
				toastr.error('You must be an administrator to access that page!', 'Permission denied');
				break;
				
			default:
				toastr.error('Couldn\'t load the page, try again later!', 'Error');
				break;
		}
	});
}]);
