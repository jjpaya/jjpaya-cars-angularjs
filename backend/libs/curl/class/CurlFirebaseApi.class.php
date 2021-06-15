<?php
	class CurlFirebaseApi extends CurlWrapper {
		private static string $api_url = 'https://identitytoolkit.googleapis.com/v1/';

		public function __construct() {
			parent::__construct();

			parent::setDefaultOptions();
			parent::addHeader('Content-Type', 'application/json');
		}
		
		public function accounts_lookup(string $id_token) : array {
			return json_decode(parent::rawPost(self::$api_url . 'accounts:lookup?key=' . Config::get_firebase_api_key(), json_encode([
				'idToken' => $id_token
			])), true);
		}
	}
?>