<?php
	class ApiCarsDetailsController extends ApiRestController {
		public function handle_get() : bool {
			$mdl = CarsModel::get_instance();
			
			$cid = $_GET['id'] ?? 0;
			
			$car = $mdl->get_car($cid);
			if (!is_null($car)) {
				$car['imgs'] = $mdl->get_car_imgs($cid);
				$mdl->increase_car_views($cid);
				$car['views']++; // to match the new viewcount
			}

			echo json_encode($car);
	
			return true;
		}
	}
?>