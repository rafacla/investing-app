<?php

class Contas extends Admin_Controller {
    function __construct() {
        parent::__construct();
		
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('budget/user'));
		$this->load->model(array('budget/conta'));
    }
	
	public function criaConta($profile_id) {
		function toNumber($target) {
			$switched = str_replace(',', '.', $target);
			if(is_numeric($target)){
				return floatval($target);
			}elseif(is_numeric($switched)){
				return floatval($switched);
			}
		}
	
		if ($this->input->post('conta_nome')!=""){
			$data['conta_nome'] = $this->input->post('conta_nome');
			$data['conta_descricao'] = $this->input->post('conta_descricao');
			$data['profile_id'] = $this->profile->id;
			if ($this->input->post('valor_reconciliado')!='') {
				$valor_r = $this->input->post('valor_reconciliado');
			} else {
				$valor_r = 0;
			}
			$data['reconciliado_valor']=toNumber($valor_r);
			$data['reconciliado_data'] = date("Y-m-d H:i:s");
			$data['created'] = date("Y-m-d H:i:s");
			$data['modified'] = $data['created'];
			$data['budget']=1;
			$this->db->insert('bud_contas', $data);
			//agora vamos criar a transação inicial:
			$dataTr['conta_id'] = $this->db->insert_id();
			$dataTr['data'] = date("Y-m-d H:i:s");
			$dataTr['sacado_nome'] = "Saldo inicial";
			$dataTr['valor'] = $data['reconciliado_valor'];
			$dataTr['conciliado'] = 1; 
			$dataTr['aprovado'] = 1;
			$dataTr['created'] = $data['created']; 
			$dataTr['modified'] = $data['created'];
			$this->db->insert('bud_transacoes', $dataTr);
			//item inicial
			$dataTrItem['categoria_id'] = 1;
			$dataTrItem['transacao_id'] = $this->db->insert_id();
			$dataTrItem['valor'] = $dataTr['valor'];
			$dataTrItem['created'] = $data['created'];
			$dataTrItem['modified'] = $data['created'];
			$this->db->insert('bud_transacoesitens', $dataTrItem);
		}
		header("Location: ".$this->input->post('url'));
		die();
	}
}
