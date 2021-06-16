<?php
	class ApiCartDetailsController extends ApiRestController {
		public function handle_get() : bool {
			$cm = CarsModel::get_instance();
			$items = $_GET['i'] ?? null;
			
			if (!$items || !is_array($items)) {
				echo json_encode([
					'ok' => false,
					'err' => 'No items!'
				]);
				
				return true;
			}
			
			$items = array_map(fn($i) => array_map(fn($n) => intval($n), explode(',', $i)), $items);
			$items = array_filter($items, fn($i) => $i[0] > 0 && $i[1] > 0);
			
			if (count($items) == 0) {
				echo json_encode([
					'ok' => false,
					'err' => 'No items!'
				]);
				
				return true;
			}
			
			$itemMap = [];
			foreach ($items as $i) {
				$itemMap[$i[0]] = $i[1];
			}
			
			$cart = [
				'items' => [],
				'total' => 0
			];
			
			$details = $cm->get_details_of(array_map(fn($i) => $i[0], $items));

			foreach ($details as $car) {
				$qty = $itemMap[$car['car_id']] ?? null;
				
				if (!$qty) {
					echo json_encode([
						'ok' => false,
						'err' => 'Error while retrieving cars! ' . $car['car_id']
					]);
				
					return true;
				}
				
				$cart['items'][] = [
					'car_id' => $car['car_id'],
					'brand_name' => $car['brand_name'],
					'model' => $car['model'],
					'price_eur_cent' => $car['price_eur_cent'],
					'qty' => $qty
				];
			}
			
			foreach ($cart['items'] as $i) {
				$cart['total'] += $i['price_eur_cent'] * $i['qty'];
			}
			
			echo json_encode([
				'ok' => true,
				'data' => $cart
			]);
			
			return true;
		}
	}
?>