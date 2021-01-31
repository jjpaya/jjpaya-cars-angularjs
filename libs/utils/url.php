<?php
	function get_split_uri(int $skip = 0) : array {
		$arr = array_values(array_filter(
				explode('/', $_SERVER['REQUEST_URI']), function($el) {
			return $el !== '';
		}));
		
		array_splice($arr, 0, $skip);
		
		return $arr;
	}
?>