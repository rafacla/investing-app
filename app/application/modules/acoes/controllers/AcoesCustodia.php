<?php

class AcoesCustodia extends Admin_Controller {
    function __construct() {
        parent::__construct();
		
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('acoes/Vw_acoescustodia'));
    }

    public function index($profile_uid) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {	
			$custodia = $this->Vw_acoescustodia->get_all('',array('profile_id'=>$this->profile->id,'custodia >'=>'0'),'','','corretora_nome');
			$custodia_vendida = $this->Vw_acoescustodia->get_all('',array('profile_id'=>$this->profile->id,'custodia <'=>'0'),'','','corretora_nome');
			$data['custodia'] = $custodia;
			$data['custodia_vendida'] = $custodia_vendida;
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "acoes_custodia";
			$this->load->view($this->_container, $data);
		}
    }
}

