<?php
	abstract class ApiRestController extends Controller {
		public static function get_json_post() : array {
			try {
				$arr = json_decode(file_get_contents('php://input'), true);
				if (!is_array($arr)) {
					return ['data' => $arr];
				}
				
				return $arr;
			} catch (Exception $e) {
				return [];
			}
		}
		
		public function handle_get() : bool { return false; }
		public function handle_post() : bool { return false; }
		public function handle_put() : bool { return false; }
		public function handle_delete() : bool { return false; }

		public function send_special() : bool {
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					return $this->handle_post();
					
				case 'PUT':
					return $this->handle_put();
					
				case 'GET':
					return $this->handle_get();
					
				case 'DELETE':
					return $this->handle_delete();
			}
			
			return false;
		}
	}
?>