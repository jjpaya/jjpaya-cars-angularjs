<?php
	abstract class ApiRestController extends Controller {
		public function handle_get() : bool { return false; }
		public function handle_post() : bool { return false; }
		public function handle_delete() : bool { return false; }

		public function send_special() : bool {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					return $this->handle_post();  

				case 'GET':
					return $this->handle_get();
					
				case 'DELETE':
					return $this->handle_delete();
			}
			
			return false;
		}
	}
?>