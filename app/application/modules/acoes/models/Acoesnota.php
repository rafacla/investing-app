<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class Acoesnota extends MY_Model {

		public function __construct() {
			parent::__construct();
			$this->table_name = 'appinv_acoes_notas';
		}

	}
