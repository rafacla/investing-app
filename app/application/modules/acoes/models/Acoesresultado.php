<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class Acoesresultado extends MY_Model {

		public function __construct() {
			parent::__construct();
			$this->table_name = 'appinv_vw_ordens_resultados';
		}
		
		public function get_all_byprofile($profile_id) {
			$sql = "call appinv_get_acoes_resultados('".$profile_id."')";
			
			$Q = $this->db->query($sql);
			

			if ($Q->num_rows() > 0) {
				foreach ($Q->result_array() as $row) {
					$data[] = $row;
				}
			}
			$Q->free_result();
			$Q->next_result();
			return $data;
		}

	}
