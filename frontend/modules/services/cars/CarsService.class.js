export default class CarsService {
	constructor($http) {
		this._$http = $http;

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
	
	async getBrands(options = {}) {
		return (await this._$http({
			method: 'GET',
			url: this.routes.brands,
			params: options
		})).data;
	}
	
	async getCars(options = {}) {
		return (await this._$http({
			method: 'GET',
			url: this.routes.cars,
			params: options
		})).data;
	}
	
	async getCarSearchSuggestions(query) {
		return (await this._$http({
			method: 'GET',
			url: this.routes.search,
			params: {q: query}
		})).data;
	}
}