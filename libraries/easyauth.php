<?php

define ('EASYAUTH_DEBUG', FALSE);

if (!function_exists('__'))
{
	function __($string)
	{
		$translated = FALSE;
		if (function_exists('lang'))
		{
			$translated = lang($string);
		}
		return $translated ? $translated : $string;
	}
}

class Easyauth
{
    protected $_ci = null;
    protected $_user = null;
    protected $_config = null;
    protected $_messages = array();

    function __construct()
    {
        $this->_ci = &get_instance();
        $this->_config = config_item('easyauth');
    }

    //getters and setters

    function config($name, $default = FALSE)
    {
    	return isset($this->_config[$name]) ? $this->_config[$name] : $default;
    }

    function set_config($name, $value)
    {
    	$this->_config[$name] = $value;
    }

	function user_id()
	{
		$user_id = $this->_ci->session->userdata( $this->config('session_key') );
		if (!$user_id && $this->config('remember'))
		{
			$remember_value = get_cookie( $this->config('remember') );
			if ($remember_value)
			{
				$query = $this->_ci->db->get_where($this->config('table'), array('remember' => $remember_value), 1);
				$user  = $query->row_object();
				if ($user)
				{
					$this->set_user_id($user->id);
					$this->set_user($user);
				}
			}
		}
  		return $this->_ci->session->userdata( $this->config('session_key') );
	}

	function set_user_id($id)
	{
		if ($id)
		{
			$this->_ci->session->set_userdata( $this->config('session_key'), $id );
		}
		else
		{
			$this->_ci->session->unset_userdata( $this->config('session_key') );
		}
	}

	function user()
	{
		if (!$this->_user)
		{
			$query = $this->_ci->db->get_where($this->config('table'), array('id' => $this->user_id()), 1);
			$this->_user = $query->row_object();
		}
		return $this->_user;
	}

	function set_user($object)
	{
		$this->_user = $object;
	}

	function logged_in()
	{
		return $this->user_id() !== FALSE;
	}

	function messages()
	{
		return $this->_messages;
	}

	function html_messages()
	{
		$html = '';
		$html_message = $this->config('html_message', '<div class="alert alert-{type}"><button class="close" data-dismiss="alert">&times;</button>{message}</div>');
		foreach($this->messages() as $message)
		{
			$item = $html_message;
			foreach($message as $key=>$value)
			{
				$item = str_replace('{'.$key.'}', $value, $item);
			}
			$html .= $item;
		}
		return $html;
	}

	function add_message($message, $type = 'error')
	{
		$this->_messages[] = array(
			'message' => $message,
			'type' => $type,
		);
	}

	// functions (called by controllers)

 	function register($email, $password)
 	{
 		if ($email === FALSE && $password === FALSE)
 		{
 			return FALSE;
 		}
		$query = $this->_ci->db->get_where( $this->config('table'), array('email' => $email), 1);
		$user  = $query->row_object();
		if (!empty($user))
		{
			$this->add_message(__('User already exists'), 'error');
			return FALSE;
		}

		$data = array(
   			'email' => $email,
   			'password' => md5($password),
   			'updated'  => strftime("%Y-%m-%d %H:%M:%S", time()),
   			'created'  => strftime("%Y-%m-%d %H:%M:%S", time()),
		);

		if (!$this->_ci->db->insert( $this->config('table'), $data ))
		{
			$this->add_message( __('There was a problem in the registration system') , 'error');
			return FALSE;
		}

		$data['id'] = $this->_ci->db->insert_id();
		$this->set_user_id($data['id']);
		$this->set_user( (object)$data );

		$this->add_message( __('Succesfully created your account') , 'success');

		return TRUE;
 	}

  	function login($email = FALSE, $password = FALSE, $remember = FALSE)
  	{
  		if ($email === FALSE || $password === FALSE)
  		{
  			return FALSE;
  		}

  		//first try email
  		$where = array(
  			'email'    => $email,
  			'password' => md5($password),
		);
		$query = $this->_ci->db->get_where( $this->config('table'), $where, 1);
		$user  = $query->row_object();
		if (!$user)
		{
			$this->add_message( __('Could not login') , 'error');
			return FALSE;
		}

		$this->set_user_id($user->id);
		$this->set_user($user);
		if ($remember && $this->config('remember'))
		{
			$remember_value = md5(uniqid($this->config('session_key')));
			if ($this->_ci->db->update( $this->config('table'), array('remember' => $remember_value) , array('id' => $user->id)))
			{
				set_cookie( $this->config('remember'), $remember_value, $this->config('cookie_expire', 60*60*24*365) );
			}
		}

		$this->add_message( __('Succesfully logged in') , 'success');

		return TRUE;
  	}

  	function logout()
  	{
		$this->set_user_id(NULL);
		$this->set_user(NULL);
		if ($this->config('remember'))
		{
			delete_cookie( $this->config('remember') );
		}
  	}

  	function forgot($email, $reset_url = 'auth/reset')
  	{
  		if ($email == FALSE)
  		{
  			return FALSE;
  		}
  		//first try email
  		$where = array(
  			'email'    => $email,
		);
		$query = $this->_ci->db->get_where( $this->config('table'), $where, 1);
		$user  = $query->row_object();
		if ($user)
		{
			$forgot_value = md5(uniqid($this->config('session_key')));
			if ($this->_ci->db->update( $this->config('table'), array('forgot' => $forgot_value) , array('id' => $user->id)))
			{
				$this->_ci->load->library('email');

				$from    = $this->config('email_from');
				$to      = $user->email;
				$subject = $this->config('email_forgot_subject');
				$message = $this->config('email_forgot_message');

				$data = (array)$user;
				$data['link'] = site_url( rtrim($reset_url,'/').'/'.$forgot_value);

				foreach($data as $key=>$value)
				{
					$message = str_replace('{'.$key.'}', $value, $message);
				}

				if (defined('EASYAUTH_DEBUG') && EASYAUTH_DEBUG === TRUE)
				{
     				echo "<pre>From: $from\nTo: $to\nSubject: $subject\nmessage:\n$message</pre>";
     				exit;
				}

				$this->_ci->email->from($from);
				$this->_ci->email->to($to);
				$this->_ci->email->subject($subject);
				$this->_ci->email->message($message);

				if ($this->_ci->email->send())
				{
					$this->add_message( __('Please check your mailbox a reset URL has been sent') , 'success');
					return TRUE;
				}
				else
				{
					$this->add_message( __('There was a problem in sending the mail') , 'error');
				}
			}
		}
		else
		{
			$this->add_message( __('No user found with that email address') , 'error');
		}
		return FALSE;
  	}

  	function reset($forgot, $password)
  	{
		$query = $this->_ci->db->get_where( $this->config('table'), array('forgot' => $forgot), 1);
		$user  = $query->row_object();
		if ($user)
		{
   			if ($this->_ci->db->update( $this->config('table'), array('password' => md5($password), 'forgot' => NULL), array('id' => $user->id) ))
   			{
   				$this->add_message( __('Password is reset') , 'success');
   				return TRUE;
   			}
   			else
   			{
   				$this->add_message( __('There was a problem in the resetting progress') , 'error');
   			}
		}
		return FALSE;
  	}

  	function profile($email = FALSE, $password = FALSE, $data = array())
  	{
  		$user = $this->user();
  		if (!$user)
  		{
  			return FALSE;
  		}

  		$data = is_array($data) ? $data : array();
  		if (trim($email)!='')
  		{
  			$data['email'] = $email;
  		}
  		if (trim($password) != '')
  		{
  			$data['password'] = md5($password);
  		}
  		if (!empty($data))
  		{
  			$data['updated'] = strftime("%Y-%m-%d %H:%M:%S", time());
  			if (!$this->_ci->db->update($this->config('table'), $data, array('id' => $user->id), 1))
  			{
  				$this->add_message( __('Profile saved') , 'success');
  			}
  			else
  			{
  				$this->add_message( __('There was a problem updating your profile') , 'error');
  			}
		}
		return TRUE;
  	}

}
