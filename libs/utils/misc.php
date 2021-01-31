<?php
	function nintval(?string $str) : ?int {
		return is_null($str) ? null : intval($str);
	}
?>