<?php
	require_once 'libs/utils/url.php';
	
	require_once 'Controller.php';
	
	abstract class MultiController extends Controller {
		private array $routes = array();
		
		public function __construct() {
			
		}
		
		public function add_route(string $path, Controller $controller) : void {
			$this->routes[$path] = $controller;
		}
		
		public function send_http_head() : bool {
			$uri = get_split_uri(1);
			//$f_uri = implode('/', $uri);
			//array_splice($arr, 0, $skip);
			//array_
		}
		
		public function send_special() : bool {
			
		}

		public function send_head() : void {
			
		}

		public function send_body() : bool {
			
		}
	}
?>