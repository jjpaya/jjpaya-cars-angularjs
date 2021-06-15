import CartService from './CartService.class.js';

var cartMod = angular.module('jjcars.serv.cart', []);

cartMod.service('Cart', ['$window', '$http', CartService]);

export default cartMod;
