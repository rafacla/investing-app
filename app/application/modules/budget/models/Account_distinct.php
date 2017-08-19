<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class Account extends MY_Model {

		public function __construct() {
			parent::__construct();

			$this->load->database();

			$this->tbItens = "bud_transacoesitens";
			$this->tbTransacoes = "bud_transacoes";
			$this->vwAccounts = "vw_accounts";
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
		
		public function get_all_noscape($fields = '', $where = array(), $table ='',$limit = '', $order_by = '', $group_by = '', $or_where='') {
			$data = array();
			if ($fields != '') {
				$this->db->select($fields);
			}

			$this->db->from($this->vwAccounts);
			
			if (count($where)) {
				$this->db->where($where,null,false);
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

		public function insert($data) {
			$data['date_created'] = $data['date_updated'] = date('Y-m-d H:i:s');
			$data['created_from_ip'] = $data['updated_from_ip'] = $this->input->ip_address();

			$success = $this->db->insert($this->table_name, $data);
			if ($success) {
				return $this->db->insert_id();
			} else {
				return FALSE;
			}
		}

		public function updateTransacao($data, $id) {
			$data['modified'] = date('Y-m-d H:i:s');

			$this->db->where($this->primary_key, $id);
			return $this->db->update('bud_transacoes', $data);
		}

		public function delete($id) {
			$this->db->where($this->primary_key, $id);

			return $this->db->delete($this->table_name);
		}
	}
