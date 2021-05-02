<?php
	abstract class Middleware {
		protected MvcModuleLoader $loader;
		
		public function __construct(MvcModuleLoader $loader) {
			$this->loader = $loader;
		}
		
		abstract public function exec() : bool;
	}
?>