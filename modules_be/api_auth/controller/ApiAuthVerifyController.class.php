<?php
	require_once 'libs/utils/misc.inc.php';
	
	class ApiAuthVerifyController extends ApiRestController {
		private AuthModel $am;
		
		public function __construct() {
			$this->am = AuthModel::get_instance();
		}
		
		/* Checks token validity */
		public function handle_get() : bool {
			$token = $_GET['token'] ?? null;
			$uid = nintval($_GET['uid'] ?? null);
			
			if (!$token || !$uid) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'No token or uid specified'
				]);
				
				return true;
			}
			
			try {
				$valid = $this->am->check_verify_token_validity($uid, $token);
			
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
		
		/* Uses token, verifies account if valid */
		public function handle_post() : bool {
			$token = $_POST['token'] ?? null;
			$uid = nintval($_POST['uid'] ?? null);

			if (!$token || !$uid) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'No token or uid specified'
				]);
				
				return true;
			}
			
			try {
				$this->am->use_verify_token($uid, $token);
			
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
			$usr = MwSessionLoader::get_user();
			
			if (!$usr) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'Not logged in!'
				]);
				
				return true;
			}
			
			$usr = $usr->get_fields();
			$uid = $usr['uid'];
			
			try {
				$data = $this->am->create_verification_token_for($uid);
				
				$mailer = new CurlMailjetMailer('JJCars');
				$mailer->send_mail($data['user']['email'], 'Mail verification', preg_replace('/^\s*/g', '', <<<EOM
					Hello!
					
					You have requested to verify your email address on JJCars for {$data['user']['name']}.
					Click on the following link to verify your account:
					http://localhost:8080/api/auth/verify?uid={$data['user']['uid']}&token={$data['token']}
					
					If you didn't request the verification you may safely ingore this email, the token expires in 6 hours.
					
					-- JJCars
EOM
				));
				
				echo json_encode([
					'ok' => true,
					'uid' => $data['user']['uid']
				]);
				
			} catch (Exception $e) {
				http_response_code(400);
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