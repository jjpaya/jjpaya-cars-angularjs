'use strict';

var langs = {};

function loadLang(key) {
	return langs[key]
	? Promise.resolve(langs[key])
	: $$.ajax(`/modules/common/view/i18n/${key}.json`)
		.then(r => r.json())
		.then(data => langs[key] = data);
}

function applyLang(data) {
	for (const el of $$('[data-tr]')) {
		var key = el.getAttribute('data-tr');
		if (data.hasOwnProperty(key)) {
			el.innerText = data[key];
		}
	}
}

function getStoredLangOrDefault() {
	var ls = navigator.language;
	try {
		ls = localStorage.getItem('lang') || ls;
	} catch(e) { }
	
	return ls;
}

function setStoredLang(lang) {
	try {
		localStorage.setItem('lang', lang);
	} catch(e) { }
}

function selectLang(lang) {
	loadLang(lang).then(applyLang);
	setStoredLang(lang);
}

function pickLangs(data) {
	return [...new Set(data.map(l => l.slice(0, 2)))];
}

function bindLangSwitchers() {
	for (const el of $$('[data-tr-switch]')) {
		el.onclick = () => {
			selectLang(el.getAttribute('data-tr-switch'));
		};
	}
}

function loadLangs() {
	var selected = pickLangs([getStoredLangOrDefault(), ...(navigator.languages || []), 'en']);

	$$.ajax('/modules/common/view/i18n/available.json')
		.then(r => r.json())
		.then(data => {
			for (const ln of selected) {
				if (data.indexOf(ln) !== -1) {
					selectLang(ln);
					break;
				}
			}
		});
}

ready(() => {
	bindLangSwitchers();
	loadLangs();
});
