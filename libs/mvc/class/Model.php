<?php
	require_once 'libs/db/inc.php';
	
	abstract class Model {
		protected Database $db;
		
		public function __construct() {
			$this->db = Database::get_connection();
		}
	}
?>