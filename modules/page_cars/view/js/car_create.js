'use strict';

window.onload = () => {
	var validatedFormInputs = [
		'car-f-npl',
		'car-f-rdat',
		'car-f-brn',
		'car-f-mdl',
		'car-f-kms',
		'car-f-pri',
		'car-f-desc'
	].map(i => document.getElementById(i));
	
	var [carForm, carCreate] = [
		'car-f-form',
		'car-f-create'
	].map(i => document.getElementById(i));

	function red(f, el) {
		if (f(el)) {
			el.classList.remove('fail');
			return true;
		}
		
		el.classList.add('fail');
	}
	
	function maybeCheckPattern(el) {
		var pat = el.getAttribute('pattern');
		
		if (pat) {
			var rgx = new RegExp(pat);
			
			return rgx.test(el.value);
		}
		
		return true;
	}
	
	carCreate.onclick = carForm.onsubmit = e => {
		var res = validatedFormInputs
			.map(red.bind(null, el => el.value.length > 0 && maybeCheckPattern(el)))
			.reduce((a, b) => a && b, true);
			
		if (!res) {
			e.preventDefault();
			return false;
		}
	}
};