<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class Vw_acoesordens extends MY_Model {

		public function __construct() {
			parent::__construct();
			$this->table_name = 'appinv_vw_acoes_ordens';
		}
		
		public function compras_byProfile($profile_id) {
			$this->db->select('nota_data, sum(ativo_trans_valor)+sum(taxa_cblc)+sum(taxa_bovespa)+sum(taxa_corretagem) AS compras');
			$this->db->where("(operacao = 'c' AND profile_id='".$profile_id."')");
			$this->db->group_by("nota_data");
			$Q = $this->db->get('appinv_vw_acoes_ordens'); 
			
			if ($Q->num_rows() > 0) {
				foreach ($Q->result_array() as $row) {
					$data[] = $row;
				}
			}
			$Q->free_result();

			return $data;
		}
		
		public function vendas_byProfile($profile_id) {
			$this->db->select('nota_data, sum(ativo_trans_valor)-sum(taxa_cblc)-sum(taxa_bovespa)-sum(taxa_corretagem) AS vendas');
			$this->db->where("(operacao = 'v' AND profile_id='".$profile_id."')");
			$this->db->group_by("nota_data");
			$Q = $this->db->get('appinv_vw_acoes_ordens'); 
			
			if ($Q->num_rows() > 0) {
				foreach ($Q->result_array() as $row) {
					$data[] = $row;
				}
			}
			$Q->free_result();

			return $data;
		}
	}
