<?php
	class MvcRouter {
		private $page_charset = 'UTF-8';
		private $page_brand = null;
		private $page_mvc_modules = array();
		private $done = false;
		
		
		
		public function set_page_charset(string $cset) : void {
			$this->page_charset = $cset;
		}
		
		public function set_page_brand(string $name) : void {
			$this->page_brand = $name;
		}
		
		public function set_page_mvc_content(array $content) : void {
			$this->page_mvc_modules = $content;
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