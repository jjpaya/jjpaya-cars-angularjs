<?php
	class CarsModel extends Model {
		private static ?CarsModel $instance = null;

		private function __construct() {
			parent::__construct();
			$this->db_setup();
		}
		
		public static function get_instance() : CarsModel {
			if (is_null(self::$instance)) {
				self::$instance = new self;
			}
			
			return self::$instance;
		}
		
		
		private function db_setup() : void {
			
			/**********
			 * 
			 * TABLES
			 * 
			 **********/
			 
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
					lat DOUBLE,
					lon DOUBLE,
					views INT NOT NULL DEFAULT 0,
					
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
			
			$this->db->pquery(<<<'EOQ'
				CREATE TABLE IF NOT EXISTS starred_cars (
					car_id BIGINT UNSIGNED,
					uid BIGINT NOT NULL,
					starred_on DATETIME NOT NULL DEFAULT NOW(),
					
					PRIMARY KEY(car_id, uid),
					FOREIGN KEY(car_id) REFERENCES cars (car_id) ON DELETE CASCADE,
					FOREIGN KEY(uid) REFERENCES users (uid) ON DELETE CASCADE
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
		
		public function get_all_starred_cars_of(int $uid) : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
				SELECT *
				FROM starred_cars
				WHERE uid = ?
				ORDER BY starred_on DESC
EOQ
			, $uid);
		}
		
		public function add_car_as_starred(int $cid, int $uid) : void {
			$this->db->pquery(<<<'EOQ'
				INSERT INTO starred_cars (car_id, uid)
				VALUES (?, ?)
EOQ
			, $cid, $uid);
		}
		
		public function del_car_from_starred(int $cid, int $uid) : void {
			$this->db->pquery(<<<'EOQ'
				DELETE FROM starred_cars
				WHERE car_id = ? AND uid = ?
EOQ
			, $cid, $uid);
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
		
		public function find_cars(string $containing, int $limit = 10) : mysqli_result {
			return $this->db->pquery(<<<'EOQ'
				SELECT DISTINCT CONCAT(br.name, ' ', model) AS name
				FROM cars AS c
				INNER JOIN car_brands AS br ON c.brand_id = br.brand_id
				WHERE CONCAT(br.name, ' ', model) LIKE CONCAT('%', ?, '%')
				ORDER BY br.name DESC, model DESC, car_id DESC
				LIMIT ?
EOQ
			, $containing, $limit);
		}
	
		private function make_filtered_cars_sql(?string $containing, ?int $max_kms,
				?int $brand_id, ?string $wheel_drive, ?int $max_price, bool $paged = true) : string {
			$sql = <<<'EOQ'
				FROM cars AS c
				INNER JOIN car_brands AS br ON c.brand_id = br.brand_id
				WHERE 1=1
EOQ;
			if (!is_null($containing)) {
				$sql .= ' AND CONCAT(br.name, \' \', model) LIKE CONCAT(\'%\', ?, \'%\')';
			}
			
			if (!is_null($max_kms))     { $sql .= ' AND kms <= ?'; }
			if (!is_null($brand_id))    { $sql .= ' AND c.brand_id = ?'; }
			if (!is_null($wheel_drive)) { $sql .= ' AND wheel_power = ?'; }
			if (!is_null($max_price))   { $sql .= ' AND price_eur_cent <= ?'; }
			
			$sql .= <<<'EOQ'
				ORDER BY 
					CASE ?
						WHEN 1 THEN car_id
    					WHEN 2 THEN views
    					WHEN 3 THEN price_eur_cent
    				END DESC
EOQ;
			
			if ($paged) { $sql .= ' LIMIT ? OFFSET ?'; }
			
			return $sql;
		}
		
		public function get_cars_paged(int $page, int $limit = 10, int $ordering = 1,
				?string $containing = null, ?int $max_kms = null, ?int $brand_id = null,
				?string $wheel_drive = null, ?int $max_price = null) : mysqli_result {
					
			$params = array_values(array_filter(array(
				$containing, $max_kms, $brand_id, $wheel_drive, $max_price,
				$ordering, $limit, ($page - 1) * $limit
			), function($el) {
				return !is_null($el);
			}));
			
			return $this->db->pquery('SELECT c.*, br.name AS brand_name'
				. $this->make_filtered_cars_sql($containing, $max_kms, $brand_id, $wheel_drive, $max_price)
			, ...$params);
		}
		
		public function get_total_cars(
				?string $containing = null, ?int $max_kms = null, ?int $brand_id = null,
				?string $wheel_drive = null, ?int $max_price = null) : int {
			
			$params = array_values(array_filter(array(
				$containing, $max_kms, $brand_id, $wheel_drive, $max_price, 1
			), function($el) {
				return !is_null($el);
			}));
			
			return $this->db->pquery('SELECT COUNT(*)'
				. $this->make_filtered_cars_sql($containing, $max_kms, $brand_id, $wheel_drive, $max_price, false)
			, ...$params)->fetch_array()[0];
		}
		
		
		public function get_car(int $cid) : ?array {
			return $this->db->pquery(<<<'EOQ'
				SELECT c.*, br.name AS brand_name
				FROM cars AS c
				INNER JOIN car_brands AS br ON c.brand_id = br.brand_id
				WHERE car_id = ?
EOQ
			, $cid)->fetch_assoc() ?? null;
		}
		
		public function increase_car_views(int $cid) : void {
			$this->db->pquery('UPDATE cars SET views = views + 1 WHERE car_id = ?', $cid);
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