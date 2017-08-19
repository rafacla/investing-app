<?php

class AcoesAjustes extends Admin_Controller {
    function __construct() {
        parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('budget/Profile'));
		$this->load->model(array('acoes/Corretora'));
		$this->load->model(array('acoes/Acoesnota'));
		$this->load->model(array('acoes/Vw_acoesnota'));
		$this->load->model(array('acoes/Vw_acoesordens'));
		$this->load->model(array('acoes/Acoesordens'));
    }

    public function index($profile_uid, $corretora_id='', $data=array()) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($profile_uid !== $this->Profile->get($this->profile->id)->uniqueid) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {	
			if ($corretora_id == '') {
				$where = array('profile_id'=>$this->profile->id,'ajuste'=>'1');
			} else {
				$where = array('profile_id'=>$this->profile->id,'corretora_id'=>$corretora_id,'ajuste'=>'1');
			}
			$query = $this->Vw_acoesnota->get_all('',$where);
			$data['notas'] = $query;
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "ajustes_list";
			$data['lista']=$query;
			if ($corretora_id!=='') {
				$data['corretora_nome'] = $this->Corretora->get($corretora_id)->nome;
			} else {
				$data['corretora_nome'] = "Todas Corretoras";
			}
			
			$data['modulo'] = "investimentos";
			$data['title'] = "Notas de Ajustes";
			
			$this->load->view($this->_container, $data);
		}
    }
	
	public function deletaOrdem($ordem_id) {
		$ordem = $this->Vw_acoesordens->get_index($ordem_id,'ordem_id');
		if ($ordem_id== "" OR $ordem->profile_id !== $this->profile->id) {
				die ($this->acesso_negado);//Encerra com a msg de erro padrão.
		}
		$this->Acoesordens->delete($ordem_id);
		redirect(base_url($this->profile->uniqueid.'/acoes/ajustes/edit/'.$ordem->nota_id), 'refresh');
		die();
	}
	
	public function getPosicao() {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		}
		$data = $this->input->post('data');
		$ativo = $this->input->post('ativo');
		$corretora_id = $this->input->post('corretora_id');
		
		$sql = "SELECT ativo_nome, CMC FROM `appinv_vw_acoes_ordens_customedio` WHERE corretora_id = '".$corretora_id."' AND ativo_nome = '".$ativo."' AND data < '".$data."' ORDER BY data DESC, ordem_id DESC LIMIT 1";
		$sql1 = "SELECT sum(qt_exc) AS qt_final FROM `appinv_vw_acoes_ordens_customedio` WHERE corretora_id = '".$corretora_id."' AND ativo_nome = '".$ativo."' AND data < '".$data."'";
		if ($this->input->post('data') and $this->input->post('ativo') and $this->input->post('corretora_id')) {
			$dados = $this->db->query($sql,false)->row();
			$dados1 = $this->db->query($sql1,false)->row();
			$dados->qt_final = $dados1->qt_final;
			echo json_encode($dados);
		} else {
			echo json_encode("-1");
		}
		die();
	}
	
	public function edit($profile_id,$nota_id) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($profile_id !== $this->Profile->get($this->profile->id)->uniqueid) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($nota_id !== 'new' and $this->Vw_acoesnota->get_index($nota_id,'nota_id')->profile_id !== $this->profile->id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		}	elseif ($this->input->post('salvaNota')=="0") {
			if ($this->input->post('ordem_de_id')!== '' and $this->Vw_acoesordens->get_index($this->input->post('ordem_de_id'),'ordem_id')->profile_id !== $this->profile->id) {
				die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
			}	elseif ($this->input->post('ordem_para_id')!== '' and $this->Vw_acoesordens->get_index($this->input->post('ordem_para_id'),'ordem_id')->profile_id !== $this->profile->id) {
				die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
			}
			$dataNota['corretora_id'] = $this->input->post('corretora_id');
			$dataNota['nota_data'] = $this->input->post('data');
			$dataNota['nota_numero'] = $this->input->post('nota_numero');
			$dataNota['irpf_n'] = $this->input->post('irpf_n');
			$dataNota['irpf_dt'] = $this->input->post('irpf_dt');
			$dataNota['taxas_cblc'] = $this->input->post('cblc');
			$dataNota['taxas_bovespa'] = $this->input->post('bovespa');
			$dataNota['taxas_corretagem'] = $this->input->post('corretagem');
			if ($nota_id == 'new') {
				$nota_id = $this->Acoesnota->insert($dataNota);
			} else {
				$this->Acoesnota->update($dataNota,$nota_id);
			}
			$ordemDe['nota_id'] = $nota_id;
			$ordemDe['ativo_nome'] = $this->input->post('ativo_de');
			$ordemDe['operacao'] = "v";
			$ordemDe['tipo_operacao'] = "n";
			$ordemDe['ativo_quantidade'] = $this->input->post('qtde_de');
			if ($this->input->post('ativo_de')!=$this->input->post('ativo_para')) { 
				$ordemDe['ativo_valor'] = $this->input->post('cmc_de');
			} else {
				$ordemDe['ativo_valor'] = $this->input->post('cmc_para');
			}
			$ordemPara['nota_id'] = $nota_id;
			$ordemPara['ativo_nome'] = $this->input->post('ativo_para');
			$ordemPara['operacao'] = "c";
			$ordemPara['tipo_operacao'] = "n";
			$ordemPara['ativo_quantidade'] = $this->input->post('qtde_para');
			$ordemPara['ativo_valor'] = $this->input->post('cmc_para');
			if ($this->input->post('ordem_de_id')== '') {
				$nota_id = $this->Acoesordens->insert($ordemDe);
			} else {
				$this->Acoesordens->update($ordemDe,$this->input->post('ordem_de_id'));
			}
			if ($this->input->post('ordem_para_id')== '') {
				$nota_id = $this->Acoesordens->insert($ordemPara);
			} else {
				$this->Acoesordens->update($ordemPara,$this->input->post('ordem_para_id'));
			}
			redirect(base_url($this->profile->uniqueid.'/acoes/ajustes/edit/'.$nota_id), 'refresh');
		} else {	
			if ($nota_id == 'new') {
				$data['titulo'] = "Nova nota de Ajustes";
			} else {
				$nota = $this->Vw_acoesnota->get_index($nota_id,'nota_id');
				$ordens = $this->Vw_acoesordens->get_all('',array('nota_id'=>$nota_id));
				$data['titulo'] = "Editando a nota de ajustes".$nota->id." (".$nota->nome.")";
				$data['nota'] = $nota;
				$data['ordens'] = $ordens;
			}
			$data['nota_id'] = $nota_id;
			$data['corretoras'] = $this->Corretora->get_all('',array('profile_id' => $this->profile->id));
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "ajustes_edit";
			
			$data['modulo'] = "investimentos";
			$data['title'] = "Nota de Ajuste";
			
			$this->load->view($this->_container, $data);
		}
	}
	
	
    public function delete($nota_id) {
		if ($this->Vw_acoesnota->get_index($nota_id,'nota_id')->profile_id !== $this->profile->id) {
			die ($this->acesso_negado);
		}
		else {
			$this->Acoesnota->delete($nota_id);
			redirect(base_url($this->profile->uniqueid.'/acoes/ajustes/'), 'refresh');
			die();
		}
    }
}

