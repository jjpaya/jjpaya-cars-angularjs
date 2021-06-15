<?php
	class ApiAuthLoginController extends ApiRestController {
		public static function get_subcontroller(array $url_path) : ?string {
			switch ($url_path[0] ?? null) {
				case 'local':  return ApiAuthLoginLocalController::class;
				case 'github': return ApiAuthLoginGithubController::class;
				case 'google': return ApiAuthLoginGoogleController::class;
			}
			
			return Err404Controller::class;
		}
	}
?>