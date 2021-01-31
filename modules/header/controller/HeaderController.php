<?php
	require_once 'libs/mvc/inc.php';
	require_once 'libs/utils/url.php';
	
	class HeaderController extends Controller {
		
		public function handle_get_head() : void {
			require __DIR__ . '/../view/navbar_head.phtml';
		}
		
		public function handle_get_body() : bool {
			$uri = get_split_uri();
			$current_p = function(string $page, bool $def = false) use ($uri) {
				if (count($uri) > 0) {
					return $page === $uri[0] ? 'current' : '';
				} else {
					return $def ? 'current' : '';
				}
			};
			
			require __DIR__ . '/../view/navbar.phtml';
			return true;
		}
	}
?>