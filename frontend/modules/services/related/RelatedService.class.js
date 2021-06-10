export default class RelatedService {
	constructor(Credentials, $http) {
		this._$http = $http;
		
		this.g_api_key = Credentials.api.google;
		this.g_api_url = 'https://www.googleapis.com/books/v1/volumes';
	}
	
	async getRelatedCarBooks(car) {
		return (await this._$http({
			method: 'GET',
			url: this.g_api_url,
			params: {q: car.brand_name + ' ' + car.model, key: this.g_api_key}
		}).catch(e => e)).data;
	}
}
