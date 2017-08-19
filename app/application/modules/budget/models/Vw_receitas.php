<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

		class vw_receitas extends MY_Model {

			public function __construct() {
				parent::__construct();
				$this->table_name = 'vw_receitas';
			}
			
			public function get_receitas($profile_uid) {
				$data = array();
		
				$this->db->from($this->table_name);
				
				$where['profile_uid'] = $profile_uid;
				if (count($where)) {
					$this->db->where($where);
				}

				$Q = $this->db->get('');

				if ($Q->num_rows() > 0) {
					foreach ($Q->result_array() as $row) {
						$data[$row['mesano']] = $row['ReceitaMes'];	
					}
					
				}
				$Q->free_result();

				return $data;
			}
		}
		