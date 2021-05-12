<?php
	require_once 'libs/utils/misc.inc.php';
	require_once 'libs/db/inc.php';
	
	class Config {
		private static ?string $google_api_key;
		private static ?string $jwt_secret;

		public static function load_config(string $file) : void {
			$config = read_json($file);
			
			Config::$google_api_key = $config['api']['google'] ?? null;
			Config::$jwt_secret = $config['jwt']['secret'] ?? null;
			
			Database::set_default_details_json($config['db']);
		}
		
		public static function get_google_api_key() : string {
			return Config::$google_api_key;
		}
		
		public static function get_jwt_secret() : string {
			return Config::$jwt_secret;
		}
	}
?>