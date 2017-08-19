<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

		class Vw_transacoes extends MY_Model {

			public function __construct() {
				parent::__construct();
				$this->table_name = 'vw_transacoes';
			}
			
			public function saldo($profile_id,$conta_id='',$periodo='') {
				$data = array();
				
				$sql = "SELECT sum(valor) AS saldoAnterior FROM `vw_transacoes` `t1` JOIN `bud_contas` `t2` ON `t1`.`conta_id` = `t2`.`id` WHERE `t2`.`profile_id` = '" . $profile_id . "'";
				if ($conta_id != '' AND $conta_id != "all")
					$sql .= " AND `t1`.`conta_id` = '".$conta_id."'";
				if (is_numeric($periodo)) 
					$sql .= " AND `t1`.`data` <  now() - interval ".$periodo." day";
				$sql .= ";";
				$Q = $this->db->query($sql);

				if ($Q->num_rows() > 0) {
					foreach ($Q->result_array() as $row) {
						$data[] = $row;
					}
				}
				$Q->free_result();

				return $data;
			}
		}
		