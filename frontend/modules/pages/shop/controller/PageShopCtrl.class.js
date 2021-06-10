export default class PageShopCtrl {
	constructor(Cars, $scope, $route, $routeParams, $location, initialCarsData, brands, totalCars) {
		this._Cars = Cars;
		this._$scope = $scope;
		this._$route = $route;
		this._$rParams = $routeParams;
		this._$loc = $location;

		this.visibleCars = initialCarsData;
		
		this.populatePageArray(totalCars);
		
		this.brands = brands;
		
		$scope.$on('$routeUpdate', (event, current) => {
			this.reloadData();
		});
	}
	
	get viewPath() {
		return '/modules/pages/shop/view';
	}

	populatePageArray(numItems) {
		this.pages = [];
		this.totalPages = Math.max(Math.ceil(numItems / 10), 1);
		for (var i = 0; i < this.totalPages; i++) {
			this.pages.push(i + 1);
		}
		
		if (this.currentPage() > this.totalPages) {
			return this.changePage(this.totalPages);
		}
	}
	
	async reloadData() {
		var C = this._Cars;
		var total = await C.getTotalCars(this.currentFilters);
		var change = this.populatePageArray(total);
		if (!change) {
			await this.changePage(this.currentPage());
		}
	}
	
	updateUrlParams() {
		this._$route.updateParams(this.currentFilters);
	}
	
	currentPage() {
		return this._$rParams.page || 1;
	}
	
	async changePage(pg) {
		pg = Math.max(Math.min(pg, this.totalPages), 1);
		
		var C = this._Cars;
		
		if (this.currentPage() !== pg) {
			this._$rParams.page = pg;
			this.updateUrlParams();
		}
		
		this.visibleCars = await C.getCars(this.currentFilters);
		
		this._$scope.$apply();
	}
	
	getCarImgUrl(car) {
		return (car.imgs[0] || {}).path || this.viewPath + '/img/placeholder.png';
	}
	
	cleanObj(obj) {
		var newObj = angular.copy(obj);
		for (var k in newObj) {
			if (newObj[k] === null || newObj[k] === undefined || newObj[k] === 'null'
					|| newObj[k] === 'NaN' || newObj[k] === ''
					|| (typeof newObj[k] === 'number' && isNaN(newObj[k]))) {
				newObj[k] = undefined;
			}
		}
		
		return newObj;
	}
	
	get currentFilters() {
		return this.cleanObj(this._$rParams);
	}
	
	readFilters() {
		this.updateUrlParams();
	}
	
	clearWheelDrive(ev) {
		this._$rParams.wheel_drive = undefined;
		this.readFilters();
	}
	
	showDetails(car) {
		this._$loc.url('/shop/view/' + car.car_id);
	}
}
