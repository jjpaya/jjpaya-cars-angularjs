<?php
	class ApiAuthController extends Controller {
		public static function get_subcontroller(array $url_path) : ?string {
			switch ($url_path[0] ?? null) {
				case 'login':    return ApiAuthLoginController::class;
				case 'register': return ApiAuthRegisterController::class;
				case 'info':     return ApiAuthInfoController::class;
				case 'logout':   return ApiAuthLogoutController::class;
			}
			
			return Err404Controller::class;
		}
	}
?>