export default class PageCarsCtrl {
	constructor(Auth, Cars, $scope, $route, $filter, initialCarsData, brands, totalCars) {
		this._Auth = Auth;
		this._Cars = Cars;
		this._$scope = $scope;
		this._$filter = $filter;
		
		this.curModal = null;
		this.requestInProgress = false;
		
		this.carToRead = null;
		this.carToDelete = null;
		
		this.carReqErr = null;
		this.carDeletionErr = null;
		
		this.newCarForm = {wheel_power: 'front'};
		
		this.visibleCars = initialCarsData;
		
		this.currentPage = 1;
		this.populatePageArray(totalCars);
		
		this.brands = brands;
		
		$scope.$watch(() => Auth.currentUser, newUser => {
			if (!newUser || !newUser.admin) {
				$route.reload();
			}
		});
	}
	
	get viewPath() {
		return '/modules/pages/cars/view';
	}
	
	capitalize(str) {
		if (!str) { return str; }
		return str.slice(0, 1).toUpperCase() + str.slice(1);
	}
	
	populatePageArray(numItems) {
		this.pages = [];
		var totpages = Math.ceil(numItems / 10);
		for (var i = 0; i < totpages; i++) {
			this.pages.push(i + 1);
		}
		
		if (this.currentPage > totpages) {
			this.currentPage = totpages;
		}
	}
	
	async reloadData() {
		var C = this._Cars;
		var total = await C.getTotalCars();
		this.populatePageArray(total);
		await this.changePage(this.currentPage);
	}
	
	async changePage(pg) {
		var C = this._Cars;
		this.visibleCars = await C.getCars({order: C.ORDER.DESC_ID, page: pg});
		this.currentPage = pg;
		this._$scope.$apply();
	}
	
	modal(mdl) {
		this.curModal = mdl;
	}
	
	createCar() {
		this.carCreationErr = null;
		this.newCarForm = {wheel_power: 'front'};
		this.modal('create');
	}
	
	async createCarConfirm() {
		this.requestInProgress = true;
		
		var carData = angular.copy(this.newCarForm);
		
		// convert stuff to be correctly sent to the backend
		carData.reg_date = this._$filter('date')(carData.reg_date, 'yyyy-MM-dd');
		carData.brand_id -= 0;
		
		try {
			await this._Cars.createCar(carData);
			this.modal(null);
			await this.reloadData();
		} catch (e) {
			this.carReqErr = e.message;
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
	
	readCar(car) {
		this.carToRead = car;
		this.modal('read');
		console.log(car)
	}
	
	updCar(car) {
		this.carReqErr = null;
		
		// copy, to not modify table until sent
		this.newCarForm = angular.copy(car);
		
		// the date must be a date object, not a string
		this.newCarForm.reg_date = new Date(this.newCarForm.reg_date);
		
		// ng-modal expects a string, convert the number to one
		this.newCarForm.brand_id += '';
		this.modal('update');
	}
	
	async updateCarConfirm() {
		this.requestInProgress = true;
		
		var carData = angular.copy(this.newCarForm);
		
		// convert stuff to be correctly sent to the backend
		carData.reg_date = this._$filter('date')(carData.reg_date, 'yyyy-MM-dd');
		carData.brand_id -= 0;
		
		try {
			await this._Cars.updateCar(carData);
			this.modal(null);
			await this.reloadData();
		} catch (e) {
			this.carReqErr = e.message;
			console.log(e);
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
	
	delCar(car) {
		this.carToDelete = car;
		this.carDeletionErr = null;
		this.modal('delete');
	}

	async delCarConfirm() {
		this.requestInProgress = true;
		
		try {
			await this._Cars.deleteCar(this.carToDelete.car_id);
			this.modal(null);
			await this.reloadData();
		} catch (e) {
			this.carDeletionErr = e.message;
			console.log(this, e);
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
}
