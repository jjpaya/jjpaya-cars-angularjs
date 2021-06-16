export default class PageShopCtrl {
	constructor(Cars, $scope, $route, $routeParams, $location, $window, initialCarsData, brands, totalCars) {
		this._Cars = Cars;
		this._$scope = $scope;
		this._$route = $route;
		this._$rParams = $routeParams;
		this._$loc = $location;

		this.visibleCars = initialCarsData;
		
		this.populatePageArray(totalCars);
		
		this.brands = brands;
		
		this.currmarkers = [];
		if ('google' in $window) {
			this.map = new google.maps.Map(document.querySelector('.shop-view .gmaps'), {
				center: {lat: 40.416, lng: -3.703},
				zoom: 4.75
			});
		} else {
			this.map = null;
		}
		
		this.setupMarkers();
		
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
		
		this.currmarkers.forEach(m => m.setMap(null));
		this.visibleCars = await C.getCars(this.currentFilters);
		this.setupMarkers();
		
		this._$scope.$apply();
	}
	
	setupMarkers() {
		if (!this.map) {
			return;
		}
		
		this.visibleCars.forEach((car, i) => {
			if (!(car.lat && car.lon)) {
				return;
			}
			
			var pos = new google.maps.LatLng(car.lat, car.lon);
			var marker = new google.maps.Marker({
			    position: pos,
			    title: car.brand_name + ' ' + car.model,
			    label: (i + 1).toString()
			});
			
			var infowindow = new google.maps.InfoWindow({
				content:
				`<div class="map-info">
					<h4>${car.brand_name + ' ' + car.model}</h4>
					<span>${car.description}, ${car.price_eur_cent / 100}€</span>
				</div>`
			});
			
			marker.addListener("click", ((iw, marker) => () => {
				iw.open(this.map, marker);
			})(infowindow, marker));
			
			this.currmarkers.push(marker);
			marker.setMap(this.map);
		});
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
