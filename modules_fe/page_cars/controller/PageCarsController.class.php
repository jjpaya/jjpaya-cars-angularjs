<?php
	class PageCarsController extends MultiController {
		private PageCarsModel $model;
		
		public function __construct() {
			parent::__construct();
			
			$this->model = new PageCarsModel;
			
			$this->add_route('/', new CrudCarsController($this->model));
			$this->add_route('api', new ApiCarsController($this->model));
		}
	}
?>