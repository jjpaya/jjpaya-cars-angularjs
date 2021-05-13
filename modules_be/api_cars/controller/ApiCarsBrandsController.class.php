<?php
	class ApiCarsBrandsController extends ApiRestController {
		public function handle_get() : bool {
			$mdl = CarsModel::get_instance();
			
			$page = $_GET['page'] ?? 1;
			$limit = $_GET['limit'] ?? 4;
			
			echo json_encode($mdl->get_brands_paged($page, $limit)
					->fetch_all(MYSQLI_ASSOC));
			
			return true;
		}
		
		public static function get_subcontroller(array $url_path) : ?string {
			switch ($url_path[0] ?? '.') {
				case 'total': return ApiCarsBrandsTotalController::class;
				case '.':     return null; // this controller
			}
			
			return Err404Controller::class;
		}
	}
?>