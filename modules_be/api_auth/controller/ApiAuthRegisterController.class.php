<?php
	class ApiAuthRegisterController extends ApiRestController {
		public function handle_post() : bool {
			$post = self::get_json_post();
			
			if (!array_key_exists('username', $post)
					|| !array_key_exists('email', $post)
					|| !array_key_exists('pass', $post)) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'Incomplete data'
				]);
				
				return true;
			}
			
			$usrn = $post['username'];
			$email = $post['email'];
			$pass = $post['pass'];
			
			$hpass = password_hash(hash('sha384', $pass, true), PASSWORD_BCRYPT, [
				'cost' => 12
			]);
			
			try {
				$am = AuthModel::get_instance();
				$uid = $am->register_local_account($usrn, $email, $hpass)
						->fetch_assoc()['inserted_uid'];
				
				// make the client log in at the same time
				http_response_code(307);
				header('Location: /api/auth/login');
				
			} catch (Exception $e) {
				echo json_encode([
					'ok' => false,
					'err' => $e->getMessage(),
					'typ' => $e::class
				]);
			}
			
			return true;
		}
	}
?>