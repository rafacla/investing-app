<?php

class AcoesNotas extends Admin_Controller {
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
				$where = array('profile_id'=>$this->profile->id,'ajuste'=>'0');
			} else {
				$where = array('profile_id'=>$this->profile->id,'corretora_id'=>$corretora_id,'ajuste'=>'0');
			}
			$query = $this->Vw_acoesnota->get_all('',$where,'','','nota_data');
			$data['notas'] = $query;
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "notas_list";
			$data['lista']=$query;
			if ($corretora_id!=='') {
				$data['corretora_nome'] = $this->Corretora->get($corretora_id)->nome;
			} else {
				$data['corretora_nome'] = "Todas Corretoras";
			}
			
			$data['modulo'] = "investimentos";
			$data['title'] = "Notas de Corretagem";
			
			$this->load->view($this->_container, $data);
		}
    }
	
	public function deletaOrdem($ordem_id) {
		$ordem = $this->Vw_acoesordens->get_index($ordem_id,'ordem_id');
		if ($ordem_id== "" OR $ordem->profile_id !== $this->profile->id) {
				die ($this->acesso_negado);//Encerra com a msg de erro padrão.
		}
		$this->Acoesordens->delete($ordem_id);
		redirect(base_url($this->profile->uniqueid.'/acoes/notas/edit/'.$ordem->nota_id), 'refresh');
		die();
	}
	
	public function edit($profile_id,$nota_id) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($profile_id !== $this->Profile->get($this->profile->id)->uniqueid) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($nota_id !== 'new' and $this->Vw_acoesnota->get_index($nota_id,'nota_id')->profile_id !== $this->profile->id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($this->input->post('salvaNota')=="0") {
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
			redirect(base_url($this->profile->uniqueid.'/acoes/notas/edit/'.$nota_id), 'refresh');
		} elseif ($this->input->post('editaOrdem')=="0"){
			//o formulário de edição de ordem foi enviada, hora de verificar:
			//Primeiro de tudo, vamos verificar se o usuário não está dando migué, tentando editar a ordem de outra pessoa:
			if ($this->input->post('ordem_id')!== "" AND $this->Vw_acoesordens->get_index($this->input->post('ordem_id'),'ordem_id')->profile_id !== $this->profile->id) {
				die ($this->acesso_negado);//Encerra com a msg de erro padrão.
			}
			$data['nota_id'] = $nota_id;
			$data['ativo_nome'] = strtoupper($this->input->post('ativo_nome'));
			$data['ativo_quantidade'] = $this->input->post('ativo_quantidade');
			$data['ativo_valor'] = $this->input->post('ativo_valor');
			$data['operacao'] = $this->input->post('operacao');
			$data['tipo_operacao'] = $this->input->post('tipo_operacao');
			if ($this->input->post('ordem_id') == "") { //sem nota_id definida, é uma ordem nova:
				$this->Acoesordens->insert($data);
			}
			else {
				$this->Acoesordens->update($data,$this->input->post('ordem_id'));
			}
			redirect(base_url($this->profile->uniqueid.'/acoes/notas/edit/'.$nota_id), 'refresh');
			die();
		} else {	
			if ($nota_id == 'new') {
				$data['titulo'] = "Nova nota de corretagem";
			} else {
				$nota = $this->Vw_acoesnota->get_index($nota_id,'nota_id');
				$ordens = $this->Vw_acoesordens->get_all('',array('nota_id'=>$nota_id));
				$data['titulo'] = "Editando a nota de corretagem ".$nota->nota_numero." (".$nota->nome.")";
				$data['nota'] = $nota;
				$data['ordens'] = $ordens;
			}
			$data['nota_id'] = $nota_id;
			$data['corretoras'] = $this->Corretora->get_all('',array('profile_id' => $this->profile->id));
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "notas_edit";
			
			$data['modulo'] = "investimentos";
			$data['title'] = "Nota de Corretagem";
			
			$this->load->view($this->_container, $data);
		}
	}
	
	
    public function delete($nota_id) {
		if ($this->Vw_acoesnota->get_index($nota_id,'nota_id')->profile_id !== $this->profile->id) {
			die ($this->acesso_negado);
		}
		else {
			$this->Acoesnota->delete($nota_id);
			redirect(base_url($this->profile->uniqueid.'/acoes/notas/'), 'refresh');
			die();
		}
    }
}

