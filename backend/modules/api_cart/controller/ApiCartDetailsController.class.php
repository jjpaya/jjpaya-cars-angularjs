<?php
	class ApiCartDetailsController extends ApiRestController {
		public function handle_get() : bool {
			
			echo json_encode([
				'stype' => $stype,
				'data' => $udatapload
			]);
			
			return true;
		}
	}
?>