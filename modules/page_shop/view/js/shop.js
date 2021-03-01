'use strict';

var shoplist = null;
var shopmap = null;

function populateShop() {
	$$.fjson('/cars/api/cars')
	.then(cars => {
		console.log(cars);
		for (var car of cars) {
			var img = car.imgs[0] || '/modules/page_cars/view/img/placeholder.png';
			
			var el = mkHTML('div', {
				className: 'l-item',
				onclick: (carid => e => {
					console.log(carid);
				})(car.car_id)
			});
			
			el.appendChild(mkHTML('img', {src: img}));
			
			var info = mkHTML('div');
			info.appendChild(mkHTML('h4', {
				innerText: car.brand_name + ' ' + car.model + ' (' + car.price_eur_cent / 100 + '€)'
			}));
			info.appendChild(mkHTML('hr'));
			
			info.appendChild(mkHTML('span', {
				innerText: car.description
			}));
			
			el.appendChild(info);
			
			shoplist.appendChild(el);
		}
	})
}

function initMap() {
	shopmap = new google.maps.Map($$('.shop-view .gmaps')[0], {
		center: {lat: 40.416, lng: -3.703},
		zoom: 4.75
	});
}

function initShop() {
	shoplist = $$('.shop-view > .listing')[0];
}

ready(() => {
	initShop();
	populateShop();
})