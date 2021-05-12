<?php
	require_once 'libs/utils/url.inc.php';
	
	class MvcModuleLoader {
		private array $middleware_instances = array();
		private array $page_controllers = array();
		private static ?array $mod_directories = null;
		
		public static function get_module_directories() : array {
			return self::$mod_directories
					?? (self::is_api_request() ? ['modules_be'] : ['modules_fe']);
		}
		
		public static function set_module_directory(array $new_dirs) : void {
			self::$mod_directories = $new_dirs;
		}
		
		public static function is_api_request() : bool {
			return (get_split_uri()[0] ?? '/') === 'api';
		}
		
		public static function get_controller_uri_path() : array {
			$uri = get_split_uri();
			
			if (self::is_api_request()) {
				// remove the /api/ from the url when handling it
				array_shift($uri);
			}
			
			return $uri;
		}
		
		public function add_middleware(string $class_type) : mixed {
			return $this->middleware_instances[] = new $class_type($this);
		}
		
		public function get_middleware(string $class_type) : mixed {
			foreach ($this->middleware_instances as &$mw) {
				if ($mw instanceof $class_type) {
					return $mw;
				}
			}
			
			return null;
		}
		
		public function exec_middlewares() : void {
			foreach ($this->middleware_instances as &$mw) {
				$mw->exec();
			}
		}
		
		public function set_page_controllers(array $ctrls) : void {
			$this->page_controllers = $ctrls;
		}
		
		public function get_real_page_controllers() : array {
			$uri = self::get_controller_uri_path();
			return array_map(fn($name) => $name::get_real_controller($uri), $this->page_controllers);
		}
		
		public function instance_modules() : array {
			return array_map(fn($name) => new $name, $this->get_real_page_controllers());
		}
	}
?>