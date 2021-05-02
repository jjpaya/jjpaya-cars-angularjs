<?php
	class MwSessionLoader extends Middleware {
		private ?User $currentSession = null; // eh... to static or not to static...
		
		public function __construct(MvcModuleLoader $loader) {
			parent::__construct($loader);
		}
		
		public static function get_user() : ?User {
			return null;
		}
		
		public function exec() : bool {
			//$this->loader->get_middleware();
			
			return true;
		}
	}
?>