<?php

class AcoesResultados extends Admin_Controller {
    function __construct() {
        parent::__construct();
		
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('acoes/Acoesresultado'));
    }

    public function index($profile_uid) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {	
			$query = $this->Acoesresultado->get_all_byprofile($this->profile->id);
			//var_dump(query);
			$data['cPontos'] = sizeof($query);
			$data['categorias'] = array_column($query,'data');
			$data['resultados'] = array_column($query,'ResLiqAntIR');
			$data['ativos'] = array_column($query,'ativo_nome');
			$data['quantidades'] = array_column($query,'qt_a');
			
			$data['modulo'] = "investimentos";
			$data['title'] = "Gráfico de Performance";
			
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "acoes_resultados";
			
			$this->load->view($this->_container, $data);
		}
    }
}

