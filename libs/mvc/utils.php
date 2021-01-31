<?php
	require_once 'libs/utils/url.php';
	require_once 'class/Controller.php';
	
	function mvc_load_mod(string $modName) : ?Controller {
		$targetClass = str_replace('_', '', ucwords($modName, '_')) . 'Controller';
		$targetPath = "modules/{$modName}/controller/{$targetClass}.php";
		
		if (!file_exists($targetPath)) {
			return null;
		}
		
		include_once $targetPath;
		
		return class_exists($targetClass) ? new $targetClass : null;
	}
	
	function mvc_load_mod_from_url_path_or_exception(
			string $defaultMod, string $fallbackMod,
			?string $exceptionMod = null) : ?Controller {
		$segments = get_split_uri();
		$targetMod = null;

		if (count($segments) == 0) {
			$targetMod = $defaultMod;
		} else {
			$targetMod = 'page_' . $segments[0];
		}
		
		try {
			$instance = mvc_load_mod($targetMod);
			
			if (is_null($instance)) {
				$instance = mvc_load_mod($fallbackMod);
			}
			
		} catch (Exception $e) {
			if (is_null($exceptionMod)) {
				throw $e;
			}
			
			$instance = mvc_load_mod($exceptionMod);
			
			if (is_null($instance)) {
				throw $e;
			}
			
			if (method_exists($instance, 'set_error_context')) {
				$instance->set_error_context($e);
			}
		}
		
		return $instance;
	}
?>