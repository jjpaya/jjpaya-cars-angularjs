<?php
	abstract class Middleware {
		protected MvcModuleLoader $loader;
		
		public __construct(MvcModuleLoader $loader) {
			$this->loader = $loader;
		}
		
		abstract public execute() : bool;
	}
?>