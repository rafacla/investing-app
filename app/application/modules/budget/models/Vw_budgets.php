<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

	class vw_budgets extends MY_Model {

		public function __construct() {
			parent::__construct();

			$this->load->database();

			$this->vwBugets = "vw_budgets";
		}

		public function get($id) {
			//return $this->db->get_where($this->table_name, array($this->primary_key => $id))->row();
		}
		
		public function get_all_bymes($profile_id,$mesano='') {
			$data = array();
			if ($mesano=='')
				$mesano = date("Ym");
			
			$sql = "call MostraBudgets('".$mesano."','".$profile_id."')";
			
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
		
		public function get_receitas($profile_id,$mesano='') {
			$data = array();
			if ($mesano=='') {
				$mes = date("m");
				$ano = date("Y");
			} else {
				$mes = substr($mesano,4,2);
				$ano = substr($mesano,0,4);
			}
			
			$sql = "call GetReceitaAjustada('".$mes."','".$ano."','".$profile_id."')";
			//var_dump($sql);
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

		public function get_all($fields = '', $where = array(), $table ='vw_budgets',$limit = '', $order_by = '', $group_by = '', $or_where='') {
			$data = array();
		
			if ($fields != '') {
				$this->db->select($fields);
			}

			$this->db->from($table);
			
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

			$Q = $this->db->get('');

			if ($Q->num_rows() > 0) {
				foreach ($Q->result_array() as $row) {
					$data[] = $row;
				}
			}
			$Q->free_result();

			return $data;
		}

		/*
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
			return $this->db->update('transacoes', $data);
		}

		public function delete($id) {
			$this->db->where($this->primary_key, $id);

			return $this->db->delete($this->table_name);
		}*/
	}
