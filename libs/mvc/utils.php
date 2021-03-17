<?php
	require_once 'libs/utils/url.php';
	require_once 'class/Controller.php';
	
	function mvc_load_mod(string $modName, bool $is_api = false) : ?Controller {
		$targetClass = str_replace('_', '', ucwords($modName, '_')) . 'Controller';
		$modules = 'modules_' . ($is_api ? 'be' : 'fe');
		$targetPath = "{$modules}/{$modName}/controller/{$targetClass}.php";
		
		if (!file_exists($targetPath)) {
			return null;
		}
		
		include_once $targetPath;
		
		return class_exists($targetClass) ? new $targetClass : null;
	}
	
	function mvc_load_mod_from_url_path_or_exception(
			string $defaultMod, string $fallbackMod,
			?string $exceptionMod = null, bool $is_api = false) : ?Controller {
		$segments = get_split_uri();
		$targetMod = null;

		if (count($segments) == 0) {
			$targetMod = $defaultMod;
		} else {
			$targetMod = ($is_api ? 'api_' : 'page_') . $segments[0];
		}
		
		try {
			$instance = mvc_load_mod($targetMod, $is_api);
			
			if (is_null($instance)) {
				$instance = mvc_load_mod($fallbackMod, $is_api);
			}
			
		} catch (Exception $e) {
			if (is_null($exceptionMod)) {
				throw $e;
			}
			
			$instance = mvc_load_mod($exceptionMod, $is_api);
			
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