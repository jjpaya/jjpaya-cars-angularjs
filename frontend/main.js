import AppConstants from './config/constants.js';
import Credentials from './config/credentials.js';

import './modules/services/auth/index.js';
import './modules/services/cars/index.js';
import './modules/services/contact/index.js';
import './modules/services/related/index.js';
import './modules/services/starred/index.js';

import './modules/directives/stringToNum/stringToNum.js';

import './modules/components/footer/index.js';
import './modules/components/header/index.js';
import './modules/components/favorite-btn/index.js';

import './modules/pages/main/index.js';
import './modules/pages/contact/index.js';
import './modules/pages/verify/index.js';
import './modules/pages/resetpw/index.js';
import './modules/pages/cars/index.js';
import './modules/pages/shop/index.js';
import './modules/pages/err404/index.js';

const requires = [
	'ngRoute',
	'ngSanitize',
	'toastr',
	
	'jjcars.serv.auth',
	'jjcars.serv.cars',
	'jjcars.serv.contact',
	'jjcars.serv.related',
	'jjcars.serv.starred',
	
	'jjcars.directive.strtonum',
	
	'jjcars.comp.footer',
	'jjcars.comp.header',
	'jjcars.comp.favorite-btn',
	
	'jjcars.page.main',
	'jjcars.page.contact',
	'jjcars.page.verify',
	'jjcars.page.resetpw',
	'jjcars.page.cars',
	'jjcars.page.shop',
	'jjcars.page.err404'
];

var jjcars = angular.module('jjcars', requires);

jjcars.constant('AppConstants', AppConstants);
jjcars.constant('Credentials', Credentials);
/*jjcars.config(['$routeProvider', $routeProvider => {
	$routeProvider
			
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
