'use strict';

var shoplist = null;
var shopfooter = null;
var shopmap = null;
var currmarkers = [];
var currpage = 1;
// google.maps.Marker, LatLng addListener
function populateShop(page = 1) {
	currpage = page;
	shoplist.innerHTML = "";
	currmarkers.forEach(m => m.setMap(null));
	currmarkers = [];
	populateFooter();
	
	$$.fjson('/cars/api/cars?page=' + page)
	.then(cars => {
		console.log(cars);
		var i = (page - 1) * 10;
		for (var car of cars) {
			i++;
			var img = (car.imgs[0] || {}).path || '/modules/page_cars/view/img/placeholder.png';
			var pos = null;
			
			if (car.lat && car.lon) {
				pos = new google.maps.LatLng(car.lat, car.lon);
			}
			
			var el = mkHTML('div', {
				className: 'l-item',
				onclick: (carid => e => {
					console.log(carid);
				})(car.car_id)
			});
			
			el.appendChild(mkHTML('div', {
				className: 'c-img',
				style: 'background-image: url("' + img +'");',
				onclick: (carid => e => {
					console.log(carid);
				})(car.car_id)
			}));
			
			var info = mkHTML('div');
			info.appendChild(mkHTML('h4', {
				innerText: i + '. ' + car.brand_name + ' ' + car.model + ' (' + car.price_eur_cent / 100 + '€)'
			}));
			info.appendChild(mkHTML('hr'));
			
			info.appendChild(mkHTML('span', {
				innerText: car.description
			}));
			
			el.appendChild(info);
			shoplist.appendChild(el);
			
			if (pos) {
				var marker = new google.maps.Marker({
				    position: pos,
				    title: car.brand_name + ' ' + car.model,
				    label: i.toString()
				});
				
				var infowindow = new google.maps.InfoWindow({
					content:
					`<div class="map-info">
						<h4>${car.brand_name + ' ' + car.model}</h4>
						<span>${car.description}, ${car.price_eur_cent / 100}€</span>
					</div>`
				});
				
				marker.addListener("click", ((iw, marker) => () => {
					iw.open(shopmap, marker);
				})(infowindow, marker));
				
				currmarkers.push(marker);
				marker.setMap(shopmap);
			}
		}
	});
}

async function populateFooter() {
	shopfooter.innerHTML = "";
	var total = await $$.fjson('/cars/api/cars/total');
	var pages = Math.ceil(total / 10);
	console.log(total, pages);

	if (currpage != 1) {
		shopfooter.appendChild(mkHTML('div', {
			className: 'page-btn',
			innerText: '|<',
			onclick: e => { populateShop(1); }
		}));
		
		shopfooter.appendChild(mkHTML('div', {
			className: 'page-btn',
			innerText: '<',
			onclick: e => { populateShop(currpage - 1); }
		}));
	}
	
	for (var i = 0; i < pages; i++) {
		shopfooter.appendChild(mkHTML('div', {
			className: 'page-btn ' + (i + 1 == currpage ? 'active' : ''),
			innerText: i + 1,
			onclick: (p => e => { populateShop(p); })(i + 1)
		}));
	}
	
	if (currpage != pages) {
		shopfooter.appendChild(mkHTML('div', {
			className: 'page-btn',
			innerText: '>',
			onclick: e => { populateShop(currpage + 1); }
		}));
		
		shopfooter.appendChild(mkHTML('div', {
			className: 'page-btn',
			innerText: '>|',
			onclick: e => { populateShop(pages); }
		}));
	}
}

function initMap() {
	shopmap = new google.maps.Map($$('.shop-view .gmaps')[0], {
		center: {lat: 40.416, lng: -3.703},
		zoom: 4.75
	});
}

async function initShop() {
	shopfooter = $('.shop-view > .view-footer')[0];
	shoplist = $$('.shop-view > .listing')[0];
	await populateFooter();
}

ready(async () => {
	await initShop();
	populateShop();
})