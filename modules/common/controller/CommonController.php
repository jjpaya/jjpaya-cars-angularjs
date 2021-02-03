<?php
	require_once 'libs/mvc/inc.php';
	
	class CommonController extends Controller {

		public function handle_get_head() : void {
			require __DIR__ . '/../view/head.html';
		}
		
		public function handle_get_body() : bool {
			require __DIR__ . '/../view/body.html';
			return true;
		}
	}
?>