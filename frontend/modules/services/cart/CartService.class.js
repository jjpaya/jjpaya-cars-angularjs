export default class CartService {
	constructor($window, $http) {
		this._$win = $window;
		this._$http = $http;
		
		this.routes = {
			details: '/api/cart/details',
			checkout: '/api/cart/checkout'
		};
		
		this.items = [];
		
		console.log(this);
		
		try {
			this.items = JSON.parse($window.localStorage.getItem('cart'));
			if (!Array.isArray(this.items)) {
				this.items = [];
			}
		} catch (e) { }
	}
	
	get viewPath() {
		return '/modules/components/cart-popup-btn/view';
	}
	
	saveCart() {
		try {
			this._$win.localStorage.setItem('cart', JSON.stringify(this.items));
		} catch (e) { }
	}
	
	emptyCart() {
		this.items = [];
		this.saveCart();
	}
	
	addCarToCart(car) {
		var i = this.items.find(v => v.car_id == car.car_id);
		if (i) {
			i.qty++;
		} else {
			this.items.push({
				car_id: car.car_id,
				qty: 1,
				name: car.brand_name + ' ' + car.model,
				price: car.price_eur_cent,
				img: (car.imgs[0] || {}).path || this.viewPath + '/img/placeholder.png'
			});
		}
		
		this.saveCart();
	}
	
	delCarFromCart(car) {
		var i = this.items.findIndex(v => v.car_id == car.car_id);
		if (i !== -1) {
			this.items.splice(i, 1);
		}
		
		this.saveCart();
	}
	
	addCarQty(car, num) {
		var i = this.items.findIndex(v => v.car_id == car.car_id);
		if (i !== -1) {
			var item = this.items[i];
			item.qty += num;
			
			if (item.qty <= 0) {
				this.items.splice(i, 1);
			}
			
			this.saveCart();
		}
	}
	
	getTotal() {
		return this.items.reduce((p, i) => p + i.price * i.qty, 0);
	}
	
	async getCartDetails() {
		var items = this.items.map(v => v.car_id + ',' + v.qty);
		
		var data = (await this._$http({
			method: 'GET',
			url: this.routes.details,
			params: {'i[]': items}
		}).catch(e => e)).data;
		
		if (!data.ok) {
			throw new Error(data.err);
		}
		
		console.log('cdetails', data);
		
		return data;
	}
	
	async cartCheckout() {
		var data = (await this._$http({
			method: 'POST',
			url: this.routes.checkout,
			data: this.items.map(i => `${i.car_id},${i.qty}`)
		}).catch(e => e)).data;
		
		if (!data.ok) {
			throw new Error(data.err);
		}
		
		console.log('checkout', data);
		
		return data;
	}
}