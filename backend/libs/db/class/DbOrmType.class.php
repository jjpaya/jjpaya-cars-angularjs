<?php
	abstract class DbOrmType {
		protected array $fields = [];
		protected static ?array $db_info = null;
		
		protected function __construct(?array $fields) {
			if (!$fields) {
				throw new Exception('Inexistent record');
			}
			
			$this->fields = $fields;
		}
		
		public function get_fields() : array {
			return $this->fields;
		}
		
		protected static function get_db_info() : array {
			if (static::$db_info) {
				return static::$db_info;
			}
			
			$rct = new ReflectionClass(static::class);
			$attrs = $rct->getAttributes();
			
			$arr = [];
			
			foreach ($attrs as $a) {
				$arr[$a->getName()] = $a->getArguments();
			}
			
			static::$db_info = $arr;
			
			return static::$db_info;
		}
		
		public static function get_one(mixed $key) : ?static {
			$db_info = static::get_db_info();
			return new static(Database::get_connection()->pquery(<<<EOQ
				SELECT *
				FROM {$db_info['table'][0]}
				WHERE {$db_info['key'][0]} = ?
EOQ
			, $key)->fetch_assoc() ?? null);
		}
		
		public static function get_all() : array {
			$db_info = static::get_db_info();
			return array_map(fn($it) => new static($it), Database::get_connection()->pquery(<<<EOQ
				SELECT *
				FROM {$db_info['table'][0]}
EOQ
			)->fetch_all(MYSQLI_ASSOC) ?? []);
		}
	}
?>