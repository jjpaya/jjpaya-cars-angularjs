import HeaderCtrl from './controller/HeaderCtrl.class.js';

var headerMod = angular.module('jjcars.comp.header', []);
headerMod.component('jjcHeader', {
	controller: [
		'AppConstants',
		'Auth',
		'Cars',
		'$scope',
		'$location',
		'$route',
		'$window',
		HeaderCtrl
	],
	templateUrl: '/modules/components/header/view/header.html'
});

export default headerMod;
