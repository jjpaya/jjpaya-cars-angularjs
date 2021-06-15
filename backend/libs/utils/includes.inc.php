<?php
	function add_to_include_path(string $path) : string|false {
		return set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	}
	
	function assert_add_to_include_path(string $path) : string {
		$res = add_to_include_path($path);
		
		if ($res === false) {
			throw new Exception('add_to_include failed');
		}
		
		return $res;
	}
?>