<?php
	class MwControllerAuthenticator extends Middleware {
		public function __construct(MvcModuleLoader $loader) {
			parent::__construct($loader);
		}
		
		private function get_controller_attrmap(string $ctrl) : array {
			$rc = new ReflectionClass($ctrl);
			$attrs = $rc->getAttributes();
			$attrmap = [];
			
			foreach ($attrs as $a) {
				$args = $a->getArguments();
				$attrmap[$a->getName()] = count($args) <= 1 ? ($args[0] ?? 'SET') : $args;
			}
			
			return $attrmap;
		}
		
		private function check_attr_auth_required(?DbUser $usr, $attrs) : bool {
			switch ($attrs['auth_required'] ?? 'guest') {
				case 'admin':
					return $usr !== null && $usr->get_is_admin();
					
				case 'SET': // default choice with no extra params on attrib
				case 'user':
					return $usr !== null;
					
				case 'guest': // no attrib
					return true;
					
				default:
					throw new Exception('Unsupported auth type: ' . $attrs['auth_required']);
			}
		}
		
		private function check_controller_perms(?DbUser $usr, string $ctrl) : bool {
			$attrs = $this->get_controller_attrmap($ctrl);
			
			return $this->check_attr_auth_required($usr, $attrs); // only 1 attr for now
		}
		
		public function exec() : void {
			$usr = $this->loader->get_middleware(MwSessionLoader::class)->get_user();
			
			foreach ($this->loader->get_real_page_controllers() as &$ctrl) {
				if (!$this->check_controller_perms($usr, $ctrl)) {
					throw new Exception('Insufficient permissions!');
				}
			}
		}
	}
?>