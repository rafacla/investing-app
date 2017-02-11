<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profiles extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
		$this->load->model(array('budget/profile'));
		$this->load->model(array('budget/categoria'));
		$this->load->model(array('budget/categorias_default'));
		
		$this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
		
		$this->load->library('session');
    }

    public function index() {
		if ($this->ion_auth->logged_in()) {
			$data['page'] = "themes/default/profiles_list.php";
			$user = $this->ion_auth->user()->row();

			if (isset($user)) {
				
				$profiles = $this->profile->get_all('',array('user_id' => intval($user->id)));
				
				$data['profiles'] = $profiles;

				$this->load->view($this->_container, $data);        
			}
			else {
				$this->ion_auth->logout();
				redirect('/auth', 'refresh');
			}
        } else {
			redirect('/auth', 'refresh');
        }
    }
	
	public function abreprofile($profile_uid) {
		$this->session->profile_uid = $profile_uid;
		redirect($profile_uid.'/accounts', 'refresh');
	}
	
	public function create() {
		$user = $this->ion_auth->user()->row();
		$data['user_id'] = $user->id;
		$data['nome'] = $this->input->post('nome');
		$data['created'] =  date("Y-m-d H:i:s");
		$data['modified'] =  date("Y-m-d H:i:s");
		$data['uniqueid'] = uniqid("");
		$this->db->insert('profiles',$data);
		
		$profile_id = $this->db->insert_id();

		$categorias = $this->categorias_default->get_all('',array(),'','','grupo,ordem');
		
		$i=0;
		$k=0;
		$categoriagrupo="";
		$categoriagrupo_id=0;
		if (count($categorias)) {
			foreach ($categorias as $key => $list) {
				if ($list['grupo']!=$categoriagrupo) {
					$datagrupo['nome'] = $list['grupo'];
					$datagrupo['profile_id'] = $profile_id;
					$datagrupo['ordem'] = $k;
					$datagrupo['created'] =  date("Y-m-d H:i:s");
					$datagrupo['modified'] =  date("Y-m-d H:i:s");
					$this->db->insert('categorias',$datagrupo);
					$categoriagrupo_id = $this->db->insert_id();
					$categoriagrupo = $list['grupo'];
					$k++;
				}
				$datacategoria['nome'] = $list['categoria'];
				$datacategoria['catmaster_id'] = $categoriagrupo_id;
				$datacategoria['ordem'] = $i;
				$datacategoria['created'] =  date("Y-m-d H:i:s");
				$datacategoria['modified'] =  date("Y-m-d H:i:s");
				$this->db->insert('categoriasitens',$datacategoria);
				$i++;
			}
		}
		
		header("Location: ". base_url('profiles/abreProfile/' . $data['uniqueid'])) ;
	}

}