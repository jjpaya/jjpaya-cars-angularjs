import CarsService from './CarsService.class.js';

var carsMod = angular.module('jjcars.serv.cars', []);

carsMod.service('Cars', ['$http', '$window', CarsService]);

export default carsMod;
