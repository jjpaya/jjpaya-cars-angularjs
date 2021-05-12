<?php
	class ApiAuthRegisterController extends ApiRestController {
		public function handle_post() : bool {
			if (!array_key_exists('username', $_POST)
					|| !array_key_exists('email', $_POST)
					|| !array_key_exists('pass', $_POST)) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'Incomplete data'
				]);
				
				return true;
			}
			
			$usrn = $_POST['username'];
			$email = $_POST['email'];
			$pass = $_POST['pass'];
			
			$hpass = password_hash(hash('sha384', $pass, true), PASSWORD_BCRYPT, [
				'cost' => 12
			]);
			
			try {
				$am = AuthModel::get_instance();
				$uid = $am->register_local_account($usrn, $email, $hpass)
						->fetch_assoc()['inserted_uid'];
				
				echo json_encode([
					'ok' => true,
					'uid' => $uid
				]);
				
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