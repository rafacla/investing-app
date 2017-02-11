<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class Vw_Contas_Saldo extends MY_Model {

		public function __construct() {
			parent::__construct();

			$this->load->database();

			$this->vwAccounts = "vw_contas_saldo";
		}

		public function get($id) {
			//return $this->db->get_where($this->table_name, array($this->primary_key => $id))->row();
		}

		public function get_all($fields = '', $where = array(), $table ='',$limit = '', $order_by = '', $group_by = '', $or_where='') {
			$data = array();
			if ($fields != '') {
				$this->db->select($fields);
			}

			$this->db->from($this->vwAccounts);
			
			if (count($where)) {
				$this->db->where($where);
			}

			if ($limit != '') {
				$this->db->limit($limit);
			}

			if ($order_by != '') {
				$this->db->order_by($order_by);
			}

			if ($group_by != '') {
				$this->db->group_by($group_by);
			}

			$Q = $this->db->get();

			if ($Q->num_rows() > 0) {
				foreach ($Q->result_array() as $row) {
					$data[] = $row;
				}
			}
			$Q->free_result();

			return $data;
		}
	}
