<?php
	require_once 'libs/mvc/inc.php';
	
	class Err404Controller extends Controller {
		
		public function get_title() : string {
			return 'Error 404';
		}
		
		public function handle_get_http_head() : bool {
			http_response_code(404);
			return false;
		}
		
		public function handle_get_body() : bool {
			require __DIR__ . '/../view/err404.phtml';
			return true;
		}
	}
?>