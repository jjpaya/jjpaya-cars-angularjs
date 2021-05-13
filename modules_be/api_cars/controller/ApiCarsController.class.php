<?php
	class ApiCarsController extends ApiRestController {
		public function handle_get() : bool {
			$mdl = CarsModel::get_instance();
			
			$page = $_GET['page'] ?? 1;
			$order = $_GET['order'] ?? 1;
			$containing = $_GET['containing'] ?? null;
			$max_kms = $_GET['max_kms'] ?? null;
			$brand_id = $_GET['brand_id'] ?? null;
			$wheel_drive = $_GET['wheel_drive'] ?? null;
			$max_price = $_GET['max_price'] ?? null;
			if (!is_null($max_price)) {
				$max_price *= 100;
			}
			
			$data = $mdl->get_cars_paged($page, 10, $order,
					$containing, $max_kms, $brand_id, $wheel_drive, $max_price)
					->fetch_all(MYSQLI_ASSOC);
			
			foreach ($data as &$car) {
				$car['imgs'] = $mdl->get_car_imgs($car['car_id']);
			}
			
			echo json_encode($data);
			
			return true;
		}
		
		public static function get_subcontroller(array $url_path) : ?string {
			switch ($url_path[0] ?? '.') {
				case 'brands':  return ApiCarsBrandsController::class;
				case 'total':   return ApiCarsTotalController::class;
				case 'details': return ApiCarsDetailsController::class;
				case 'search':  return ApiCarsSearchController::class;
				case 'starred': return ApiCarsStarredController::class;
				case '.':       return null; // this controller
			}
			
			return Err404Controller::class;
		}
	}
?>