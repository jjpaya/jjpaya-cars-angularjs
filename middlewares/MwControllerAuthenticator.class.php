<?php
	class MwControllerAuthenticator extends Middleware {
		public function __construct(MvcModuleLoader $loader) {
			parent::__construct($loader);
		}
		
		public function exec() : bool {
			$this->loader->get_middleware(MwSessionLoader::class)
			->get_user();
			
			return true;
		}
	}
?>