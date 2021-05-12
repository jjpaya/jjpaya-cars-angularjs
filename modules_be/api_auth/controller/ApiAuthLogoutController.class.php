<?php
	class ApiAuthLogoutController extends ApiRestController {
		public function handle_post() : bool {
			// doesn't really matter if not logged in, has no effect
			setcookie('jwtsesstoken', '', [
				'expires' => 1,
				'path' => '/api/',
				'secure' => false,  /* TODO: change to true on https */
				'httponly' => true,
				'samesite' => 'Lax'
			]);
			
			echo json_encode([
				'ok' => true
			]);
			
			return true;
		}
	}
?>