import PageVerifyCtrl from './controller/PageVerifyCtrl.class.js';

var pVerifyMod = angular.module('jjcars.page.verify', []);

pVerifyMod.controller('PageVerifyCtrl', [
	'Auth',
	'$routeParams',
	'$scope',
	'tokenOk',
	PageVerifyCtrl
]);

pVerifyMod.config(['$routeProvider', $routeProvider => {
	$routeProvider.when('/verify/:uid/:token', {
		templateUrl: '/modules/pages/verify/view/verify.html',
		controller: 'PageVerifyCtrl',
		controllerAs: '$ctrl',
		title: 'Verify Mail',
		resolve: {
			tokenOk: ['Auth', '$route', (Auth, $route) => {
				return Auth.verifyAccountTest($route.current.params.uid, $route.current.params.token)
						.then(data => data.ok && data.valid);
			}]
		}
	});
}]);

export default pVerifyMod;