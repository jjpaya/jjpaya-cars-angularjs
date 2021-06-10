import RelatedService from './RelatedService.class.js';

var relatedMod = angular.module('jjcars.serv.related', []);

relatedMod.service('Related', ['Credentials', '$http', RelatedService]);

export default relatedMod;
