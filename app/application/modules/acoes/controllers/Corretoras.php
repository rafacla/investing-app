<?php

class Corretoras extends Admin_Controller {
    function __construct() {
        parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('budget/Profile'));
		$this->load->model(array('acoes/Corretora'));
    }

    public function index($profile_uid, $data=array()) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($profile_uid !== $this->Profile->get($this->profile->id)->uniqueid) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {	
			$query = $this->Corretora->get_all('',array('profile_id'=>$this->profile->id));
			$data['corretoras'] = $query;
			$data['nrCorretoras'] = sizeof($query);
			
			$data['page'] = $this->config->item('ci_acoes_template_dir') . "corretoras_list";
			
			$this->load->view($this->_container, $data);
		}
    }
	
	public function add($profile_uid) {
		if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} elseif ($profile_uid !== $this->Profile->get($this->profile->id)->uniqueid) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {	
			$this->form_validation->set_rules('nome','Nome da corretora','trim|required');
			$this->form_validation->set_rules('site','Site da corretora','trim|required|valid_url');
			if ($this->input->post('nome')) {
				$nome = $this->input->post('nome');
				$site = $this->input->post('site');
				$profile_id = $this->profile->id;

				if ($this->form_validation->run())
				{
					$dados['profile_id'] = $profile_id;
					$dados['nome'] = $nome;
					if (substr($site,0,7)=='http://') {
						$dados['site'] = $site;
					} else {
						$dados['site'] = 'http://'.$site;
					}
					$corretora = $this->Corretora->insert($dados);
					redirect(base_url($this->profile->uniqueid.'/corretoras'), 'refresh');
					die();
				}
			}
        }
		
		$data['page'] = $this->config->item('ci_acoes_template_dir') . "corretoras_create";
        $this->load->view($this->_container, $data);
    }

    public function edit2($id) {
        if ($this->input->post('first_name')) {
            $data['first_name'] = $this->input->post('first_name');
            $data['last_name'] = $this->input->post('last_name');
            $data['email'] = $this->input->post('email');
            $data['phone'] = $this->input->post('phone');
            $group_id = $this->input->post('group_id');

            $this->ion_auth->remove_from_group('', $id);
            $this->ion_auth->add_to_group($group_id, $id);

            $this->ion_auth->update($id, $data);

            redirect('/admin/users', 'refresh');
        }

        $this->load->helper('ui');

        $data['groups'] = $this->ion_auth->groups()->result();
        $data['user'] = $this->ion_auth->user($id)->row();
        $data['user_group'] = $this->ion_auth->get_users_groups($id)->row();
        $data['page'] = $this->config->item('ci_budget_template_dir_admin') . "users_edit";
        $this->load->view($this->_container, $data);
    }

    public function delete($profile_uid,$corretora_id) {
		if ($this->input->post('deletaCorretora')) {
			$this->Corretora->delete($corretora_id);
			redirect(base_url($this->profile->uniqueid.'/corretoras'), 'refresh');
		} else {
			$data['deleta_corretora_nome'] = $this->Corretora->get($corretora_id)->nome;
			$data['deleta_corretora_id'] = $corretora_id;
			$this->index($profile_uid,$data);
		}
    }
}

