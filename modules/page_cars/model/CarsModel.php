<?php
	require_once 'libs/mvc/inc.php';

	class CarsModel extends Model {
		
		public function __construct() {
			parent::__construct();
			$this->db_setup();
		}
		
		private function db_setup() : void {
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS car_brands (
					brand_id SERIAL PRIMARY KEY,
					name VARCHAR(24) NOT NULL UNIQUE CHECK (name REGEXP '^[a-z ]+$'),
					img VARCHAR(255)
				)
EOQ
			);
			
			/*$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS car_tags (
					tag_id BIGINT UNSIGNED,
					model_id SERIAL,
					name VARCHAR(24) NOT NULL CHECK (name REGEXP '^[a-z0-9 ]+$')
				)
EOQ
			);*/
			
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS cars (
					car_id SERIAL PRIMARY KEY,
					num_plate VARCHAR(7) NOT NULL UNIQUE CHECK (num_plate REGEXP '[0-9]{4}[A-Z]{3}'),
					reg_date DATE NOT NULL,
					brand_id BIGINT UNSIGNED NOT NULL,
					model VARCHAR(24) NOT NULL CHECK (model REGEXP '^[a-zA-Z0-9 ]+$'),
					color VARCHAR(7) NOT NULL CHECK (color REGEXP '^#[a-zA-Z0-9]{6}$'),
					kms INT NOT NULL DEFAULT 0 CHECK (kms >= 0 AND kms <= 999999),
					itv BOOLEAN NOT NULL,
					wheel_power ENUM('front', 'rear', 'all') NOT NULL,
					price_eur_cent INT NOT NULL CHECK (price_eur_cent >= 10000 AND price_eur_cent <= 500000000),
					created DATE NOT NULL DEFAULT CURDATE(),
					description VARCHAR(255) NOT NULL,
					
					FOREIGN KEY(brand_id) REFERENCES car_brands (brand_id)
				)
EOQ
			);
			
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS car_images (
					img_id SERIAL PRIMARY KEY,
					car_id BIGINT UNSIGNED,
					path VARCHAR(255) NOT NULL,
					
					FOREIGN KEY(car_id) REFERENCES cars (car_id) ON DELETE CASCADE
				)
EOQ
			);
		}
		
		public function get_all_brands() : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
				SELECT *
				FROM car_brands
				ORDER BY name ASC
EOQ
			);
		}
		
		public function get_brands_paged(int $page, int $limit = 4) : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
				SELECT *
				FROM car_brands
				ORDER BY name ASC
				LIMIT ?
				OFFSET ?
EOQ
			, $limit, ($page - 1) * $limit);
		}
		
		public function get_total_brands() : int {
			return intval($this->db->pquery(<<<'EOQ'
				SELECT COUNT(*)
				FROM car_brands
EOQ
			)->fetch_array()[0]);
		}
		
		public function get_all_cars() : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
				SELECT c.*, br.name AS brand_name
				FROM cars AS c
				INNER JOIN car_brands AS br ON c.brand_id = br.brand_id
				ORDER BY car_id DESC
EOQ
			);
		}
		
		public function get_cars_paged(int $page, int $limit = 10) : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
				SELECT c.*, br.name AS brand_name
				FROM cars AS c
				INNER JOIN car_brands AS br ON c.brand_id = br.brand_id
				ORDER BY car_id DESC
				LIMIT ?
				OFFSET ?
EOQ
			, $limit, ($page - 1) * $limit);
		}
		
		public function get_total_cars() : int {
			return intval($this->db->pquery(<<<'EOQ'
				SELECT COUNT(*)
				FROM cars
EOQ
			)->fetch_array()[0]);
		}
		
		
		public function get_car(int $cid) : ?array {
			return $this->db->pquery('SELECT * FROM cars WHERE car_id = ?', $cid)
					->fetch_assoc() ?? null;
		}
		
		public function get_car_imgs(int $cid) : array {
			return $this->db->pquery('SELECT * FROM car_images WHERE car_id = ?', $cid)
					->fetch_all(MYSQLI_ASSOC) ?? array();
		}
		
		public function create_car_brand(?string $name, ?string $img) : void {
			$this->db->pquery(<<<'EOQ'
				INSERT INTO cars (name, img)
				VALUES (?, ?)
EOQ
			, $name, $img);
		}
		
		public function create_car(
				?string $num_plate, ?string $reg_date, ?int $brand_id,
				?string $model, ?string $color, ?int $kms, ?int $itv,
				?string $wheel_power, ?int $price_eur_cent,
				?string $description) : void {

			$this->db->pquery(<<<'EOQ'
				INSERT INTO cars (num_plate, reg_date, brand_id, model, color, kms, itv, wheel_power, price_eur_cent, description)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
EOQ
			, $num_plate, $reg_date, $brand_id,
			  $model, $color, $kms, $itv, $wheel_power,
			  $price_eur_cent, $description);
		}
		
		public function update_car(array $car) : void {
			$this->db->pquery(<<<'EOQ'
				UPDATE cars
				SET num_plate = ?, reg_date = ?, brand_id = ?, model = ?,
					color = ?, kms = ?, itv = ?, wheel_power = ?,
					price_eur_cent = ?, description = ?
				WHERE car_id = ?
EOQ
			, $car['num_plate'], $car['reg_date'], $car['brand_id'],
			  $car['model'], $car['color'], $car['kms'], $car['itv'],
			  $car['wheel_power'], $car['price_eur_cent'], $car['description'],
			  $car['car_id']);
		}
		
		public function delete_car(int $cid) : void {
			$this->db->pquery('DELETE FROM cars WHERE car_id = ?', $cid);
		}
	}
?>