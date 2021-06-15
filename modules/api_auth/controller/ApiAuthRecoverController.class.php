<?php
	require_once 'libs/utils/misc.inc.php';
	
	class ApiAuthRecoverController extends ApiRestController {
		private AuthModel $am;
		
		public function __construct() {
			$this->am = AuthModel::get_instance();
		}
		
		/* Checks token validity */
		public function handle_get() : bool {
			$rectoken = $_GET['token'] ?? null;
			$uid = nintval($_GET['uid'] ?? null);
			
			if (!$rectoken || !$uid) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'No token or uid specified'
				]);
				
				return true;
			}
			
			try {
				$valid = $this->am->check_recover_token_validity($uid, $rectoken);
			
				echo json_encode([
					'ok' => true,
					'valid' => $valid
				]);
				
			} catch (Exception $e) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => $e->getMessage()
				]);
			}
			
			return true;
		}
		
		/* Uses token, changes password if valid */
		public function handle_post() : bool {
			$post = self::get_json_post();
			
			$rectoken = $post['token'] ?? null;
			$uid = nintval($post['uid'] ?? null);
			$newpass = $post['newpass'] ?? null;
			
			if (!$rectoken || !$uid || !$newpass) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'No token or uid or new pass specified'
				]);
				
				return true;
			}
			
			$hpass = password_hash(hash('sha384', $newpass, true), PASSWORD_BCRYPT, [
				'cost' => 12
			]);
			
			try {
				$this->am->use_recover_token($uid, $rectoken, $hpass);
			
				echo json_encode([
					'ok' => true,
					'uid' => $uid
				]);
				
			} catch (Exception $e) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => $e->getMessage()
				]);
			}
			
			return true;
		}
		
		/* Creates a token and sends mail */
		public function handle_put() : bool {
			$post = self::get_json_post();
			$email = $post['email'] ?? null;
			
			if (!$email) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'No mail specified'
				]);
				
				return true;
			}

			try {
				$data = $this->am->create_recovery_token_for($email);
				
				$mailer = new CurlMailjetMailer('JJCars');
				$mailer->send_mail($data['user']['email'], 'Password recovery', preg_replace('/^\s*/', '', <<<EOM
					Hello!
					
					You have requested a password recovery for {$data['user']['name']}.
					Click on the following link to proceed with the change:
					http://localhost:8080/#/resetpw/{$data['user']['uid']}/{$data['token']}
					
					If you didn't request this change you may safely ingore this email, the token expires in 6 hours.
					
					-- JJCars
EOM
				));
				
				echo json_encode([
					'ok' => true
				]);
				
			} catch (Exception $e) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => $e instanceof mysqli_sql_exception ? $e->getCode() : $e->getMessage()
				]);
			}
			
			return true;
		}
	}
?>