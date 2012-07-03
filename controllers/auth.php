<?php

/**
 * Auth Controller
 *
 * This is an example controller.
 * You can strip/add anything to it.
 * Copy this file to your controllers folder
 *
 * @package codeigniter-easyauth
 * @author Yvo van Dillen
 * @version $Id$
 * @access public
 */
class Auth extends CI_Controller
{
	/**
	 * Auth::__construct()
	 *
	 * @return
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->spark('easyauth/1.0.0');
		$this->load->helper( array('url', 'form', 'html') );
	}

	/**
	 * Auth::index()
	 *
	 * If the user is logged in it will relay to the profile function
	 * else it will relay to the login screen
	 *
	 * @return
	 */
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

	/**
	 * Auth::login()
	 *
	 * The login screen, on success it will redirect to /
	 *
	 * @return
	 */
	function login()
	{
		$email    = $this->input->post('email');
		$password = $this->input->post('password');
		$remember = $this->input->post('remember');

		if ($this->easyauth->login($email, $password, $remember))
		{
			redirect('/');
		}

		$data = array(
			'email'    => $email,
			'password' => $password,
			'remember' => $remember,
			'messages' => $this->easyauth->html_messages(),
		);

		$this->load->view('auth/login', $data);
	}

	/**
	 * Auth::logout()
	 *
	 * This will log you out and redirect to /
	 *
	 * @return
	 */
	function logout()
	{
		$this->easyauth->logout();
		redirect('/');
	}

	/**
	 * Auth::register()
	 *
	 * This will register a user
	 *
	 * @return
	 */
	function register()
	{
		$email     = $this->input->post('email');
		$password  = $this->input->post('password');
		$password2 = $this->input->post('password2');

		if ($email && $password && $password2 )
		{
			if ($password === $password2)
			{
				if ($this->easyauth->register($email, $password))
				{
					redirect('/');
				}
			}
			else
			{
				$this->easyauth->add_message(__('The passwords don\'t match'), 'error');
			}
		}

		$data = array(
			'email'     => $email,
			'password'  => $password,
			'password2' => $password2,
			'messages' => $this->easyauth->html_messages(),
		);

		$this->load->view('auth/register', $data);
	}

	/**
	 * Auth::forgot()
	 *
	 * If a user forgot the password here it can enter
	 * the email address and a reset link will be mailed
	 *
	 * @return
	 */
	function forgot()
	{
		$email    = $this->input->post('email');

		if ($this->easyauth->forgot($email))
		{
			redirect('auth/login');
		}

		$data = array(
			'email'    => $email,
			'messages' => $this->easyauth->html_messages(),
		);

		$this->load->view('auth/forgot', $data);
	}

	/**
	 * Auth::reset()
	 *
	 * This is the link that will reset the users password
	 *
	 * @param string $forgot This is the hash that will be provided in the mail
	 * @return
	 */
	function reset($forgot = null)
	{
		if (!$forgot)
		{
   			redirect('/');
		}
		$password = $this->input->post('password');
		$password2 = $this->input->post('password2');
		if ($password)
		{
			if ($password === $password2)
			{
				if ($this->easyauth->reset($forgot, $password))
				{
					redirect('auth/login');
				}
			}
			else
			{
				$this->easyauth->add_message(__('The passwords don\'t match'), 'error');
			}
		}

		$data = array(
			'messages' => $this->easyauth->html_messages(),
		);
		$this->load->view('auth/reset', $data);
	}

	/**
	 * Auth::profile()
	 *
	 * Here the user can change a few settings
	 *
	 * @return
	 */
	function profile()
	{
		if (!$this->easyauth->logged_in())
		{
			$this->login();
			return;
		}

		$user = $this->easyauth->user();

		$email = $this->input->post('email');
		if ($email)
		{
			$password  = trim($this->input->post('password'));
			$password2 = trim($this->input->post('password2'));

			if ($password == $password2)
			{
				if ($this->easyauth->profile($email, $password))
				{
     				redirect('auth/profile');
				}
			}
			else
			{
				$this->easyauth->add_message(__('The passwords don\'t match'), 'error');
			}
		}

		$data = array(
   			'email'    => $user->email,
   			'password' => $user->password,
   			'messages' => $this->easyauth->html_messages(),
		);

		$this->load->view('auth/profile', $data);
	}

}