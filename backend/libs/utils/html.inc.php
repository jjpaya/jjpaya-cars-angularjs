<?php
	function htmlesc(?array $thr) : ?array {
		if (is_null($thr)) {
			return null;
		}
		
		foreach ($thr as $k => &$v) {
			$v = htmlentities($v);
		}
		
		return $thr;
	}
?>