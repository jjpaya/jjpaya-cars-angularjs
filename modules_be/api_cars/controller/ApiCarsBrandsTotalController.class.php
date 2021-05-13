<?php
	class ApiCarsBrandsController extends ApiRestController {
		public function handle_get() : bool {
			$mdl = CarsModel::get_instance();
			
			echo json_encode($mdl->get_total_brands());
			return true;
		}
	}
?>