<?php
	class Database extends mysqli {
		private static ?Database $instance = null;
		
		private static string $host = 'localhost';
		private static string $user = 'www-data';
		private static string $pass = '';
		private static string $db = '';

		private function __construct() {
			parent::__construct(
				Database::$host,
				Database::$user,
				Database::$pass,
				Database::$db);
			
			$this->set_charset('utf8mb4');
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		}
		
		/* simple query */
		public function squery($query) : mysqli_result|bool {
			return parent::query($query);
		}
		
		public function pquery($query, ...$args) : mysqli_result|bool {
			$params = [];
			$result = null;
			
			$stmt = $this->prepare($query);
			
			if (!$stmt) {
				return false;
			}
			
			try {
				if (count($args) > 0) {
					$types = array_reduce($args, function ($string, $arg) use (&$params) {
						$params[] = &$arg;
						if (is_float($arg))       { $string .= 'd'; }
						elseif (is_integer($arg)) { $string .= 'i'; }
						elseif (is_string($arg))  { $string .= 's'; }
						else                      { $string .= 'b'; }
						return $string;
					}, '');
					array_unshift($params, $types);

					call_user_func_array([$stmt, 'bind_param'], $params);
				}
				
				return $stmt->execute() ? $stmt->get_result() : false;
			} finally {
				$stmt->close();
			}
		}
		
		public static function get_connection() : Database {
			if (is_null(Database::$instance)) {
				Database::$instance = new Database;
				
				if (mysqli_connect_errno()) {
					throw new Exception('Connect failed: ' . mysqli_connect_error());
				}
			}
			
			return Database::$instance;
		}
		
		public static function set_default_details(
				string $user = 'www-data', string $pass = '', string $db = '',
				string $host = 'localhost') : void {
			Database::$host = $host;
			Database::$user = $user;
			Database::$pass = $pass;
			Database::$db = $db;
		}
		
		public static function set_default_details_json(array $details) : void {
			Database::set_default_details($details['user'], $details['pass'], $details['name']);
		}
	}
?>