<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    var $_container;
    var $_modules;

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
		
        $this->load->config('ci_budget');
		
        // Set container variable
        $this->_container = $this->config->item('ci_budget_template_dir_public') . "layout.php";
        $this->_modules = $this->config->item('modules_locations');
		
		$this->load->library('session');		
		
		if(!$this->session->userdata('lang')) {
			$this->session->set_tempdata('lang',$this->config->item('language'),10*365*24*60*60);
		}
		$idiom = $this->session->userdata('lang');
		
		$this->lang->load('budgetapp_lang', $idiom);
		
        log_message('debug', 'CI Budget : MY_Controller class loaded');
    }
}