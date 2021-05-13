<?php
	class ApiCarsTotalController extends ApiRestController {
		public function handle_get() : bool {
			$mdl = CarsModel::get_instance();
			
			$containing = $_GET['containing'] ?? null;
			$max_kms = $_GET['max_kms'] ?? null;
			$brand_id = $_GET['brand_id'] ?? null;
			$wheel_drive = $_GET['wheel_drive'] ?? null;
			$max_price = $_GET['max_price'] ?? null;
			if (!is_null($max_price)) {
				$max_price *= 100;
			}
			
			echo json_encode($mdl->get_total_cars($containing,
					$max_kms, $brand_id, $wheel_drive, $max_price));
			
			return true;
		}
	}
?>