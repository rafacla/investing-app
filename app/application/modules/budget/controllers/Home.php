<?php

class Home extends Admin_Controller {

    function __construct() {
        parent::__construct();
		
		$this->load->library('session');
	    $this->load->library(array('ion_auth'));

		if (!$this->ion_auth->logged_in()) {
			redirect('/auth', 'refresh');
		}
    }

    public function index() {
		if ($this->session->has_userdata('profile_id') && is_string($this->session->profile_id)) {
			redirect($this->session->profile_id.'/accounts', 'refresh');
		} else { 
			redirect('/profiles', 'refresh');
		}
    }

}
