<?php
	class Err404Controller extends StaticController {
		
		public function get_title() : string {
			return 'Error 404';
		}
		
		public function handle_get_http_head() : bool {
			http_response_code(404);
			return false;
		}
	}
?>