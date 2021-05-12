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
					confirm_change_token BINARY(16) NOT NULL,
					expires DATETIME NOT NULL DEFAULT (NOW() + INTERVAL 6 HOUR),
					FOREIGN KEY(target_uid) REFERENCES users (uid) ON DELETE CASCADE
				)
EOQ
			);

			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS mail_verify_tokens (
					target_uid BIGINT NOT NULL PRIMARY KEY,
					confirm_change_token BINARY(16) NOT NULL,
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
	}
?>