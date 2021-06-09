<?php
	#[auth_required('admin')]
	class ApiCarsAdminController extends ApiRestController {
		/* Delete car */
		public function handle_delete() : bool {
			$cid = $_GET['id'] ?? null;
			
			if ($cid === null) {
				echo json_encode([
					'ok' => false,
					'err' => 'No id to delete'
				]);
				
				return true;
			}
			
			$cm = CarsModel::get_instance();
			$cm->delete_car($cid);
			
			echo json_encode([
				'ok' => true
			]);
			
			return true;
		}
		
		/* Update car */
		public function handle_patch() : bool {
			$post = self::get_json_post();
			$cm = CarsModel::get_instance();
			$car = $cm->get_car($post['car_id']);
			
			try {
				if (is_null($car)) {
					throw new Exception('No such car', 1);
				}
				
				$car['num_plate'] = $post['num_plate'] ?? null;
				$car['reg_date'] = $post['reg_date'] ?? null;
				$car['brand_id'] = nintval($post['brand_id'] ?? null);
				$car['model'] = $post['model'] ?? null;
				$car['color'] = $post['color'] ?? null;
				$car['kms'] = nintval($post['kms'] ?? null);
				$car['wheel_power'] = $post['wheel_power'] ?? null;
				$car['itv'] = ($post['itv'] ?? null) === 'on' ? 1 : 0;
				$car['price_eur_cent'] = nintval($post['price_eur_cent'] ?? null);
				$car['description'] = $post['description'] ?? null;
				
				$cm->update_car($car);
				
				echo json_encode([
					'ok' => true
				]);
			} catch (Exception $e) {
				echo json_encode([
					'ok' => false,
					'code' => $e->getCode(),
					'err' => $e->getMessage()
				]);
			}
			
			return true;
		}
		
		/* Create car */
		public function handle_post() : bool {
			$post = self::get_json_post();
			$cm = CarsModel::get_instance();
			
			try {
				$cm->create_car(
					$post['num_plate'] ?? null,
					$post['reg_date'] ?? null,
					nintval($post['brand_id'] ?? null),
					$post['model'] ?? null,
					$post['color'] ?? null,
					nintval($post['kms'] ?? null),
					($post['itv'] ?? null) === 'on',
					$post['wheel_power'] ?? null,
					nintval($post['price_eur_cent'] ?? null),
					$post['description'] ?? null);
				
				echo json_encode([
					'ok' => true
				]);
			} catch (Exception $e) {
				// duplicate errc: 1062
				// constraint errc: 4025
				echo json_encode([
					'ok' => false,
					'code' => $e->getCode(),
					'err' => $e->getMessage()
				]);
			}
			
			return true;
		}
	}
?>