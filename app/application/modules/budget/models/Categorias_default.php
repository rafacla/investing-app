<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

		class categorias_default extends MY_Model {

			public function __construct() {
				parent::__construct();
				$this->table_name = 'categorias_default';
			}
		}
		