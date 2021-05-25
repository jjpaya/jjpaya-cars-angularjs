export default class PageMainCtrl {
	constructor(Cars, $window, carouselCarData, scrollerBrandData) {
		this._Cars = Cars;
		this.carouselCarData = carouselCarData;
		this.scrollerBrandData = scrollerBrandData;
		this.scrollerEnd = this.scrollerBrandData.length === 0;
		this.scrollerPage = 1;
		
		$window.onscroll = ev => {
			if (($window.innerHeight + $window.pageYOffset + 5) >= $window.document.body.offsetHeight) {
				this.loadMoreBrandsScroller();
			}
		};
		
		/*this.carouselGlider = new $window.Glider($window.document.getElementById('carousel-track'), {
			slidesToShow: 4,
			slidesToScroll: 4,
			draggable: true,
			dots: '#c-dots'
		});*/
		console.log(this);
	}
	
	get viewPath() {
		return '/modules/pages/main/view';
	}
	
	brandClick(brand) {
		console.log(brand);
	}
	
	getCarImgUrl(car) {
		return (car.imgs[0] || {}).path || this.viewPath + '/img/placeholder.png';
	}
	
	carClick(car) {
		console.log(car);
	}
	
	async loadMoreBrandsScroller() {
		if (this.scrollerEnd) {
			return;
		}
		
		var newData = await this._Cars.getBrands({page: ++this.scrollerPage});
		this.scrollerEnd = newData.length === 0;
		
		this.scrollerBrandData.push(...newData);
	}
}
