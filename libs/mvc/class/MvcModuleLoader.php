<?php
	class MvcModuleLoader {
		private array $middleware_instances = array();
		private array $page_mvc_module_names = array();
		private string $module_directory = 'modules';
		
		public get_module_directory() : string {
			return $this->module_directory;
		}
		
		public set_module_directory(string $new_dir) : void {
			$this->module_directory = $new_dir;
		}
		
		public add_middleware(string $class_type) : mixed {
			return $this->middleware_instances[] = new $class_type($this);
		}
		
		public get_middleware(string $class_type) : ?mixed {
			foreach ($this->middleware_instances as &$mw) {
				if ($mw::class instanceof $class_type) {
					return $mw;
				}
			}
			
			return null;
		}
		
		public exec_middlewares() : bool {
			foreach ($this->middleware_instances as &$mw) {
				if (!$mw->execute()) {
					return false;
				}
			}
			
			return true;
		}
		
		public instance_modules() : array {
			return array_map(fn($name) => mvc_load_mod($name, $this->module_directory));
		}
	}
?>