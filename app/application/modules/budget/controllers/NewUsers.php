<?php

class NewUsers extends MY_Controller {

    function __construct() {
        parent::__construct();
		$this->load->library(array('ion_auth'));
    }

	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	public function _valid_csrf_nonce()
	{
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	
    public function create() {
        if ($this->input->post('email')) {
            $username = $this->input->post('email');
            $password = $this->input->post('password');
            $email = $this->input->post('email');
			$group_id = array( $this->input->post('group_id'));
			
            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'username' => $this->input->post('username'),
                'phone' => $this->input->post('phone'),
            );
			
            $user = $this->ion_auth->register($username, $password, $email, $additional_data,$group_id);
	
			if(!$user)
            {
                $errors = $this->ion_auth->errors();
                echo "<div class=\"alert alert-danger\" role=\"alert\">";
				echo $errors;
				echo "</div>";
            }
            else
            {
                $data['page'] = "themes/default/users_criado.php";
				$this->load->view($this->_container, $data);
            }


        }

		
		$data['page'] = "themes/default/users_create.php";
        
        $this->load->view($this->_container, $data);
    }
	
	public function activate($id, $code=false)
	{
		if ($code !== false)
		{
			$activation = $this->ion_auth->activate($id, $code);
		}
		else if ($this->ion_auth->is_admin())
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			// redirect them to the auth page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("auth", 'refresh');
		}
		else
		{
			// redirect them to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("esqueciasenha", 'refresh');
		}
	}
	
	// forgot password
	public function forgot_password()
	{
		$this->load->helper(array('form', 'url'));
		
		$this->load->library('form_validation');
				
		$this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
		
		if ($this->form_validation->run() == false)
		{
			$this->data['type'] = $this->config->item('identity','ion_auth');
			// setup the input
			$this->data['email'] = array('name' => 'email',
				'id' => 'email',
			);

			$this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
			
			// set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['page'] = "themes/default/login_form.php";
			
			$this->load->view($this->_container, $this->data);
			//$this->_render_page('auth/forgot_password', $this->data);
		}
		else
		{
			$identity_column = $this->config->item('identity','ion_auth');
			$identity = $this->ion_auth->where($identity_column, $this->input->post('email'))->users()->row();

			if(empty($identity)) {

	            		$this->ion_auth->set_error('forgot_password_email_not_found');
		            	
						$this->session->set_flashdata('message', $this->ion_auth->errors());
                		redirect("esqueciasenha", 'refresh');
            		}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				// if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->data['message'] = "E-mail enviado com sucesso!";
				
				$this->session->flashdata('message');
				$this->data['page'] = "themes/default/login_form.php";
				
				$this->load->view($this->_container, $this->data);
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("esqueciasenha", 'refresh');
			}
		}
	}

	// reset password - final step for forgotten password
	public function reset_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			// if the code is valid then display the password reset form
			$this->load->helper(array('form', 'url'));
		
			$this->load->library('form_validation');
				
			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				// display the form

				// set the flash data error message if there is one
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

				$this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
				$this->data['new_password'] = array(
					'name' => 'new',
					'id'   => 'new',
					'type' => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
				$this->data['new_password_confirm'] = array(
					'name'    => 'new_confirm',
					'id'      => 'new_confirm',
					'type'    => 'password',
					'pattern' => '^.{'.$this->data['min_password_length'].'}.*$',
				);
				$this->data['user_id'] = array(
					'name'  => 'user_id',
					'id'    => 'user_id',
					'type'  => 'hidden',
					'value' => $user->id,
				);
				
				$this->data['csrf'] = $this->_get_csrf_nonce();
				$this->data['code'] = $code;

				// render
				$this->data['page'] = "themes/default/users_reset_password.php";
        
				$this->load->view($this->_container, $this->data);
				//$this->_render_page('auth/reset_password', $this->data);
			}
			else
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id'))
				{

					// something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					show_error($this->lang->line('error_csrf'));

				}
				else
				{
					// finally change the password
					$identity = $user->{$this->config->item('identity', 'ion_auth')};

					$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

					if ($change)
					{
						// if the password was successfully changed
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect("auth/login", 'refresh');
					}
					else
					{
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('auth/reset_password/' . $code, 'refresh');
					}
				}
			}
		}
		else
		{
			// if the code is invalid then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("esqueciasenha", 'refresh');
		}
	}
	
}