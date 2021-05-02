<?php
	class MainController extends Controller {
		public static function get_subcontroller(array $url_path) : ?string {
			return PageMainController::class;
		}
	}
?>