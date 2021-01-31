<?php
	require_once 'libs/mvc/inc.php';

	class ThreadsModel extends Model {
		
		public function __construct() {
			parent::__construct();
			$this->db_setup();
		}
		
		private function db_setup() : void {
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS threads (
					thread_id SERIAL PRIMARY KEY,
					subject VARCHAR(64) NOT NULL,
					created DATE NOT NULL DEFAULT CURDATE(),
					expires DATE NOT NULL DEFAULT (CURDATE() + INTERVAL 5 DAY),
					message VARCHAR(255) NOT NULL
				)
EOQ
			);
		}
		
		public function get_all_threads() : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
			SELECT *
			FROM threads
			WHERE expires >= CURDATE()
			ORDER BY thread_id DESC
EOQ
			);
		}
		
		public function get_thread(int $tid) : ?array {
			return $this->db->pquery('SELECT * FROM threads WHERE thread_id = ?', $tid)
					->fetch_assoc() ?? null;
		}
		
		public function create_thread(
				?string $subject, ?string $expires, ?string $message) : void {

			$this->db->pquery(<<<'EOQ'
			INSERT INTO threads (subject, expires, message)
			VALUES (?, ?, ?)
EOQ
			, $subject, $expires, $message);
		}
		
		public function update_thread(array $thr) : void {
			$this->db->pquery(<<<'EOQ'
			UPDATE threads
			SET subject = ?, expires = ?, message = ?
			WHERE thread_id = ?
EOQ
			, $thr['subject'], $thr['expires'],
			  $thr['message'], $thr['thread_id']);
		}
		
		public function delete_thread(int $tid) : void {
			$this->db->pquery('DELETE FROM threads WHERE thread_id = ?', $tid);
		}
	}
?>