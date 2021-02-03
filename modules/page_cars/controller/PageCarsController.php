<?php
	require_once 'libs/utils/url.php';
	require_once 'libs/utils/html.php';
	require_once 'libs/utils/misc.php';
	require_once 'libs/mvc/inc.php';
	require_once __DIR__ . '/../model/CarsModel.php';
	
	class PageCarsController extends Controller {
		private CarsModel $model;
		
		public function __construct() {
			$this->model = new CarsModel;
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
			return 'Cars';
		}
		
		public function handle_post_http_head_create() : bool {
			try {
				$this->model->create_car(
					$_POST['num_plate'] ?? null,
					$_POST['reg_date'] ?? null,
					nintval($_POST['brand_id'] ?? null),
					$_POST['model'] ?? null,
					$_POST['color'] ?? null,
					nintval($_POST['kms'] ?? null),
					($_POST['itv'] ?? null) === 'on',
					$_POST['wheel_power'] ?? null,
					nintval($_POST['price_eur_cent'] ?? null),
					$_POST['description'] ?? null);
				
				header('Location: /cars', true, 303);
			} catch (Exception $e) {
				// duplicate errc: 1062
				// constraint errc: 4025
				die($e->getMessage());
				header('Location: /cars/create#error-' . $e->getCode(), true, 303);
			}
			
			return true;
		}
		
		public function handle_post_http_head_update(int $cid) : bool {
			$car = $this->model->get_car($cid);
			
			try {
				if (is_null($car)) {
					throw new Exception('No such car', 1);
				}
				
				$car['num_plate'] = $_POST['num_plate'] ?? null;
				$car['reg_date'] = $_POST['reg_date'] ?? null;
				$car['brand_id'] = nintval($_POST['brand_id'] ?? null);
				$car['model'] = $_POST['model'] ?? null;
				$car['color'] = $_POST['color'] ?? null;
				$car['kms'] = nintval($_POST['kms'] ?? null);
				$car['wheel_power'] = $_POST['wheel_power'] ?? null;
				$car['itv'] = ($_POST['itv'] ?? null) === 'on' ? 1 : 0;
				$car['price_eur_cent'] = nintval($_POST['price_eur_cent'] ?? null);
				$car['description'] = $_POST['description'] ?? null;
				
				$this->model->update_car($car);
				
				header('Location: /cars', true, 303);
			} catch (Exception $e) {
				die($e->getMessage());
				header('Location: /cars/update/' . $cid . '#error-' . $e->getCode(), true, 303);
			}
			
			return true;
		}
		
		public function handle_post_http_head_delete(int $cid) : bool {
			$this->model->delete_car($cid);
			
			header('Location: /cars', true, 303);
		}
		
		public function handle_post_http_head_redir(int $cid) : bool {
			switch ($_POST['op'] ?? null) {
				case 'upd':
					header('Location: /cars/update/' . $cid, true, 303);
					break;
					
				case 'del':
					header('Location: /cars/delete/' . $cid, true, 307);
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
			$car_new_action = '/cars/create';
			$cars = $this->model->get_all_cars();
			$next_car = fn() => htmlesc($cars->fetch_assoc());
			
			require __DIR__ . '/../view/cars.phtml';
			return true;
		}
		
		public function handle_get_body_create() : bool {
			$brands = $this->model->get_all_brands();
			$next_brand = fn() => htmlesc($brands->fetch_assoc());
			
			require __DIR__ . '/../view/car_create.phtml';
			return true;
		}
		
		public function handle_get_body_update(int $cid) : bool {
			$brands = $this->model->get_all_brands();
			$next_brand = fn() => htmlesc($brands->fetch_assoc());
			$car = htmlesc($this->model->get_car($cid));
			
			if (!is_null($car)) {
				require __DIR__ . '/../view/car_update.phtml';
			} else {
				require __DIR__ . '/../view/car_update_noexists.phtml';
			}
			
			return true;
		}
		
		
		public function handle_get_special_read(int $cid) : bool {
			echo json_encode($this->model->get_car($cid));
			
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
					require __DIR__ . '/../view/car_create_head.phtml';
					break;
					
				default:
					require __DIR__ . '/../view/cars_head.phtml';
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