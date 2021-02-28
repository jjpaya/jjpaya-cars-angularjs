'use strict';

var carousel = null;

function populateCarousel() {
	$$.fjson('/cars/api/cars')
	.then(cars => {
		console.log(cars);
		for (var car of cars) {
			var img = car.imgs[0] || '/modules/page_cars/view/img/placeholder.png';
			
			var el = mkHTML('div', {
				className: 'c-car',
				style: 'background-image: url("' + img +'");',
				onclick: (carid => e => {
					console.log(carid);
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

ready(() => {
	initCarousel();
	populateCarousel();
})