<?php
	class Err503Controller extends Controller {
		private ?Exception $context = null;
		
		public function set_error_context(Exception $e) : void {
			$this->context = $e;
		}
		
		public function send_special() : bool {
			http_response_code(503);
			echo json_encode([
				'ok' => false,
				'err' => $this->context->getMessage()
			]);
			
			return true;
		}
		
		public function handle_get_body() : bool {
			$e = $this->context;
			
			require __DIR__ . '/../view/err503.phtml';
			return true;
		}
	}
?>