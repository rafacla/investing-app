<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

		class vw_categorias extends MY_Model {

			public function __construct() {
				parent::__construct();
				$this->table_name = 'vw_categorias';
			}
		}
		