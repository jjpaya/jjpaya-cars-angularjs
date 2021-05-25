import FooterCtrl from './controller/FooterCtrl.class.js';

var footerMod = angular.module('jjcars.comp.footer', []);
footerMod.component('jjcFooter', {
	controller: ['AppConstants', FooterCtrl],
	templateUrl: '/modules/components/footer/view/footer.html'
});

export default footerMod;
