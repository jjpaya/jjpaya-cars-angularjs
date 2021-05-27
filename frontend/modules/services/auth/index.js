import AuthService from './AuthService.class.js';

var authMod = angular.module('jjcars.serv.auth', []);

authMod.service('Auth', ['$http', '$window', AuthService]);

export default authMod;
