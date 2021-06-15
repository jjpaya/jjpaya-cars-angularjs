<?php
	class ApiAuthLoginLocalController extends ApiRestController {
		public function handle_post() : bool {
			$post = self::get_json_post();
			
			if (!array_key_exists('username', $post)
					|| !array_key_exists('pass', $post)) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'Incomplete data'
				]);
				
				return true;
			}
			
			$am = AuthModel::get_instance();
			$usrn = $post['username']; // can be username or email
			$pass = $post['pass'];
			
			$accdata = $am->get_local_account_info($usrn);
			
			if (!$accdata || !password_verify(hash('sha384', $pass, true), $accdata['password'])) {
				http_response_code(404);
				echo json_encode([
					'ok' => false,
					'err' => 'No such user or bad password'
				]);
				
				return true;
			}
			
			$jwt = new JWT(Config::get_jwt_secret());
			$exptime = time() + 60 * 60 * 24 * 7;
			
			$sesspload = $jwt->encode([
				'sess_uid' => $accdata['uid'],
				'persist' => boolval($post['persist'] ?? false),
				'stype' => 'local',
				'exp' => $exptime
			]);
			
			$udatapload = $jwt->encode([
				'uid' => $accdata['uid'],
				'username' => $accdata['username'],
				'admin' => $accdata['is_admin'],
				'img' => $accdata['img'],
				'stype' => 'local',
				'email_verified' => $accdata['email_verified'],
				'exp' => $exptime
			]);
			
			setcookie('jwtsesstoken', $sesspload, [
				'expires' => ($post['persist'] ?? false) ? $exptime : 0,
				'path' => '/api/',
				'secure' => false,  /* TODO: change to true on https */
				'httponly' => true,
				'samesite' => 'Lax'
			]);

			echo json_encode([
				'ok' => true,
				'data' => $udatapload
			]);
			
			return true;
		}
	}
?>