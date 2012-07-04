<?php

class Auth extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//$this->load->spark('easyauth/1.0.0');
		$this->load->library('easyauth');
		$this->load->library('form_validation');
		$this->load->helper( array('url', 'form', 'html') );

		$this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
	}

	function index()
	{
		if ($this->easyauth->logged_in())
		{
			$this->profile();
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

		$email    = $this->input->post('email');
		$password = $this->input->post('password');
		$remember = $this->input->post('remember');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->login($email, $password, $remember))
			{
				redirect('/');
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'email'    => $email,
			'password' => $password,
			'remember' => $remember,
			'messages' => $messages,
		);

		$this->load->view('auth/login', $data);
	}

	function logout()
	{
		$this->easyauth->logout();
		redirect('/');
	}

	function register()
	{
		$this->form_validation->set_rules('email', __('Email'), 'required|valid_email|is_unique['.$this->easyauth->config('table', 'users').'.email]');
		$this->form_validation->set_rules('password', __('Password'), 'required|matches[passconf]');
		$this->form_validation->set_rules('passconf', __('Password Confirmation'), 'required');

		$email    = $this->input->post('email');
		$password = $this->input->post('password');
		$passconf = $this->input->post('passconf');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->register($email, $password))
			{
				redirect('/');
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'email'    => $email,
			'password' => $password,
			'passconf' => $passconf,
			'messages' => $messages,
		);

		$this->load->view('auth/register', $data);
	}

	function forgot()
	{
		$this->form_validation->set_rules('email', __('Email'), 'required|valid_email');

		$email = $this->input->post('email');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->forgot($email))
			{
				redirect('auth/login');
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'email'    => $email,
			'messages' => $messages,
		);

		$this->load->view('auth/forgot', $data);
	}

	function reset($forgot = null)
	{
		if (!$forgot)
		{
   			redirect('/');
		}

		$this->form_validation->set_rules('password', __('Password'), 'required|matches[passconf]');
		$this->form_validation->set_rules('passconf', __('Password Confirmation'), 'required');

		$password = $this->input->post('password');
		$passconf = $this->input->post('passconf');

		if ($this->form_validation->run())
		{
			if ($this->easyauth->reset($forgot, $password))
			{
				redirect('auth/login');
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
			'messages' => $messages,
		);
		$this->load->view('auth/reset', $data);
	}

	function profile()
	{
		if (!$this->easyauth->logged_in())
		{
			$this->login();
			return;
		}

		$this->form_validation->set_rules('email', __('Email'), 'required|valid_email');
		$this->form_validation->set_rules('password', __('New password'), 'matches[passconf]');
		$this->form_validation->set_rules('passconf', __('Password Confirmation'), '');

		$user = $this->easyauth->user();

		$email    = $this->input->post('email');
		$password = trim($this->input->post('password'));
		$passconf = trim($this->input->post('passconf'));

		if ($this->form_validation->run())
		{
			if ($this->easyauth->profile($email, $password))
			{
 				//profile was succesfully saved
 				//maybe redirect here? or link another table?
			}
		}

		$messages = validation_errors() . $this->easyauth->html_messages();

		$data = array(
   			'email'    => $user->email,
   			'password' => $user->password,
   			'messages' => $messages,
		);

		$this->load->view('auth/profile', $data);
	}
}