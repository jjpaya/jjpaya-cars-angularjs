<?php
	class UrlPathControllerSelector extends Controller {
		private static string $default_controller = 'MainController';
		private static string $fallback_controller = 'Err404Controller';
		private static string $controller_class_prefix = 'Page';

		public static function set_default_controller(string $class_name) : void {
			self::$default_controller = $class_name;
		}

		public static function set_fallback_controller(string $class_name) : void {
			self::$fallback_controller = $class_name;
		}

		public static function set_controller_class_prefix(string $prefix) : void {
			self::$controller_class_prefix = $prefix;
		}

		public static function get_subcontroller(array $url_path) : ?string {
			$target_class = null;

			if (count($url_path) == 0) {
				$target_class = self::$default_controller;
			} else {
				$target_class = self::$controller_class_prefix . ucfirst($url_path[0]) . 'Controller';
			}
			
			if (!class_exists($target_class)) {
				$target_class = self::$fallback_controller;
			}
			
			return $target_class;
		}
	}
?>