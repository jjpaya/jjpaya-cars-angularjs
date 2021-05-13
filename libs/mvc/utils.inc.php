<?php
	require_once 'libs/utils/includes.inc.php';

	function mvc_setup_autoloader() {
		assert_add_to_include_path(__DIR__ . '/class');
		
		assert_add_to_include_path($_SERVER['DOCUMENT_ROOT'] . '/libs/db/class');
		assert_add_to_include_path($_SERVER['DOCUMENT_ROOT'] . '/libs/jwt/class');
		assert_add_to_include_path($_SERVER['DOCUMENT_ROOT'] . '/libs/curl/class');
		assert_add_to_include_path($_SERVER['DOCUMENT_ROOT'] . '/middlewares');
		assert_add_to_include_path($_SERVER['DOCUMENT_ROOT'] . '/models');
		assert_add_to_include_path($_SERVER['DOCUMENT_ROOT'] . '/models/db_types');
		
		spl_autoload_register(function ($class_name) {
			foreach (['.class.php', '.class.singleton.php'] as &$ext) {
				$filepath = $class_name . $ext;
				
				if (file_exists(stream_resolve_include_path($filepath))){
					require $filepath;    
				}
			}
		});
		
		spl_autoload_register(function ($class_name) {
			$mods_directory = MvcModuleLoader::get_module_directories();
			
			if (!str_ends_with($class_name, 'Controller')
					&& !str_ends_with($class_name, 'Model')) {
				// Not an mvc class, ignore
				return;
			}
			
			$class_parts = preg_split('/(?=[A-Z])/', $class_name, -1, PREG_SPLIT_NO_EMPTY);
			$class_type = strtolower(array_pop($class_parts));
			$mod_name = strtolower(implode('_', $class_parts));
			
			if (count($class_parts) == 0) {
				return;
			}
			
			foreach ($mods_directory as &$mod_dir) {
				$target_path = "{$mod_dir}/{$mod_name}";
				$target_class_path = "{$target_path}/{$class_type}/{$class_name}.class.php";
				
				if (file_exists($target_class_path)) {
					assert_add_to_include_path("{$_SERVER['DOCUMENT_ROOT']}/{$target_path}/controller");
					assert_add_to_include_path("{$_SERVER['DOCUMENT_ROOT']}/{$target_path}/model");
					require $target_class_path;
				}
			}
		});
	}
?>