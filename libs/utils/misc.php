<?php
	function nintval(?string $str) : ?int {
		return is_null($str) ? null : intval($str);
	}
	
	function read_json(string $file) : ?array {
		return json_decode(file_get_contents($file), true);
	}
?>