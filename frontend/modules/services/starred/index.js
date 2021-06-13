import StarredService from './StarredService.class.js';

var starredMod = angular.module('jjcars.serv.starred', []);

starredMod.service('Starred', ['Auth', '$http', StarredService]);

export default starredMod;
