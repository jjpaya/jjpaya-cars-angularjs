<?php
	class Config {
		private static ?string $maps_api_key = null;
		
		public static function load_config(string $file) : void {
			$config = read_json($file);
			
			Database::set_default_details_json($config['db']);
		}
		
		public static function get_maps_api_key() : string {
			return $maps_api_key;
		}
	}
?>