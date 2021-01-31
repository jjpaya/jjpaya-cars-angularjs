<?php
	require_once 'libs/mvc/inc.php';
	
	class CommonController extends Controller {

		public function handle_get_head() : void {
			require __DIR__ . '/../view/head.phtml';
		}
	}
?>