import HeaderCtrl from './controller/HeaderCtrl.class.js';

var headerMod = angular.module('jjcars.comp.header', []);
headerMod.component('jjcHeader', {
	controller: ['AppConstants', 'Auth', '$scope', '$location', HeaderCtrl],
	templateUrl: '/modules/components/header/view/header.html'
});

export default headerMod;
