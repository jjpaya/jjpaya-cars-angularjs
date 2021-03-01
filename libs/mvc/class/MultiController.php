<?php
	require_once 'libs/utils/url.php';
	
	require_once 'Controller.php';
	
	abstract class MultiController extends Controller {
		private array $routes = array();
		private int $depth;
		
		public function __construct(int $depth = 1) {
			$this->depth = $depth;
		}

		public function add_route(string $path, Controller $controller) : void {
			$this->routes[$path] = $controller;
		}
		
		public function get_route_controller() : ?Controller {
			$uri = get_split_uri($this->depth);
			$controller = null;
			
			while (count($uri) > 0) {
				$f_uri = implode('/', $uri);
				
				if (array_key_exists($f_uri, $this->routes)) {
					$controller = $this->routes[$f_uri];
					break;
				}

				array_splice($uri, -1);
			}
			
			if (is_null($controller)) {
				$controller = $this->routes['/'] ?? null;
			}

			return $controller;
		}
		
		public function get_title() : string {
			$controller = $this->get_route_controller();
			
			return $controller ? $controller->get_title() : '';
		}
		
		
		public function send_http_head() : bool {
			$controller = $this->get_route_controller();
			
			return $controller && $controller->send_http_head();
		}
		
		public function send_special() : bool {
			$controller = $this->get_route_controller();
			
			return $controller && $controller->send_special();
		}

		public function send_head() : void {
			$controller = $this->get_route_controller();
			
			if ($controller) {
				$controller->send_head();
			}
		}

		public function send_body() : bool {
			$controller = $this->get_route_controller();
			
			return $controller && $controller->send_body();
		}
	}
?>