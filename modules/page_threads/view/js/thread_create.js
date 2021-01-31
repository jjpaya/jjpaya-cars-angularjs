'use strict';

window.onload = () => {
	var [esub, eexp, emsg, eform, ecreate] = [
		'thr-f-sub',
		'thr-f-exp',
		'thr-f-msg',
		'thr-f-form',
		'thr-f-create'
	].map(i => document.getElementById(i));

	function red(f, el) {
		if (f(el)) {
			el.classList.remove('fail');
			return true;
		}
		
		el.classList.add('fail');
	}
	
	ecreate.onclick = eform.onsubmit = e => {
		var res = [esub, eexp, emsg]
			.map(red.bind(null, el => el.value.length > 0))
			.reduce((a, b) => a && b, true)
			& red(el => (new Date(el.value)).getTime() > (new Date()).getTime(), eexp);
			
		if (!res) {
			e.preventDefault();
			return false;
		}
	}
};