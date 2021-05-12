<?php
	class MvcRouter extends MvcModuleLoader {
		private ?string $exception_controller = 'Err503Controller';
		private string $page_charset = 'UTF-8';
		private ?string $page_brand = null;
		private ?array $page_mvc_modules = null;
		private bool $done = false;
		
		public function set_page_charset(string $cset) : void {
			$this->page_charset = $cset;
		}
		
		public function set_page_brand(string $name) : void {
			$this->page_brand = $name;
		}
		
		public function request_done() : bool {
			return $this->done;
		}
		
		public function get_charset() : string {
			return $this->page_charset;
		}
		
		public function get_title() : string {
			$title = '';
			
			foreach ($this->page_mvc_modules as &$mod) {
				$title = $mod->get_title();
				if (strlen($title) > 0) {
					break;
				}
			}
			
			if (!is_null($this->page_brand)) {
				if (strlen($title) > 0) {
					$title .= ' - ';
				}
				
				 $title .= $this->page_brand;
			}
			
			return $title;
		}
		
		public function handle_request() : void {
			try {
				$this->exec_middlewares();
				$this->page_mvc_modules = parent::instance_modules();
				
			} catch (Exception $e) {
				if (is_null($this->exception_controller) || !class_exists($this->exception_controller)) {
					throw $e;
				}
			
				$instance = new $this->exception_controller;
			
				if (method_exists($instance, 'set_error_context')) {
					$instance->set_error_context($e);
				}
				
				$this->page_mvc_modules = [$instance];
			}
			
			$this->send_http_head();
			
			if ($this->request_done()) {
				return;
			}
			
			// for example, a file download
			$this->handle_special_request();
			
			if ($this->request_done()) {
				return;
			}
			
			$this->send_html();
		}
		
		
		
		private function send_http_head() : void {
			foreach ($this->page_mvc_modules as &$mod) {
				$this->done |= $mod->send_http_head();
			}
		}
		
		private function handle_special_request() : void {
			foreach ($this->page_mvc_modules as &$mod) {
				$this->done |= $mod->send_special();
				
				if ($this->request_done()) {
					break;
				}
			}
		}
		
		private function send_head() : void {
			foreach ($this->page_mvc_modules as &$mod) {
				$mod->send_head();
			}
		}
		
		private function send_body() : void {
			$didSomething = false;
			
			foreach ($this->page_mvc_modules as &$mod) {
				$didSomething |= $mod->send_body();
			}
			
			if (!$didSomething) {
				http_response_code(501);
				echo 'Not implemented';
			}
		}
		
		private function send_html() : void {
			require __DIR__ . '/../templates/skel.phtml';
		}
	}
?>