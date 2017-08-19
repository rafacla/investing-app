<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

		class vw_mes_gasto extends MY_Model {

			public function __construct() {
				parent::__construct();
				$this->table_name = 'vw_mes_gasto';
			}
			
			public function get_all($fields = '', $where = array(), $table ='',$limit = '', $order_by = '', $group_by = '', $or_where='') {
				$data = array();
		
				if ($fields != '') {
					$this->db->select($fields);
				}
				if ($table!='')
					$this->db->from($table);
				else
					$this->db->from($this->table_name);
				
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

				$maxmesano = 0;
				$minmesano = 999999;
				if ($Q->num_rows() > 0) {
					foreach ($Q->result_array() as $row) {
						$data[$row['mesano']][$row['categoriaitem_id']] = $row;
						if ($row['mesano'] > $maxmesano)
							$maxmesano = $row['mesano'];
						if ($row['mesano'] < $minmesano) 
							$minmesano = $row['mesano'];						
							
						$data['minmesano'] = $minmesano;
						$data['maxmesano'] = $maxmesano;
					}
				}
				$Q->free_result();

				return $data;
			}
		}
		