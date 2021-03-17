'use strict';

var langs = {};

async function loadLang(key) {
	return langs[key]
		|| (langs[key] = await $$.fjson(`/modules_fe/common/view/i18n/${key}.json`));
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
	$$('[data-tr-switch]')
		.click((e, el) => selectLang(el.getAttribute('data-tr-switch')));
}

async function loadLangs() {
	const selected = pickLangs([getStoredLangOrDefault(), ...(navigator.languages || []), 'en']);
	const data = await $$.fjson('/modules_fe/common/view/i18n/available.json');
	
	for (const ln of selected) {
		if (data.indexOf(ln) !== -1) {
			selectLang(ln);
			break;
		}
	}
}

ready(() => {
	bindLangSwitchers();
	loadLangs();
});
