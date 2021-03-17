'use strict';

var searchbar = null;
var acompl = null;

async function populateAutocomplete(query) {
	acompl.innerHTML = "";
	if (query.length == 0) {
		acompl.classList.add('hidden');
		return;
	} else {
		acompl.classList.remove('hidden');
	}
	
	var res = await $$.fjson('/cars/api/cars/search?q=' + encodeURIComponent(query));
	
	for (var sug of res) {
		acompl.appendChild(mkHTML('div', {
			className: 'acompl',
			innerText: sug.name,
			onclick: (name => e => {
				window.onhashchange = function() {
					window.location.reload();
				};
				
				window.location.href = "/shop#" + JSON.stringify({filters: {containing: name}});
			})(sug.name)
		}));
	}
	
	if (res.length == 0) {
		acompl.appendChild(mkHTML('div', {
			className: 'acompl none',
			innerText: 'No results'
		}));
	}
	
	console.log(res);
}

function initSearch() {
	searchbar = $$('#h-search')[0];
	acompl = $$('#h-autocomplete')[0];
	
	var fil = hashStorage.get('filters') || {};
	if (fil.containing) {
		searchbar.value = fil.containing;
	}
	
	searchbar.oninput = e => {
		populateAutocomplete(searchbar.value);
	};
	
	searchbar.onkeydown = e => {
		if (e.key == 'Enter') {
			var filterquery = '';
			if (searchbar.value.length > 0) {
				filterquery = JSON.stringify({
					filters: {containing: searchbar.value}
				});
			}
			
			window.onhashchange = function() {
				window.location.reload();
			};
			
			window.location.href = "/shop#" + filterquery;
		}
	};
}

ready(() => {
	initSearch();
});
