'use strict';

var carousel = null;
var scroller = null;
var scrollerPage = 1;
var scrollerEnd = false;

function populateCarousel() {
	$$.fjson('/cars/api/cars?order=2')
	.then(cars => {
		console.log(cars);
		for (var car of cars) {
			var img = (car.imgs[0] || {}).path || '/modules/page_cars/view/img/placeholder.png';
			
			var el = mkHTML('div', {
				className: 'c-car',
				style: 'background-image: url("' + img +'");',
				onclick: (carid => e => {
					console.log(carid);
					window.location.href = "/shop#" + JSON.stringify({view: carid});
				})(car.car_id)
			});
			
			el.setAttribute('data-carid', car.car_id);
			console.log(el);
			
			el.appendChild(mkHTML('span', {
				innerText: car.brand_name + ' ' + car.model,
				className: 'c-car-name'
			}));
			
			el.appendChild(mkHTML('span', {
				innerText: car.price_eur_cent / 100 + '€',
				className: 'c-car-price'
			}));
			
			carousel.addItem(el);
		}
	})
}

function initCarousel() {
	carousel = new Glider($$('#carousel-track')[0], {
		slidesToShow: 4,
		slidesToScroll: 4,
		draggable: true,
		dots: '#c-dots'
	});
}

function loadMoreBrandsScroller() {
	if (scrollerEnd) {
		return;
	}
	
	$$.fjson('/cars/api/brands?page=' + scrollerPage)
	.then(brands => {
		if (brands.length === 0) {
			scrollerEnd = true;
			return;
		}
		
		console.log(brands);
		
		for (var brand of brands) {
			var el = mkHTML('div', {
				className: 's-brand',
				style: 'background-image: url("' + brand.img +'");',
				onclick: (brandid => e => {
					console.log(brandid);
					window.location.href = "/shop#" + JSON.stringify({
						filters: {brand_id: brandid}
					});
				})(brand.brand_id)
			});
			
			scroller.appendChild(el);
		}
		
	});
	
	scrollerPage++;
}

function initBrandsScroller() {
	scroller = $$('#brand-scroller')[0];
	window.onscroll = ev => {
	    if ((window.innerHeight + window.pageYOffset + 5) >= document.body.offsetHeight) {
	        loadMoreBrandsScroller();
	    }
	};
}

ready(() => {
	initCarousel();
	populateCarousel();
	
	initBrandsScroller();
	loadMoreBrandsScroller();
})