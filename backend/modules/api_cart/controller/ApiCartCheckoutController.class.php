<?php
	#[auth_required]
	class ApiCartCheckoutController extends ApiRestController {
		public function handle_post() : bool {
			$cm = CarsModel::get_instance();
			$am = AuthModel::get_instance();
			$items = self::get_json_post();
			
			$user = MwSessionLoader::get_user()->get_fields();
			$data = null;

			switch (MwSessionLoader::get_sess_type()) {
				case 'google':
					$data = $am->get_google_acct_info($user['uid']);
					break;
					
				case 'github':
					$data = $am->get_github_acct_info($user['uid']);
					break;
					
				case 'local':
					$data = $am->get_local_acct_info($user['uid']);
					break;
			}
			
			if (!$items || !is_array($items)) {
				echo json_encode([
					'ok' => false,
					'err' => 'No items!'
				]);
				
				return true;
			}
			
			if (!$data || !$data['email_verified']) {
				echo json_encode([
					'ok' => false,
					'err' => 'Email not verified!'
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
			
			foreach ($cart['items'] as &$i) {
				$cart['total'] += $i['price_eur_cent'] * $i['qty'];
				$i['price_eur'] = $i['price_eur_cent'] / 100.0;
			}
			
			$cart['total'] /= 100.0;
			
			$iid = null;
			try {
				$iid = $cm->checkout($user['uid'], $items);
			} catch (Exception $e) {
				echo json_encode([
					'ok' => false,
					'err' => $e->getMessage(),
					'code' => $e->getCode()
				]);
				
				return true;
			}
			
			try {
				$order_details = '';
				foreach ($cart['items'] as &$i) {
					$order_details .= "- {$i['qty']}x {$i['brand_name']} {$i['model']}, {$i['price_eur']} € each\n";
				}
						
				$mailer = new CurlMailjetMailer('JJCars');
				$mailer->send_mail($data['email'], 'Your JJCars receipt for Order #' . $iid, <<<EOM
Hello!

Here are the details of your order:
{$order_details}

Total: {$cart['total']} €

Thank you for shopping with us.

-- JJCars
EOM
				);
			} catch (Exception $e) { }
			
			echo json_encode([
				'ok' => true,
				'iid' => $iid
			]);
			
			return true;
		}
	}
?>