export default class FooterCtrl {
	constructor(AppConstants) {
		this.year = (new Date).getFullYear();
		this.author = AppConstants.author;
	}
}
