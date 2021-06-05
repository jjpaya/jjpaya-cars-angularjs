export default class PageCarsCtrl {
	constructor(Auth, Cars, $scope, $route, initialCarsData) {
		this._Auth = Auth;
		this._Cars = Cars;
		this._$scope = $scope;
		
		this.curModal = null;
		this.requestInProgress = false;
		
		this.carToRead = null;
		this.carToUpdate = null;
		this.carToDelete = null;
		this.carDeletionErr = null;
		
		this.visibleCars = initialCarsData;
		
		$scope.$watch(() => Auth.currentUser, newUser => {
			if (!newUser || !newUser.admin) {
				$route.reload();
			}
		});
	}
	
	get viewPath() {
		return '/modules/pages/cars/view';
	}
	
	modal(mdl) {
		this.curModal = mdl;
	}
	
	createCar() {
		this.modal('create');
	}
	
	readCar(car) {
		this.carToRead = car;
		this.modal('read');
		console.log(car)
	}
	
	updCar(car) {
		this.carToUpdate = car;
		this.modal('update');
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
		} catch (e) {
			this.carDeletionErr = e.message;
		}
		
		this.requestInProgress = false;
		this._$scope.$apply();
	}
}
