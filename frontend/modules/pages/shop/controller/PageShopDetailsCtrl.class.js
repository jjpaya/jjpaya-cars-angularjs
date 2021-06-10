export default class PageShopDetailsCtrl {
	constructor($window, info) {
		this._$win = $window;
		this.car = info.car;
		this.related = info.related;
		
		console.log(this);
	}
	
	get viewPath() {
		return '/modules/pages/shop/view';
	}
	
	capitalize(str) {
		if (!str) { return str; }
		return str.slice(0, 1).toUpperCase() + str.slice(1);
	}
	
	goBack() {
		this._$win.history.back();
	}
}
