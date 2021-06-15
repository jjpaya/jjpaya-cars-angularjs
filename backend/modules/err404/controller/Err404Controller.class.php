<?php
	class Err404Controller extends ApiRestController {
		public function send_special() : bool {
			http_response_code(404);
			return true;
		}
	}
?>