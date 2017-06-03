<?php

class AcoesCotas extends Admin_Controller {
    function __construct() {
        parent::__construct();
		
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('acoes/Vw_acoesordens'));
		$this->load->model(array('acoes/Proc_custodiadiaria'));
		$this->load->model(array('acoes/Acoesresultado'));
		$this->load->model(array('acoes/Acoeshistorico'));
		$this->load->model(array('acoes/Cdi'));
	}

    public function index($profile_uid) {
		set_time_limit(0);
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {	
			$custodia = $this->Proc_custodiadiaria->get_all_byprofile($this->profile->id);
			$resultados = $this->Acoesresultado->get_all_byprofile($this->profile->id);
			$data['compras'] = $this->Vw_acoesordens->compras_byProfile($this->profile->id);
			$data['vendas'] = $this->Vw_acoesordens->vendas_byProfile($this->profile->id);
			$data['res_datas'] = array_column($resultados,'data');
			$data['res_valores'] = array_column($resultados,'ResLiqAntIR');
			$data['custodia'] = $custodia;
			$data_i = min(array_column($custodia,'data'));
			$data['ibovespa'] = $this->Acoeshistorico->get_all('data,fechamento',array('data>='=>$data_i,'ativo'=>'IBOVESPA'),'','','data');
			$data['cdi'] = $this->Cdi->get_all('data,taxa_diaria',array('data>='=>$data_i),'','','data');
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "acoes_cotas";
			$this->load->view($this->_container, $data);
		}
    }
}

