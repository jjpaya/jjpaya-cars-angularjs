<?php
	require_once 'libs/mvc/inc.php';
	require_once 'CrudCarsController.php';
	require_once 'ApiCarsController.php';
	
	class PageCarsController extends MultiController {
		private CarsModel $model;
		
		public function __construct() {
			parent::__construct();
			
			$this->model = new CarsModel;
			
			$this->add_route('/', new CrudCarsController($this->model));
			$this->add_route('api', new ApiCarsController($this->model));
		}
	}
?>