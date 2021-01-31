<?php
	require_once 'libs/mvc/inc.php';
	
	class FooterController extends Controller {
		
		public function handle_get_body() : bool {
			require __DIR__ . '/../view/footer.phtml';
			return true;
		}
	}
?>