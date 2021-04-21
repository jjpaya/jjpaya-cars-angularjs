'use strict';

var gkey = null;
var shoplist = null;
var shopfooter = null;
var shopmap = null;
var currmarkers = [];
var currpage = 1;
var brandmap = {};

async function populateRelatedBooks(car) {
	var query = encodeURIComponent(brandmap[car.brand_id].name + ' ' + car.model);
	
	var books = await $$.fjson(`https://www.googleapis.com/books/v1/volumes?q=${query}&key=${gkey}`);
	console.log(books);
	
	var rel = $$('.details-view > .related')[0];
	
	rel.innerHTML = "";
	
	for (var book of books.items) {
		var inf = book.volumeInfo;
		
		var c = mkHTML('div', {className: 'book'});
		var lnk = mkHTML('a', {href: inf.infoLink});
		
		c.appendChild(lnk);
		
		if (inf.imageLinks) {
			lnk.appendChild(mkHTML('img', {
				src: inf.imageLinks.smallThumbnail
			}));
		}
		
		lnk.appendChild(mkHTML('div', {
			innerText: inf.title,
			className: 'book-title'
		}));
		
		rel.appendChild(c);
	}
	
	new Glider(rel, {
		slidesToShow: 5,
		slidesToScroll: 5,
		draggable: true
	});
}

function switchViews(on_details) {
	var ops = ['add', 'remove'];
	$$('.shop-view')[0].classList[ops[+on_details]]('shown');
	$$('.details-view')[0].classList[ops[+!on_details]]('shown');
	if (!on_details) {
		hashStorage.del('view');
	}
}

async function showDetails(carid) {
	switchViews(true);
	hashStorage.set('view', carid);
	
	var car = await $$.fjson('/cars/api/cars/details?id=' + carid);
	
	console.log('v', car);
	
	$$('.item-name').text(brandmap[car.brand_id].name + ' ' + car.model);
	$$('.item-price').text(car.price_eur_cent / 100 + ' €');
	
	var imgc = $$('.item-imgs')[0];
	imgc.innerHTML = "";
	
	for (var img of car.imgs) {
		imgc.appendChild(mkHTML('img', {
			src: img.path
		}));
	}
	
	if (car.imgs.length == 0) {
		imgc.appendChild(mkHTML('img', {
			src: '/page_cars/img/placeholder.png'
		}));
	}
	
	new Glider(imgc, {
		slidesToShow: 1,
		slidesToScroll: 1,
		draggable: true
	});
	
	var descc = $$('.item-desc')[0];
	descc.innerHTML = "";
	var desckeys = [
		'color', 'created', 'description', 'kms', 'lat', 'lon',
		'model', 'num_plate', 'reg_date', 'wheel_power', 'views'
	];
	
	for (var i of desckeys) {
		var readableKey = i.replace(/_/g, ' ')
				.replace(/^./, i[0].toUpperCase());
				
		descc.appendChild(mkHTML('div', {
			innerText: readableKey + ': ' + car[i]
		}))
	}
	
	await populateRelatedBooks(car);
}

function getFilterQuery() {
	var fil = hashStorage.get('filters') || {};
	var params = Object.keys(fil).map(k => `${k}=${encodeURIComponent(fil[k])}`).join('&');
	if (params.length > 0) {
		params = '&' + params;
	}
	
	return params;
}

function refreshShop() {
	populateShop(currpage);
	updateFilters();
}

function populateShop(page = 1) {
	currpage = page;
	shoplist.innerHTML = "";
	currmarkers.forEach(m => m.setMap(null));
	currmarkers = [];
	populateFooter();
	
	return $$.fjson('/cars/api/cars?page=' + page + getFilterQuery())
	.then(cars => {
		console.log(cars);
		var i = (page - 1) * 10;
		
		if (cars.length == 0) {
			shoplist.appendChild(mkHTML('div', {
				innerText: 'No results.'
			}));
		}
		
		for (var car of cars) {
			i++;
			var img = (car.imgs[0] || {}).path || '/page_cars/img/placeholder.png';
			var pos = null;
			
			if (car.lat && car.lon && shopmap) {
				pos = new google.maps.LatLng(car.lat, car.lon);
			}
			
			var el = mkHTML('div', {
				className: 'l-item',
				onclick: (carid => e => {
					showDetails(carid);
				})(car.car_id)
			});
			
			el.appendChild(mkHTML('div', {
				className: 'c-img',
				style: 'background-image: url("' + img +'");'
			}));
			
			var info = mkHTML('div');
			info.appendChild(mkHTML('h4', {
				innerText: i + '. ' + car.brand_name + ' ' + car.model + ' (' + car.price_eur_cent / 100 + '€)'
			}));
			info.appendChild(mkHTML('hr'));
			
			info.appendChild(mkHTML('span', {
				innerText: car.description + ', ' + car.views + ' view(s)'
			}));
			
			el.appendChild(info);
			shoplist.appendChild(el);
			
			if (pos && shopmap) {
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

	var total = await $$.fjson('/cars/api/cars/total' + getFilterQuery().replace('&', '?'));
	var pages = Math.max(1, Math.ceil(total / 10));
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

function radioFilterUnchecked() {
    this.checked = false;
    this.onclick = null;
    
    var fil = hashStorage.get('filters') || {};
    delete fil[this.name];
    
    hashStorage.set('filters', fil);
    
    populateShop(1);
}

function updateFilters() {
	var fil = hashStorage.get('filters') || {};
	
	Object.keys(fil).forEach(k => {
		var el = $$('.filters *[name="' + k + '"]');

		el.forEach(e => {
			if (e.type == 'radio') {
				e.checked = fil[k] === e.value;
				if (e.checked) {
					e.onclick = radioFilterUnchecked;
				}
			} else {
				e.value = fil[k];
			}
		});
	});
}

async function initFilters() {
	var totalbr = await $$.fjson('/cars/api/brands/total');
	var brands = await $$.fjson('/cars/api/brands?limit=' + totalbr);
	brandmap = {};
	
	console.log(totalbr, brands);

	var sel = $$('select[name="brand_id"]')[0];
	sel.innerHTML = "";
	
	sel.appendChild(mkHTML('option', {
		innerText: 'Any brand',
		value: '-1'
	}));
	
	for (var br of brands) {
		sel.appendChild(mkHTML('option', {
			value: br.brand_id+'',
			innerText: br.name
		}));
		
		brandmap[br.brand_id] = br;
	}
	
	var radios = $$('input[type="radio"]');
	radios.on('change', (e, elm) => {
		for (var r of radios) { r.onclick = null; }
		
		var fil = hashStorage.get('filters') || {};
        fil[elm.name] = elm.value;
        
        hashStorage.set('filters', fil);
		
		elm.onclick = radioFilterUnchecked;
	    
	    populateShop(1);
	});
	
	updateFilters();
	
	$$('.filters input, .filters select').on('change', (e, elm) => {
		if (elm.type == 'radio') {
			return;
		}
		
		var fil = hashStorage.get('filters') || {};
		if (elm.value.length == 0 || elm.value == '-1') {
			delete fil[elm.name];
		} else {
			fil[elm.name] = elm.value;
		}
		
		hashStorage.set('filters', fil);
		
		populateShop(1);
	});
}

async function initShop() {
	shopfooter = $$('.shop-view > .view-footer')[0];
	shoplist = $$('.shop-view > .listing')[0];
	$$('.det-head > a').click(() => switchViews(false));
	gkey = $$('meta[name="jjp-google-api"]')[0].content;
}

ready(async () => {
	await initShop();
	await initFilters();
	
	var cid = hashStorage.get('view');
	if (cid) {
		await showDetails(cid);
	}
	
	await populateShop();
});
