<?php
	class ApiAuthLoginGithubController extends ApiRestController {
		public function handle_post() : bool {
			$post = self::get_json_post();
			
			if (!array_key_exists('idToken', $post)) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'Incomplete data'
				]);
				
				return true;
			}
			
			$am = AuthModel::get_instance();
			
			$cfa = new CurlFirebaseApi();
			$account = $cfa->accounts_lookup($post['idToken']);
			$user = $account['users'][0] ?? null;
			
			if (!$user) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'Invalid token!'
				]);
				
				return true;
			}
			
			$username = preg_replace('/@.*/', '', $user['email']);

			$accdata = $am->get_or_create_github_account($user['localId'], $username, $user['photoUrl'], $user['email']);
			
			$jwt = new JWT(Config::get_jwt_secret());
			$exptime = time() + 60 * 60 * 24 * 7;
			
			$sesspload = $jwt->encode([
				'sess_uid' => $accdata['uid'],
				'persist' => true,
				'stype' => 'github',
				'exp' => $exptime
			]);
			
			$udatapload = $jwt->encode([
				'uid' => $accdata['uid'],
				'username' => $accdata['username'],
				'admin' => $accdata['is_admin'],
				'img' => $accdata['img'],
				'stype' => 'github',
				'exp' => $exptime
			]);
			
			setcookie('jwtsesstoken', $sesspload, [
				'expires' => $exptime,
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