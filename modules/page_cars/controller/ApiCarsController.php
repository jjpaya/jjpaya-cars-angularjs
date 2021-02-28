<?php
	require_once 'libs/mvc/inc.php';

	abstract class ApiDbEndpoint extends ApiRestController {
		protected CarsModel $mdl;
		
		public function __construct(CarsModel $mdl) {
			$this->mdl = $mdl;
		}
	}
	
	class ApiCarsController extends MultiController {
		public function __construct(Model $mdl, int $depth = 2) {
			parent::__construct($depth);

			$this->add_route('brands', new class($mdl) extends ApiDbEndpoint {
				public function handle_get() : bool {
					$page = $_GET['page'] ?? 1;
					echo json_encode($this->mdl->get_brands_paged($page)
							->fetch_all(MYSQLI_ASSOC));
					
					return true;
				}
			});
			
			$this->add_route('brands/total', new class($mdl) extends ApiDbEndpoint {
				public function handle_get() : bool {
					echo json_encode($this->mdl->get_total_brands());
					return true;
				}
			});
			
			$this->add_route('cars', new class($mdl) extends ApiDbEndpoint {
				public function handle_get() : bool {
					$page = $_GET['page'] ?? 1;
					
					$data = $this->mdl->get_cars_paged($page)
							->fetch_all(MYSQLI_ASSOC);
					
					foreach ($data as &$car) {
						$car['imgs'] = $this->mdl->get_car_imgs($car['car_id']);
					}
					
					echo json_encode($data);
					
					return true;
				}
			});
			
			$this->add_route('cars/total', new class($mdl) extends ApiDbEndpoint {
				public function handle_get() : bool {
					echo json_encode($this->mdl->get_total_cars());
					return true;
				}
			});
		}
	}
?>