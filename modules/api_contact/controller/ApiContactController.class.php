<?php
	#[auth_required]
	class ApiContactController extends ApiRestController {
		public function handle_post() : bool {
			$post = self::get_json_post();
			
			if (!$post['message']) {
				http_response_code(400);
				echo json_encode([
					'ok' => false,
					'err' => 'No message specificed.'
				]);
				
				return true;
			}
			
			try {
				$usr = MwSessionLoader::get_user();
				$usr = $usr->get_fields();
				$msg = htmlentities($post['message'], ENT_QUOTES);
				
				$mailer = new CurlMailjetMailer($usr['username']);
				$mailer->send_mail(Config::get_mailjet_email(), 'Message from contact form', preg_replace('/^\s*/', '', <<<EOM
					You have received the following message from user: {$usr['username']}, uid: {$usr['uid']}:
					
					{$msg}
EOM
				));
				
				echo json_encode([
					'ok' => true
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
	}
?>