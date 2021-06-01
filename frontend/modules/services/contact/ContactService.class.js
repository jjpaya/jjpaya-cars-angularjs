export default class ContactService {
	constructor($http) {
		this._$http = $http;

		this.routes = {
			send_message: '/api/contact'
		};
	}
	
	async sendContactMsg(message) {
		return (await this._$http({
			method: 'POST',
			url: this.routes.send_message,
			data: {message}
		}).catch(e => ({data: {ok: false}}))).data;
	}
}