<?php
	class ApiCartController extends Controller {
		public static function get_subcontroller(array $url_path) : ?string {
			switch ($url_path[0] ?? null) {
				case 'details':  return ApiCartDetailsController::class;
				case 'checkout': return ApiCartCheckoutController::class;
			}
			
			return Err404Controller::class;
		}
	}
?>