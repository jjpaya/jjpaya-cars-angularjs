export default class StarredService {
	constructor(Auth, $http) {
		this.starred_url = '/api/cars/starred';
		this._Auth = Auth;
		this._$http = $http;
		
		this.starredCacheUser = null;
		this.starredCache = null;
	}
	
	async getStarredCarIdsArray() {
		var data = (await this._$http({
			method: 'GET',
			url: this.starred_url
		}).catch(e => e)).data;
		
		if (!Array.isArray(data)) {
			throw new Error(data.err);
		}
		
		return data;
	}
	
	async getStarredCarIds() {
		if (!this._Auth.currentUser) {
			throw new Error('Unauthorized');
		}
		
		if (this.starredCacheUser !== this._Auth.currentUser) {
			// refresh cache
			this.starredCache = null;
		}
		
		if (this.starredCache) {
			return this.starredCache;
		}
		
		// this way the same request is reused for all simultaneous function calls
		this.starredCache = this.getStarredCarIdsArray().then(data => {
			this.starredCache = {};
			for (var car of data) {
				this.starredCache[car.car_id] = car;
			}
			
			return this.starredCache;
		});
		
		this.starredCacheUser = this._Auth.currentUser;
		
		var data = await this.starredCache;

		return this.starredCache;
	}
	
	async isCarStarred(id) {
		var starredIds = await this.getStarredCarIds();
		return (id in starredIds);
	}
	
	async starCar(cid) {
		var data = (await this._$http({
			method: 'PUT',
			url: this.starred_url,
			params: {cid}
		}).catch(e => e)).data;
		
		if (!data.ok) {
			throw new Error(data.err);
		}
		
		if (this.starredCache) {
			var curdate = (new Date()).toISOString().replace('T', ' ').slice(0, -5);
			this.starredCache[cid] = {car_id: cid, starred_on: curdate};
		}
		
		return data;
	}
	
	async unStarCar(cid) {
		var data = (await this._$http({
			method: 'DELETE',
			url: this.starred_url,
			params: {cid}
		}).catch(e => e)).data;
		
		if (!data.ok) {
			throw new Error(data.err);
		}
		
		if (this.starredCache) {
			delete this.starredCache[cid];
		}
		
		return data;
	}
}