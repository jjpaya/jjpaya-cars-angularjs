<?php
	require_once 'libs/utils/misc.inc.php';
	require_once 'libs/db/inc.php';
	
	class Config {
		private static ?string $google_api_key;
		private static ?string $jwt_secret;
		private static ?string $mailjet_email;
		private static ?string $mailjet_user;
		private static ?string $mailjet_pass;

		public static function load_config(string $file) : void {
			$config = read_json($file);
			
			Config::$google_api_key = $config['api']['google'] ?? null;
			Config::$jwt_secret = $config['jwt']['secret'] ?? null;
			Config::$mailjet_email = $config['api']['mailjet']['email'] ?? null;
			Config::$mailjet_user = $config['api']['mailjet']['user'] ?? null;
			Config::$mailjet_pass = $config['api']['mailjet']['pass'] ?? null;
			
			Database::set_default_details_json($config['db']);
		}
		
		public static function get_google_api_key() : string {
			return Config::$google_api_key;
		}
		
		public static function get_jwt_secret() : string {
			return Config::$jwt_secret;
		}
		
		public static function get_mailjet_email() : string {
			return Config::$mailjet_email;
		}
		
		public static function get_mailjet_user() : string {
			return Config::$mailjet_user;
		}
		
		public static function get_mailjet_pass() : string {
			return Config::$mailjet_pass;
		}		
	}
?>