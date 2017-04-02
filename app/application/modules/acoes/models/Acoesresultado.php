<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class Acoesresultado extends MY_Model {

		public function __construct() {
			parent::__construct();
			$this->table_name = 'appinv_vw_ordens_resultados';
		}

	}
