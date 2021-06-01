import PageContactCtrl from './controller/PageContactCtrl.class.js';

var pContactMod = angular.module('jjcars.page.contact', []);

pContactMod.controller('PageContactCtrl', [
	'Auth',
	'Contact',
	'$scope',
	PageContactCtrl
]);

pContactMod.config(['$routeProvider', $routeProvider => {
	$routeProvider.when('/contact', {
		templateUrl: '/modules/pages/contact/view/contact.html',
		controller: 'PageContactCtrl',
		controllerAs: '$ctrl',
		title: 'Contact'
	});
}]);

export default pContactMod;