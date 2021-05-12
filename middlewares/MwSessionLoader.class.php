<?php
	class MwSessionLoader extends Middleware {
		private static ?DbUser $current_session_user = null;
		private static string $current_session_type = 'none';
		
		public function __construct(MvcModuleLoader $loader) {
			parent::__construct($loader);
		}
		
		public static function get_user() : ?DbUser {
			return self::$current_session_user;
		}
		
		public static function get_sess_type() : string {
			return self::$current_session_type;
		}
		
		public function exec() : void {
			$am = AuthModel::get_instance();
			$jwt = new JWT(Config::get_jwt_secret());
			
			if (!array_key_exists('jwtsesstoken', $_COOKIE)) {
				return;
			}
			
			try {
				$pload = $jwt->decode($_COOKIE['jwtsesstoken']);
			
				self::$current_session_user = DbUser::get_one($pload['sess_uid']);
				self::$current_session_type = $pload['stype'] ?? 'unk';
				
				// refresh the token
				$exptime = time() + 60 * 60 * 24 * 7;
				$sesspload = $jwt->encode([
					'sess_uid' => $pload['sess_uid'],
					'persist' => $pload['persist'],
					'stype' => $pload['stype'],
					'exp' => $exptime
				]);
				
				setcookie('jwtsesstoken', $sesspload, [
					'expires' => $pload['persist'] ? $exptime : 0,
					'path' => '/api/',
					'secure' => false,  /* TODO: change to true on https */
					'httponly' => true,
					'samesite' => 'Lax'
				]);
				
			} catch (Exception $e) {
				// invalid/expired session
				setcookie('jwtsesstoken', '', [
					'expires' => 1,
					'path' => '/api/',
					'secure' => false,  /* TODO: change to true on https */
					'httponly' => true,
					'samesite' => 'Lax'
				]);
			}
		}
	}
?>