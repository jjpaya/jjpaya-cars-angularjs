<?php
	class ApiCarsSearchController extends ApiRestController {
		public function handle_get() : bool {
			$mdl = CarsModel::get_instance();
			
			$query = $_GET['q'] ?? null;
			
			if (is_null($query)) {
				echo '[]';
				return true;
			}
			
			echo json_encode($mdl->find_cars($query)
					->fetch_all(MYSQLI_ASSOC));
			
			return true;
		}
	}
?>