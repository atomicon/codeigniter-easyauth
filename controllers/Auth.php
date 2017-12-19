<?php

class Auth extends MY_Controller
{
	public $redirect = '/';

	function __construct()
	{
		parent::__construct();
		$this->load->library('easyauth');
		$this->load->library('form_validation');
		$this->load->helper(array('url', 'form', 'html'));

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
		$this->redirect = $this->input->get_post('redirect');
		if (empty($this->redirect))
		{
			$this->redirect = '/';
		}
	}

	function index()
	{
		if ($this->easyauth->logged_in())
		{
			// redirect to a profile page
			redirect('/');
		}
		else
		{
			$this->login();
		}
	}

	function login()
	{
		$this->form_validation->set_rules('email', __('Email'), 'required|valid_email');
		$this->form_validation->set_rules('password', __('Password'), 'required');

		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$remember = $this->input->post('remember');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->login($email, $password, $remember))
			{
				redirect($this->redirect);
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'email' => $email,
			'password' => $password,
			'remember' => $remember,
			'messages' => $messages,
			'redirect' => $this->redirect,
		);

		$this->load->view('auth/login', $data);
	}

	function logout()
	{
		$this->easyauth->logout();
		redirect($this->redirect);
	}

	function register()
	{
		$this->form_validation->set_rules('email', __('Email'), 'required|valid_email|is_unique[' . $this->easyauth->config('table', 'users') . '.email]');
		$this->form_validation->set_rules('password', __('Password'), 'required|matches[passconf]');
		$this->form_validation->set_rules('passconf', __('Password confirmation'), 'required');

		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$passconf = $this->input->post('passconf');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->register($email, $password))
			{
				redirect($this->redirect);
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'email' => $email,
			'password' => $password,
			'passconf' => $passconf,
			'messages' => $messages,
			'redirect' => $this->redirect,
		);

		$this->load->view('auth/register', $data);
	}

	function forgot_password()
	{
		$this->form_validation->set_rules('email', __('Email'), 'required|valid_email');

		$email = $this->input->post('email');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->forgot_password($email))
			{
				redirect('login');
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'email' => $email,
			'messages' => $messages,
			'redirect' => $this->redirect,
		);

		$this->load->view('auth/forgot_password', $data);
	}

	function reset_password($forgot = null)
	{
		if (!$forgot)
		{
			redirect($this->redirect);
		}

		$this->form_validation->set_rules('password', __('Password'), 'required|matches[passconf]');
		$this->form_validation->set_rules('passconf', __('Password confirmation'), 'required');

		$password = $this->input->post('password');
		$passconf = $this->input->post('passconf');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->reset_password($forgot, $password))
			{
				redirect('login');
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'messages' => $messages,
			'redirect' => $this->redirect,
		);

		$this->load->view('auth/reset_password', $data);
	}	

	function impersonate($id)
	{
		$this->easyauth->impersonate($id);
		redirect($this->redirect);
	}

	function unimpersonate()
	{
		$this->easyauth->unimpersonate($id);
		redirect($this->redirect);
	}
}
