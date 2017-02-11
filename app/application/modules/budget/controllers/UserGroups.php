<?php

class UserGroups extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $group = 'admin';

        if (!$this->ion_auth->in_group($group))
        {
            $this->session->set_flashdata('message', 'You must be an administrator to view the user groups page.');
            redirect('admin/dashboard');
        }
    }

    public function index() {    
        $groups = $this->ion_auth->groups()->result();

        $data['groups'] = $groups;
        $data['page'] = $this->config->item('ci_budget_template_dir_admin') . "groups_list";
        $this->load->view($this->_container, $data);
    }

    public function create() {
        if ($this->input->post('name')) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');

            $group = $this->ion_auth->create_group($name,$description);

            if(!$group)
            {
                $view_errors = $this->ion_auth->messages();
            }
            else
            {
                redirect('/admin/user-groups', 'refresh');
            }
        }
        $data['page'] = $this->config->item('ci_budget_template_dir_admin') . "groups_create";
        $this->load->view($this->_container, $data);
    }

    public function edit($id) {
        if ($this->input->post('name')) {
            $name = $this->input->post('name');
            $description = $this->input->post('description');

            $group_update = $this->ion_auth->update_group($id, $name, $description);

            redirect('/admin/user-groups', 'refresh');
        }

        $group =  $this->ion_auth->group($id)->row();

        $data['group'] = $group;
        $data['page'] = $this->config->item('ci_budget_template_dir_admin') . "groups_edit";
        $this->load->view($this->_container, $data);
    }

    public function delete($id) {
        $group_delete = $this->ion_auth->delete_group($id);

        redirect('/admin/user-groups', 'refresh');
    }
}