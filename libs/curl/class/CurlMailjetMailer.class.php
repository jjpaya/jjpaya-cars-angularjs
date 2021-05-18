<?php
	class CurlMailjetMailer extends CurlWrapper {
		private static string $api_url = 'https://api.mailjet.com/v3.1/send';
		private static ?string $from_email = null;
		private static ?string $from_name = null;
		
		public function __construct(?string $name = null) {
			parent::__construct();

			parent::setDefaultOptions();
			parent::setAuthCredentials(Config::get_mailjet_user(), Config::get_mailjet_pass());
			parent::addHeader('Content-Type', 'application/json');
			
			if (self::$from_email === null) {
				self::$from_email = Config::get_mailjet_email();
			}
			
			if ($name !== null) {
				self::$from_name = $name;
			}
			
			if (self::$from_name === null) {
				self::$from_name = 'Webpage mailer';
			}
		}
		
		public static function set_from_name(string $name) : void {
			self::$from_name = $name;
		}
		
		public static function set_from_email(string $mail) : void {
			self::$from_email = $mail;
		}
		
		public function send_mail(string $to, string $subj, string $text_content) : void {
			parent::rawPost(self::$api_url, json_encode([
				'Messages' => [
					0 => [
						'From' => [
							'Email' => self::$from_email,
							'Name' => self::$from_name,
						],
						'To' => [
							0 => [
								'Email' => $to,
							],
						],
						'Subject' => $subj,
						'TextPart' => $text_content,
					],
				],
			]));
		}
	}

?>