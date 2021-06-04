import PageResetPwRequestCtrl from './controller/PageResetPwRequestCtrl.class.js';
import PageResetPwUseCtrl from './controller/PageResetPwUseCtrl.class.js';

var pResetPwMod = angular.module('jjcars.page.resetpw', []);

pResetPwMod.controller('PageResetPwRequestCtrl', [
	'Auth',
	'$scope',
	PageResetPwRequestCtrl
]);

pResetPwMod.controller('PageResetPwUseCtrl', [
	'Auth',
	'$routeParams',
	'$scope',
	'tokenOk',
	PageResetPwUseCtrl
]);

pResetPwMod.config(['$routeProvider', $routeProvider => {
	$routeProvider.when('/resetpw', {
		templateUrl: '/modules/pages/resetpw/view/resetpw_request.html',
		controller: 'PageResetPwRequestCtrl',
		controllerAs: '$ctrl',
		title: 'Recover password'
	});
	
	$routeProvider.when('/resetpw/:uid/:token', {
		templateUrl: '/modules/pages/resetpw/view/resetpw_use.html',
		controller: 'PageResetPwUseCtrl',
		controllerAs: '$ctrl',
		title: 'Set a new password',
		resolve: {
			tokenOk: ['Auth', '$route', (Auth, $route) => {
				return Auth.recoverPasswordTest($route.current.params.uid, $route.current.params.token)
						.then(data => data.ok && data.valid);
			}]
		}
	});
}]);

export default pResetPwMod;