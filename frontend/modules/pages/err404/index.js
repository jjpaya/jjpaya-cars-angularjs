import PageErr404Ctrl from './controller/PageErr404Ctrl.class.js';

var pE404Mod = angular.module('jjcars.page.err404', []);

pE404Mod.controller('PageErr404Ctrl', [
	PageErr404Ctrl
]);

pE404Mod.config(['$routeProvider', $routeProvider => {
	$routeProvider.otherwise({
		templateUrl: '/modules/pages/err404/view/err404.html',
		controller: 'PageErr404Ctrl',
		controllerAs: '$ctrl',
		title: 'Not found (404)'
	});
}]);

export default pE404Mod;