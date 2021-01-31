<?php
	require_once 'libs/utils/url.php';
	require_once 'libs/utils/html.php';
	require_once 'libs/utils/misc.php';
	require_once 'libs/mvc/inc.php';
	require_once __DIR__ . '/../model/ThreadsModel.php';
	
	class PageThreadsController extends Controller {
		private ThreadsModel $model;
		
		public function __construct() {
			$this->model = new ThreadsModel;
			$this->prepare();
		}
		
		public function prepare() : void {
			$uri = get_split_uri(1);
			
			switch ($uri[0] ?? 'list') {
				case 'list':
				case 'read':
				case 'create':
				case 'update':
				case 'delete':
					break;
					
				default:
					throw new Error('Invalid operation');
			}
		}
		
		public function get_title() : string {
			return 'Threads';
		}
		
		public function handle_post_http_head_create() : bool {
			try {
				$this->model->create_thread(
					$_POST['subject'] ?? null,
					$_POST['expires'] ?? null,
					$_POST['message'] ?? null);
				
				header('Location: /threads', true, 303);
			} catch (Exception $e) {
				header('Location: /threads/create#error-' . $e->getCode(), true, 303);
			}
			
			return true;
		}
		
		public function handle_post_http_head_update(int $tid) : bool {
			$thr = $this->model->get_thread($tid);
			
			try {
				if (is_null($thr)) {
					throw new Exception('No such thread', 1);
				}
				
				$thr['subject'] = $_POST['subject'] ?? null;
				$thr['expires'] = $_POST['expires'] ?? null;
				$thr['message'] = $_POST['message'] ?? null;
				
				$this->model->update_thread($thr);
				
				header('Location: /threads', true, 303);
			} catch (Exception $e) {
				//http_response_code(400);
				header('Location: /threads/update/' . $tid . '#error-' . $e->getCode(), true, 303);
			}
			
			return true;
		}
		
		public function handle_post_http_head_delete(int $tid) : bool {
			$this->model->delete_thread($tid);
			
			header('Location: /threads', true, 303);
		}
		
		public function handle_post_http_head_redir(int $tid) : bool {
			switch ($_POST['op'] ?? null) {
				case 'upd':
					header('Location: /threads/update/' . $tid, true, 303);
					break;
					
				case 'del':
					header('Location: /threads/delete/' . $tid, true, 307);
					break;
					
				default:
					return false;
			}
			
			return true;
		}
		
		public function handle_post_http_head() : bool {
			$uri = get_split_uri(1);

			switch ($uri[0] ?? null) {
				case 'create':
					return $this->handle_post_http_head_create();
					
				case 'update':
					return $this->handle_post_http_head_update(nintval($uri[1] ?? null));
					
				case 'delete':
					return $this->handle_post_http_head_delete(nintval($uri[1] ?? null));
					
				default:
					return $this->handle_post_http_head_redir(nintval($_POST['id'] ?? null));
			}
		}
		
		
		
		public function handle_get_body_list() : bool {
			$thr_new_action = '/threads/create';
			$threads = $this->model->get_all_threads();
			$next_thread = fn() => htmlesc($threads->fetch_assoc());
			
			require __DIR__ . '/../view/threads.phtml';
			return true;
		}
		
		public function handle_get_body_create() : bool {
			require __DIR__ . '/../view/thread_create.phtml';
			return true;
		}
		
		public function handle_get_body_update(int $tid) : bool {
			$thr = htmlesc($this->model->get_thread($tid));
			
			if (!is_null($thr)) {
				require __DIR__ . '/../view/thread_update.phtml';
			} else {
				require __DIR__ . '/../view/thread_update_noexists.phtml';
			}
			
			return true;
		}
		
		
		public function handle_get_special_read(int $tid) : bool {
			echo json_encode($this->model->get_thread($tid));
			
			return true;
		}

		public function handle_get_special() : bool {
			$uri = get_split_uri(1);

			switch ($uri[0] ?? 'list') {
				case 'read':
					return $this->handle_get_special_read(nintval($uri[1] ?? null));
			}
			
			return false;
		}

		
		public function handle_get_head() : void {
			$uri = get_split_uri(1);

			switch ($uri[0] ?? 'list') {
				case 'create':
				case 'update':
					require __DIR__ . '/../view/thread_create_head.phtml';
					break;
					
				default:
					require __DIR__ . '/../view/threads_head.phtml';
					break;
			}
		}

		public function handle_get_body() : bool {
			$uri = get_split_uri(1);

			switch ($uri[0] ?? 'list') {
				case 'list':
					return $this->handle_get_body_list();
					
				case 'create':
					return $this->handle_get_body_create();
					
				case 'update':
					return $this->handle_get_body_update(nintval($uri[1] ?? null));
			}
			
			return false;
		}
	}
?>