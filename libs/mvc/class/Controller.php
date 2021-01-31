<?php
	abstract class Controller {
		public function get_title() : string {
			return '';
		}
		
		// return true if request is done, or if something was sent (body)
		public function handle_get_http_head() : bool { return false; }
		public function handle_get_special() : bool { return false; }
		public function handle_get_head() : void { }
		public function handle_get_body() : bool { return false; }
		
		public function handle_post_http_head() : bool { return false; }
		public function handle_post_special() : bool { return false; }
		public function handle_post_head() : void { }
		public function handle_post_body() : bool { return false; }
		
		public function send_http_head() : bool {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					return $this->handle_post_http_head();  

				case 'GET':
					return $this->handle_get_http_head();
			}
			
			return false;
		}
		
		public function send_special() : bool {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					return $this->handle_post_special();  

				case 'GET':
					return $this->handle_get_special();
			}
			
			return false;
		}

		public function send_head() : void {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$this->handle_post_head();
					break;

				case 'GET':
					$this->handle_get_head();
					break;
			}
		}

		public function send_body() : bool {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					return $this->handle_post_body();  

				case 'GET':
					return $this->handle_get_body();
			}
			
			return false;
		}
	}
?>