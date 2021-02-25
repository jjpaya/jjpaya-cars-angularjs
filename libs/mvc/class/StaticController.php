<?php
	require_once 'Controller.php';

	abstract class StaticController extends Controller {
		protected ReflectionClass $rc;
		protected string $name;
		private bool $is_content;

		public function __construct() {
			$rc = new ReflectionClass(static::class);
			$is_content = false;

			$name = $rc->getShortName();
			if (str_starts_with($name, 'Page')) {
				$name = substr($name, 4);
				$is_content = true;
			}

			if (str_ends_with($name, 'Controller')) {
				$name = substr($name, 0, -10);
			}
		}

		public function get_title() : string {
			return $is_content ? $name : '';
		}

		private function try_file_include(string $path) : bool {
			if (file_exists($path . '.phtml') {
				$path .= '.phtml';
			} else if (file_exists($path . '.html') {
				$path .= '.html';
			} else {
				return false;
			}

			include $path;
			
			return true;
		}

		public function handle_get_head() : void {
			$path = dirname($rc->getFileName(), 2) . '/view/' . strtolower($name) . '_head';
			try_file_include($path);
		}

		public function handle_get_body() : void {
			$path = dirname($rc->getFileName(), 2) . '/view/' . strtolower($name) . '_body';
			try_file_include($path);
		}
	}
?>
