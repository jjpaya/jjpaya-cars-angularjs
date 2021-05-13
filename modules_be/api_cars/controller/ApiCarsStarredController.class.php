<?php
	#[auth_required]
	class ApiCarsStarredController extends ApiRestController {
		private CarsModel $mdl;
		
		public function __construct() {
			$this->mdl = CarsModel::get_instance();
		}
		
		public function handle_get() : bool {
			$uid = MwSessionLoader::get_user()->get_fields()['uid'];
			
			echo json_encode($this->mdl->get_all_starred_cars_of($uid)
					->fetch_all(MYSQLI_ASSOC) ?? []);
			
			return true;
		}
		
		public function handle_put() : bool {
			$uid = MwSessionLoader::get_user()->get_fields()['uid'];
			$cid = $_GET['cid'] ?? 0;
			
			try {
				$this->mdl->add_car_as_starred($cid, $uid);
				
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
		
		public function handle_delete() : bool {
			$uid = MwSessionLoader::get_user()->get_fields()['uid'];
			$cid = $_GET['cid'] ?? 0;
			
			try {
				$this->mdl->del_car_from_starred($cid, $uid);
				
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