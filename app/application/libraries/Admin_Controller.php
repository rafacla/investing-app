<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller {
    public $is_admin;
    public $logged_in_name;
	public $logged_in_user_id;
	public $logged_in_email;
	public $acesso_negado;
	
	public $aContas;
	public $profile;
	
    function __construct() {
        parent::__construct();

		$this->acesso_negado = "<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>";
        // Set container variable
        $this->_container = $this->config->item('ci_budget_template_dir_admin') . "layout.php";
        $this->_modules = $this->config->item('modules_locations');

        $this->load->library(array('ion_auth'));
		$this->load->model(array('budget/vw_contas_saldo'));
		$this->load->model(array('budget/profile'));
		
		if (!$this->ion_auth->logged_in()) {
            redirect('/auth', 'refresh');
        }
		
		if ($this->session->has_userdata('profile_uid') && $this->session->profile_uid !== '' && is_string($this->session->profile_uid )) {
			$this->aContas = $this->vw_contas_saldo->get_all('',array('profile_uid'=>$this->session->profile_uid),'','','conta_nome');
		} else {
			redirect('/profiles', 'refresh');
		}
		
        $this->is_admin = $this->ion_auth->is_admin();
        $user = $this->ion_auth->user()->row();
        $this->logged_in_name = $user->first_name;
		$this->logged_in_email = $user->email;
		$this->logged_in_user_id = $user->id;
		
		$this->profile = $this->profile->get_index($this->session->profile_uid,'uniqueid');

        log_message('debug', 'CI Budget : Admin_Controller class loaded');
    }
}