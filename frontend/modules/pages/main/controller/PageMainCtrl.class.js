export default class PageMainCtrl {
	constructor(Cars, $window, $scope, $location, carouselCarData, scrollerBrandData) {
		this._Cars = Cars;
		this._$scope = $scope;
		this._$loc = $location;
		
		this.carouselCarData = carouselCarData;
		this.scrollerBrandData = scrollerBrandData;
		this.scrollerEnd = this.scrollerBrandData.length === 0;
		this.scrollerPage = 1;
		
		this.loadingBrands = false;
		
		$window.onscroll = ev => {
			if (($window.innerHeight + $window.pageYOffset + 5) >= $window.document.body.offsetHeight) {
				this.loadMoreBrandsScroller();
			}
		};
		
		console.log(this);
	}
	
	get viewPath() {
		return '/modules/pages/main/view';
	}
	
	brandClick(brand) {
		this._$loc.url('/shop?brand_id=' + brand.brand_id);
	}
	
	getCarImgUrl(car) {
		return (car.imgs[0] || {}).path || this.viewPath + '/img/placeholder.png';
	}
	
	carClick(car) {
		this._$loc.url('/shop/view/' + car.car_id);
	}
	
	async loadMoreBrandsScroller() {
		if (this.scrollerEnd || this.loadingBrands) {
			return;
		}
		
		this.loadingBrands = true;
		var newData = await this._Cars.getBrands({page: ++this.scrollerPage});
		this.scrollerEnd = newData.length === 0;
		
		this.loadingBrands = false;
		this.scrollerBrandData.push(...newData);
		this._$scope.$apply();
	}
}
