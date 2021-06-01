import ContactService from './ContactService.class.js';

var contactMod = angular.module('jjcars.serv.contact', []);

contactMod.service('Contact', ['$http', ContactService]);

export default contactMod;
