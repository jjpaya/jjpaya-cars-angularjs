import FavoriteBtnCtrl from './controller/FavoriteBtnCtrl.class.js';

var favBtnMod = angular.module('jjcars.comp.favorite-btn', []);
favBtnMod.component('jjcFavoriteBtn', {
	controller: [
		'Cars',
		'Auth',
		'Starred',
		'$scope',
		'toastr',
		FavoriteBtnCtrl
	],
	templateUrl: '/modules/components/favorite-btn/view/favorite_btn.html',
	bindings: {
		carObj: '<'
	}
});

export default favBtnMod;
