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
			car_admin: '/api/cars/admin',
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
	
	async getTotalCars(options = {}) {
		return (await this._$http({
			method: 'GET',
			url: this.routes.cars_total,
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
	
	async createCar(data) {
		var res = (await this._$http({
			method: 'POST',
			url: this.routes.car_admin,
			data: data
		}).catch(e => e)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async updateCar(data) {
		var res = (await this._$http({
			method: 'PATCH',
			url: this.routes.car_admin,
			data: data
		}).catch(e => e)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
	
	async deleteCar(id) {
		var res = (await this._$http({
			method: 'DELETE',
			url: this.routes.car_admin,
			params: {id}
		}).catch(e => e)).data;
		
		if (!res.ok) {
			throw new Error(res.err);
		}
		
		return res;
	}
}