export default class FavoriteBtnCtrl {
	constructor(Cars, Auth, Starred, $scope, toastr) {
		this._Cars = Cars;
		this._Auth = Auth;
		this._Starred = Starred;
		this._$scope = $scope;
		this._toastr = toastr;
		
		this.STATE = {
			LOADING: 'loading',
			NOTLOGGED: 'nouser',
			FAVVED: 'favved',
			UNFAVVED: 'unfavved'
		};
		
		this.btnState = this.STATE.LOADING;
		this.toggling = false;
	}
	
	$onInit() {
		this._$scope.$watch(() => this._Auth.currentUser, user => {
			this.loadState();
		});
	}
	
	/*$onChange(c) {
		console.log('ch', c)
	}*/
	
	getDisabled() {
		return this.btnState === this.STATE.LOADING || this.toggling || !this._Auth.currentUser;
	}
	
	getClass() {
		return this.btnState;
	}
	
	isLoading() {
		return this.btnState === this.STATE.LOADING;
	}
	
	async loadState() {
		this.btnState = this.STATE.LOADING;
		
		try {
			this.btnState = (await this._Starred.isCarStarred(this.carObj.car_id))
					? this.STATE.FAVVED
					: this.STATE.UNFAVVED;
		} catch (e) {
			this.btnState = this.STATE.NOTLOGGED;
		}
		
		this._$scope.$apply();
	}
	
	async toggleFav(ev) {
		ev.stopPropagation();
		
		var req = null;
		var newst = null;
		var toast = null;
		
		switch (this.btnState) {
			case this.STATE.FAVVED:
				req = this._Starred.unStarCar(this.carObj.car_id);
				newst = this.STATE.UNFAVVED;
				toast = 'Unmarked';
				break;
				
			case this.STATE.UNFAVVED:
				req = this._Starred.starCar(this.carObj.car_id);
				newst = this.STATE.FAVVED;
				toast = 'Marked';
				break;
				
			default:
				return;
		}
		
		this.toggling = true;
		
		try {
			var res = await req;
			this.btnState = newst;
			this._toastr.success(toast + ' ' + this.carObj.brand_name + ' ' + this.carObj.model + ' as favorite!');
		} catch (e) {
			this._toastr.error(e.message);
		}
		
		this.toggling = false;
		this._$scope.$apply();
	}
}