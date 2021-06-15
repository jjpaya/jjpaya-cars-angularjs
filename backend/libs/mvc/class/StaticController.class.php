<?php
	abstract class StaticController extends Controller {
		protected ReflectionClass $rc;
		protected string $name;
		private bool $is_content;

		public function __construct() {
			$this->rc = new ReflectionClass(static::class);
			$this->is_content = false;

			$this->name = $this->rc->getShortName();
			if (str_starts_with($this->name, 'Page')) {
				$this->name = substr($this->name, 4);
				$this->is_content = true;
			}

			if (str_ends_with($this->name, 'Controller')) {
				$this->name = substr($this->name, 0, -10);
			}
		}

		public function get_title() : string {
			return $this->is_content ? $this->name : '';
		}

		private function try_file_include(string $path) : bool {
			if (file_exists($path . '.phtml')) {
				$path .= '.phtml';
			} else if (file_exists($path . '.html')) {
				$path .= '.html';
			} else {
				return false;
			}

			include $path;
			
			return true;
		}

		public function handle_get_head() : void {
			$path = dirname($this->rc->getFileName(), 2) . '/view/' . strtolower($this->name) . '_head';
			$this->try_file_include($path);
		}

		public function handle_get_body() : bool {
			$path = dirname($this->rc->getFileName(), 2) . '/view/' . strtolower($this->name) . '_body';
			return $this->try_file_include($path);
		}
	}
?>