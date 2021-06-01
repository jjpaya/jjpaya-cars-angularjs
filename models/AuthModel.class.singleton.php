<?php
	class AuthModel extends Model {
		private static ?AuthModel $instance = null;

		private function __construct() {
			parent::__construct();
			$this->db_setup();
		}
		
		public static function get_instance() : AuthModel {
			if (is_null(self::$instance)) {
				self::$instance = new self;
			}
			
			return self::$instance;
		}
		
		private function db_setup() : void {
			
			/**********
			 * 
			 * TABLES
			 * 
			 **********/
			
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS users (
					uid BIGINT NOT NULL PRIMARY KEY DEFAULT CAST(UUID_SHORT() AS SIGNED),
					created DATETIME NOT NULL DEFAULT NOW(),
					last_login DATETIME NOT NULL DEFAULT NOW(),
					username VARCHAR(30) NOT NULL UNIQUE,
					is_admin BOOLEAN NOT NULL DEFAULT false,
					img VARCHAR(255),
					CONSTRAINT proper_name CHECK (username REGEXP '^[\\w.-]+$')
				)
EOQ
			);
			
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS deleted_users (
					uid BIGINT NOT NULL PRIMARY KEY,
					created DATETIME NOT NULL,
					last_login DATETIME NOT NULL,
					deleted DATETIME NOT NULL DEFAULT NOW(),
					username VARCHAR(30) NOT NULL
				)
EOQ
			);

			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS links_local (
					uid BIGINT NOT NULL PRIMARY KEY,
					email VARCHAR(256) NOT NULL UNIQUE,
					email_verified BOOLEAN NOT NULL DEFAULT false,
					password VARCHAR(60) NOT NULL,
					CONSTRAINT proper_email CHECK (email REGEXP '^[^\\s@\\x00-\\x1F\\x7F%!\\\\]+@[^\\s@\\x00-\\x1F\\x7F%!\\\\]+\\.[^\\s@\\x00-\\x1F\\x7F%!\\\\]+$'),
					FOREIGN KEY(uid) REFERENCES users (uid) ON DELETE CASCADE
				)
EOQ
			);

			/*$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS links_google (
					uid BIGINT NOT NULL PRIMARY KEY,
					FOREIGN KEY(uid) REFERENCES users (uid) ON DELETE CASCADE
				)
EOQ
			);*/

			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS pass_reset_tokens (
					target_uid BIGINT NOT NULL PRIMARY KEY,
					confirm_change_token VARCHAR(32) NOT NULL,
					expires DATETIME NOT NULL DEFAULT (NOW() + INTERVAL 6 HOUR),
					FOREIGN KEY(target_uid) REFERENCES users (uid) ON DELETE CASCADE
				)
EOQ
			);

			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS mail_verify_tokens (
					target_uid BIGINT NOT NULL PRIMARY KEY,
					confirm_change_token VARCHAR(32) NOT NULL,
					expires DATETIME NOT NULL DEFAULT (NOW() + INTERVAL 6 HOUR),
					FOREIGN KEY(target_uid) REFERENCES users (uid) ON DELETE CASCADE
				)
EOQ
			);
			
			
			/**********
			 * 
			 * PROCEDURES
			 * 
			 **********/

			$this->db->squery(<<<'EOQ'
				CREATE PROCEDURE IF NOT EXISTS get_local_account_info(IN _username VARCHAR(30))
				SELECT u.uid, u.username, u.is_admin, u.img, l.password, l.email_verified
				FROM users AS u
				INNER JOIN links_local AS l ON l.uid = u.uid
				WHERE _username IN (u.username, l.email)
EOQ
			);
			
			$this->db->squery(<<<'EOQ'
				CREATE PROCEDURE IF NOT EXISTS register_local_account(IN _username VARCHAR(30), IN _email VARCHAR(256), IN _hashed_pass VARCHAR(60))
				BEGIN
					DECLARE inserted_uid BIGINT DEFAULT 0;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					START TRANSACTION;
					
					INSERT INTO users (username)
						VALUES (_username);

					SELECT uid INTO inserted_uid
						FROM users
						WHERE username = _username;
					
					INSERT INTO links_local (uid, email, password)
						VALUES (inserted_uid, _email, _hashed_pass);
					
					COMMIT;
					
					SELECT inserted_uid;
				END
EOQ
			);
			
			$this->db->squery(<<<'EOQ'
				CREATE PROCEDURE IF NOT EXISTS use_recover_token(IN _target_uid BIGINT, IN _rtoken VARCHAR(32), IN _hashed_pass VARCHAR(60))
				BEGIN
					DECLARE err_invalid_token CONDITION FOR SQLSTATE '45000';
					DECLARE token_valid BOOLEAN DEFAULT false;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					DELETE FROM pass_reset_tokens
						WHERE NOW() > expires;
					
					START TRANSACTION;
					
					SELECT true INTO token_valid
						FROM pass_reset_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _rtoken;
					
					IF NOT token_valid THEN
						SIGNAL err_invalid_token;
					END IF;
					
					DELETE FROM pass_reset_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _rtoken;
					
					UPDATE links_local
						SET password = _hashed_pass
						WHERE uid = _target_uid;
					
					COMMIT;
				END
EOQ
			);
			
			$this->db->squery(<<<'EOQ'
				CREATE PROCEDURE IF NOT EXISTS create_recover_token(IN _username VARCHAR(30), IN _token VARCHAR(32))
				BEGIN
					DECLARE err_user_not_found CONDITION FOR SQLSTATE '45000';
					DECLARE selected_uid BIGINT DEFAULT 0;
					DECLARE selected_uname VARCHAR(30) DEFAULT '';
					DECLARE selected_email VARCHAR(256) DEFAULT '';

					SELECT u.uid, u.username, l.email INTO selected_uid, selected_uname, selected_email
					FROM users AS u
					INNER JOIN links_local AS l ON l.uid = u.uid
					WHERE _username IN (u.username, l.email);
					
					IF selected_uid = 0 THEN
						SIGNAL err_user_not_found;
					END IF;
					
					INSERT INTO pass_reset_tokens (target_uid, confirm_change_token)
					VALUES (selected_uid, _token)
					ON DUPLICATE KEY UPDATE confirm_change_token = VALUE(confirm_change_token), expires = VALUE(expires);
					
					SELECT selected_uid AS uid, selected_uname AS name, selected_email AS email;
				END
EOQ
			);
			
			$this->db->squery(<<<'EOQ'
				CREATE PROCEDURE IF NOT EXISTS use_verify_token(IN _target_uid BIGINT, IN _vtoken VARCHAR(32))
				BEGIN
					DECLARE err_invalid_token CONDITION FOR SQLSTATE '45000';
					DECLARE token_valid BOOLEAN DEFAULT false;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					DELETE FROM mail_verify_tokens
						WHERE NOW() > expires;
					
					START TRANSACTION;
					
					SELECT true INTO token_valid
						FROM mail_verify_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _vtoken;
					
					IF NOT token_valid THEN
						SIGNAL err_invalid_token;
					END IF;
					
					DELETE FROM mail_verify_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _vtoken;
					
					UPDATE links_local
						SET email_verified = true
						WHERE uid = _target_uid;
					
					COMMIT;
				END
EOQ
			);
			
			$this->db->squery(<<<'EOQ'
				CREATE OR REPLACE PROCEDURE create_verification_token(IN _uid BIGINT, IN _token VARCHAR(32))
				BEGIN
					DECLARE err_user_not_found CONDITION FOR SQLSTATE '45000';
					DECLARE selected_uid BIGINT DEFAULT 0;
					DECLARE selected_uname VARCHAR(30) DEFAULT '';
					DECLARE selected_email VARCHAR(256) DEFAULT '';

					SELECT u.uid, u.username, l.email INTO selected_uid, selected_uname, selected_email
					FROM users AS u
					INNER JOIN links_local AS l ON l.uid = u.uid
					WHERE u.uid = _uid;
					
					IF selected_uid = 0 THEN
						SIGNAL err_user_not_found;
					END IF;
					
					INSERT INTO mail_verify_tokens (target_uid, confirm_change_token)
					VALUES (selected_uid, _token)
					ON DUPLICATE KEY UPDATE confirm_change_token = VALUE(confirm_change_token), expires = VALUE(expires);
					
					SELECT selected_uid AS uid, selected_uname AS name, selected_email AS email;
				END
EOQ
			);
			
			/**********
			 * 
			 * TRIGGERS
			 * 
			 **********/
			
			$this->db->squery(<<<'EOQ'
				CREATE TRIGGER IF NOT EXISTS record_deleted_user_ad
				AFTER DELETE ON users
				FOR EACH ROW
				INSERT INTO deleted_users (uid, created, last_login, username)
				VALUES (OLD.uid, OLD.created, OLD.last_login, OLD.username)
EOQ
			);
		}
		
		
		
		
		
		
		public function get_user_info(int $uid) : ?array {
			return $this->db->pquery(<<<'EOQ'
				SELECT *
				FROM users
				WHERE uid = ?
EOQ
			, $uid)->fetch_assoc() ?? null;
		}

		public function get_local_acct_info(int $uid) : ?array {
			return $this->db->pquery(<<<'EOQ'
				SELECT *
				FROM links_local
				WHERE uid = ?
EOQ
			, $uid)->fetch_assoc() ?? null;
		}
		
		public function get_local_account_info(string $username) : ?array {
			return $this->db->pquery(<<<'EOQ'
				CALL get_local_account_info(?)
EOQ
			, $username)->fetch_assoc() ?? null;
		}
		
		public function register_local_account(string $username, string $email, string $hashed_pass) : mysqli_result|bool {
			return $this->db->pquery(<<<'EOQ'
				CALL register_local_account(?, ?, ?)
EOQ
			, $username, $email, $hashed_pass);
		}
		
		public function create_recovery_token_for(string $username) : array {
			$token = bin2hex(random_bytes(16));
			
			$data = $this->db->pquery(<<<'EOQ'
				CALL create_recover_token(?, ?)
EOQ
			, $username, $token)->fetch_assoc() ?? null;
			
			return [
				'token' => $token,
				'user' => $data
			];
		}
		
		public function check_recover_token_validity(int $uid, string $token) : bool {
			return $this->db->pquery(<<<'EOQ'
				SELECT 1 AS ok
				FROM pass_reset_tokens
				WHERE target_uid = ? AND confirm_change_token = ?
EOQ
			, $uid, $token)->fetch_assoc()['ok'] ?? false;
		}
		
		public function use_recover_token(int $uid, string $token, string $new_hashed_pass) : bool {
			return boolval($this->db->pquery(<<<'EOQ'
				CALL use_recover_token(?, ?, ?)
EOQ
			, $uid, $token, $new_hashed_pass));
		}
		
		
		public function create_verification_token_for(int $uid) : array {
			$token = bin2hex(random_bytes(16));
			
			$data = $this->db->pquery(<<<'EOQ'
				CALL create_verification_token(?, ?)
EOQ
			, $uid, $token)->fetch_assoc() ?? null;
			
			return [
				'token' => $token,
				'user' => $data
			];
		}
		
		public function check_verify_token_validity(int $uid, string $token) : bool {
			return $this->db->pquery(<<<'EOQ'
				SELECT 1 AS ok
				FROM mail_verify_tokens
				WHERE target_uid = ? AND confirm_change_token = ?
EOQ
			, $uid, $token)->fetch_assoc()['ok'] ?? false;
		}
		
		public function use_verify_token(int $uid, string $token) : bool {
			return boolval($this->db->pquery(<<<'EOQ'
				CALL use_verify_token(?, ?)
EOQ
			, $uid, $token));
		}
	}
?>