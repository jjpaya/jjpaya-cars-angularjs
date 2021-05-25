export default class CarsService {
	constructor($http, $window) {
		this._$http = $http;
		this._$win = $window;
		
		this.ORDER = {
			DESC_ID: 1,
			DESC_VIEWS: 2,
			DESC_PRICE: 3
		};
		
		this.routes = {
			cars: '/api/cars',
			cars_total: '/api/cars/total',
			brands: '/api/cars/brands',
			brands_total: '/api/cars/brands/total',
			details: '/api/cars/details',
			search: '/api/cars/search',
			starred: '/api/cars/starred'
		}
	}
	
	optionsToQuery(opts) {
		return '?' + Object.keys(opts)
				.map(i => i + '=' + this._$win.encodeURIComponent(opts[i]))
				.join('&');
	}
	
	async getBrands(options = {}) {
		return (await this._$http({
			method: 'GET',
			url: this.routes.brands + this.optionsToQuery(options)
		})).data;
	}
	
	async getCars(options = {}) {
		return (await this._$http({
			method: 'GET',
			url: this.routes.cars + this.optionsToQuery(options)
		})).data;
	}
}