export default ['Auth', '$route', function authGuard(Auth, $route) {
	if (!Auth.currentUser) {
		return Promise.reject('unauthorized');
	}
	
	if ($route.current.adminOnly && !Auth.currentUser.admin) {
		return Promise.reject('noperms');
	}
}];
