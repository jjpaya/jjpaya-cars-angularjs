<?php
	require_once 'libs/mvc/inc.php';
	
	class PageMainController extends Controller {
		
		public function get_title() : string {
			return 'Main';
		}

		public function handle_get_body() : bool {
			require __DIR__ . '/../view/main.phtml';
			return true;
		}
	}
?>