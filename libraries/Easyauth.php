<?php

define('EASYAUTH_DEBUG', ENVIRONMENT !== 'production');

if (!function_exists('__'))
{
	/**
	 * __()
	 *
	 * This is a mimic of the i18n __ wordpress function
	 *
	 * @param string $string The string to translate
	 * @return string The translated string
	 */
	function __($string)
	{
		$translated = false;
		if (function_exists('lang'))
		{
			$translated = lang($string);
		}
		return empty($translated) ? $string : $translated;
	}
}

/**
 * Codeigniter-Easyauth
 *
 * @package codeigniter-easyauth
 * @author Yvo van Dillen
 * @copyright Atomicon
 * @access public
 */
class Easyauth
{
	// The codeigniter object
	protected $_ci = null;

	// The user object
	protected $_user = null;

	// The config object
	protected $_config = null;

	// Array of messages
	protected $_messages = array();

	/**
	 * Easyauth::__construct()
	 *
	 * Initializes codeigniter-easyauth
	 *
	 * @return
	 */
	function __construct()
	{
		$this->_ci = &get_instance();
		$this->_ci->load->driver('session');
		$this->_ci->load->helper('cookie');
		$this->_ci->load->config('easyauth');
		$this->_config = config_item('easyauth');

		if ($this->config('install'))
		{
			$this->install();
		}
	}

	/**
	 * Easyauth::install()
	 *
	 * This will install the 'table' as defined by the config (if install in the config is true)
	 * if the 'table' doesn't already exists.
	 * It will also install one user -> `admin@admin.com` with password: `password`
	 *
	 * @return
	 */
	function install()
	{
		$table = $this->config('table');
		if ($this->_ci->db->table_exists($table))
		{
			return;
		}
		$this->_ci->db->simple_query("
			CREATE TABLE IF NOT EXISTS `$table` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `email` varchar(255) NOT NULL,
			  `password` varchar(255) NOT NULL,
			  `role` varchar(255) DEFAULT 'user',
			  `forgot` varchar(255) DEFAULT NULL,
			  `remember` varchar(255) DEFAULT NULL,
			  `last_login` datetime DEFAULT NULL,
			  `created` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
		");

		// Login with: 'admin@admin.com' and 'password'
		$this->_ci->db->simple_query("
			INSERT INTO `$table` (`id`, `email`, `password`, `role`, `forgot`, `remember`, `last_login`, `created`) VALUES
			(1, 'admin@admin.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'admin', NULL, NULL, NULL, NOW());
		");
	}

	/**
	 * Easyauth::user_id()
	 *
	 * This will return the user_id
	 * If the session failes it will try to get it via the cookie
	 *
	 * @return mixed integer (user id) or FALSE on failure
	 */
	function user_id()
	{
		$user_id = $this->_ci->session->userdata($this->config('session_key'));
		if (!$user_id)
		{
			if ($this->config('remember'))
			{
				$remember_value = get_cookie($this->config('remember'));
				if ($remember_value)
				{
					$query = $this->_ci->db->get_where($this->config('table'), array('remember' => $remember_value), 1);
					$user = $query->row_object();
					if ($user)
					{
						$this->set_user_id($user->id);
						$this->set_user($user);
						$this->set_last_login();
					}
				}
			}
		}
		else
		{
			$this->set_user_id($user_id);
		}

		if ($this->is_impersonating())
		{
			$query = $this->_ci->db->get_where($this->config('table'), array('id' => $this->is_impersonating()), 1);
			$user = $query->row_object();
			if ($user)
			{
				$this->set_user($user);
				$user_id = $user->id;
			}
		}

		return $user_id;
	}

	/**
	 * Easyauth::logged_in()
	 *
	 * Same as user_id only this returns a real boolean
	 *
	 * @return boolean TRUE or FALSE
	 */
	function logged_in()
	{
		return $this->user_id() !== false;
	}

	// functions (called by controllers) ====================================================================

	/**
	 * Easyauth::register()
	 *
	 * @param string $email
	 * @param string $password
	 * @return integer Last insert id on success and FALSE on failure
	 */
	function register($email, $password)
	{
		if ($email === false && $password === false)
		{
			return false;
		}
		$query = $this->_ci->db->get_where($this->config('table'), array('email' => $email), 1);
		$user = $query->row_object();
		if (!empty($user))
		{
			$this->add_message(__('User already exists'), 'error');
			return false;
		}

		$time = strftime("%Y-%m-%d %H:%M:%S", time());
		$data = array(
			'email' => $email,
			'password' => $this->encode($password),
			'last_login' => $time,
			'created' => $time,
			);

		if (!$this->_ci->db->insert($this->config('table'), $data))
		{
			$this->add_message(__('There was a problem in the registration system'), 'error');
			return false;
		}

		$user_id = $this->_ci->db->insert_id();

		$data['id'] = $user_id;
		$this->set_user_id($data['id']);
		$this->set_user((object)$data);

		$this->add_message(__('Succesfully created your account'), 'success');

		return $user_id;
	}

	/**
	 * Easyauth::login()
	 *
	 * Tries to login
	 * on error make sure you check the messages
	 *
	 * @param string $email
	 * @param string $password
	 * @param boolean $remember
	 * @return boolean TRUE on success and FALSE on failure
	 */
	function login($email = false, $password = false, $remember = false)
	{
		if ($email === false || $password === false)
		{
			return false;
		}

		//first try email
		$where = array(
			'email' => $email,
			'password' => $this->encode($password),
			);
		$query = $this->_ci->db->get_where($this->config('table'), $where, 1);
		$user = $query->row_object();
		if (!$user)
		{
			$this->add_message(__('Could not login'), 'error');
			return false;
		}

		$this->set_user_id($user->id);
		$this->set_user($user);
		$this->set_last_login();

		if ($remember)
		{
			$this->set_remember_me();
		}

		$this->add_message(__('Succesfully logged in'), 'success');

		return true;
	}

	/**
	 * Easyauth::logout()
	 *
	 * Logs out the user
	 *
	 * @return void
	 */
	function logout()
	{
		$this->set_user_id(null);
		$this->set_user(null);
		if ($this->config('remember'))
		{
			delete_cookie($this->config('remember'));
		}
	}

	/**
	 * Easyauth::forgot()
	 *
	 * If a user forgot a password, in this function an email
	 * will be created and will be send to the user with a reset link
	 *
	 * @param string $email The users email address
	 * @param string $reset_url (default=auth/reset)
	 * @return boolean TRUE on success and FALSE on failure
	 */
	function forgot_password($email, $reset_url = 'reset-password')
	{
		if ($email == false)
		{
			return false;
		}
		//first try email
		$where = array('email' => $email, );
		$query = $this->_ci->db->get_where($this->config('table'), $where, 1);
		$user = $query->row_object();
		if ($user)
		{
			$forgot_value = $this->encode(uniqid($this->config('session_key')));
			if ($this->_ci->db->update($this->config('table'), array('forgot' => $forgot_value), array('id' => $user->id)))
			{
				$this->_ci->load->library('email');

				$from = $this->config('email_from');
				$to = $user->email;
				$subject = $this->config('email_forgot_subject');
				$message = $this->config('email_forgot_message');

				$data = (array )$user;
				$data['link'] = site_url(rtrim($reset_url, '/') . '/' . $forgot_value);

				foreach ($data as $key => $value)
				{
					$message = str_replace('{' . $key . '}', $value, $message);
				}

				if (defined('EASYAUTH_DEBUG') && EASYAUTH_DEBUG === true)
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
					$this->add_message(__('Please check your mailbox a reset URL has been sent'), 'success');
					return true;
				}
				else
				{
					$this->add_message(__('There was a problem in sending the mail'), 'error');
				}
			}
		}
		else
		{
			$this->add_message(__('No user found with that email address'), 'error');
		}
		return false;
	}

	/**
	 * Easyauth::reset()
	 *
	 * @param string $forgot The secret hash received per email
	 * @param string $password The new password for the user
	 * @return boolean TRUE on success and FALSE on failure
	 */
	function reset_password($forgot, $password)
	{
		$query = $this->_ci->db->get_where($this->config('table'), array('forgot' => $forgot), 1);
		$user = $query->row_object();
		if ($user)
		{
			if ($this->_ci->db->update($this->config('table'), array('password' => $this->encode($password), 'forgot' => null), array('id' => $user->id)))
			{
				$this->add_message(__('Password is reset'), 'success');
				return true;
			}
			else
			{
				$this->add_message(__('There was a problem in the resetting progress'), 'error');
			}
		}
		return false;
	}

	/**
	 * Easyauth::profile()
	 *
	 * @param string $email New email address
	 * @param string $password New password
	 * @return boolean TRUE on success and FALSE on failure
	 */
	function profile($email = false, $password = false)
	{
		$user = $this->user();
		if (!$user)
		{
			return false;
		}

		$data = array();
		if (trim($email) != '')
		{
			$data['email'] = $email;
		}

		if (trim($password) != '')
		{
			$data['password'] = $this->encode($password);
		}

		if (!empty($data))
		{
			if (!$this->_ci->db->update($this->config('table'), $data, array('id' => $user->id), 1))
			{
				$this->add_message(__('There was a problem updating your profile'), 'error');
				return false;
			}
			else
			{
				$this->add_message(__('Profile saved'), 'success');
				return true;
			}
		}
		return true;
	}


	// Impersonation ========================================================================================

	/**
	 * Easyauth::impersonate()
	 *
	 * Impersonate an other user
	 *
	 * @return boolean
	 */
	function impersonate($id)
	{
		$user = $this->user();
		if (isset($user->role) && $user->role == 'admin')
		{
			$query = $this->_ci->db->get_where($this->config('table'), array('id' => $id), 1);
			$impersonate_user = $query->row_object();
			if (isset($impersonate_user->id))
			{
				$this->_ci->session->set_userdata($this->config('session_key') . '_impersonate', $impersonate_user->id);
				return true;
			}
		}
		return false;
	}

	/**
	 * Easyauth::unimpersonate()
	 *
	 * Unimpersonate an other user and return to your own account
	 *
	 * @return boolean
	 */
	function unimpersonate()
	{
		return $this->_ci->session->unset_userdata($this->config('session_key') . '_impersonate');
	}

	/**
	 * Easyauth::is_impersonating()
	 *
	 * Returns the user_id of the user you are impersonating
	 *
	 * @return id (int)
	 */
	function is_impersonating()
	{
		return $this->_ci->session->userdata($this->config('session_key') . '_impersonate');
	}

	// messages ========================================================================================

	/**
	 * Easyauth::messages()
	 *
	 * Returns all messages
	 *
	 * @return array
	 */
	function messages()
	{
		return $this->_messages;
	}

	/**
	 * Easyauth::html_messages()
	 *
	 * Returns all messages but formatted as html
	 *
	 * @return string
	 */
	function html_messages()
	{
		$html = '';
		$html_message = $this->config('html_message', '<div class="alert alert-{type}">{message}</div>');
		foreach ($this->messages() as $message)
		{
			$item = $html_message;
			foreach ($message as $key => $value)
			{
				$item = str_replace('{' . $key . '}', $value, $item);
			}
			$html .= $item;
		}
		return $html;
	}

	/**
	 * Easyauth::add_message()
	 *
	 * @param string $message The message
	 * @param string $type (can be 'error', 'info', 'success' or 'warning')
	 * @return void
	 */
	function add_message($message, $type = 'error')
	{
		$type = $type == 'error' ? 'danger error' : $type;
		$this->_messages[] = array(
			'message' => $message,
			'type' => $type,
			);
	}

	// getters & setters ====================================================================================

	/**
	 * Easyauth::set_last_login()
	 *
	 * @return bool TRUE on success and FALSE on failure
	 */
	function set_last_login()
	{
		$user = $this->user();
		if (!$user)
		{
			return false;
		}

		$data = array('last_login' => strftime("%Y-%m-%d %H:%M:%S", time()), );

		return $this->_ci->db->update($this->config('table'), $data, array('id' => $user->id), 1);
	}

	/**
	 * Easyauth::set_remember_me()
	 *
	 * Updates the database and sets a cookie
	 *
	 * @return bool TRUE on success and FALSE on failure
	 */

	function set_remember_me()
	{
		$user = $this->user();
		if (!$user)
		{
			return false;
		}
		if ($this->config('remember'))
		{
			$remember_value = $this->encode(uniqid($this->config('session_key')));
			if ($this->_ci->db->update($this->config('table'), array('remember' => $remember_value), array('id' => $user->id)))
			{
				set_cookie($this->config('remember'), $remember_value, $this->config('cookie_expire', 60 * 60 * 24 * 365));
				return true;
			}
		}
		return false;
	}


	/**
	 * Easyauth::config()
	 *
	 * @param string $name The config item to get
	 * @param mixed $default The default value if the $name is not found
	 * @return mixed
	 */
	function config($name, $default = false)
	{
		return isset($this->_config[$name]) ? $this->_config[$name] : $default;
	}

	/**
	 * Easyauth::set_config()
	 *
	 * @param string $name The config item to set
	 * @param mixed $value The value config the config item
	 * @return void
	 */
	function set_config($name, $value)
	{
		$this->_config[$name] = $value;
	}

	/**
	 * Easyauth::set_user_id()
	 *
	 * Set the current user_id
	 *
	 * @param integer $id The user id
	 * @return void
	 */
	function set_user_id($id)
	{
		if ($id)
		{
			$this->_ci->session->set_userdata($this->config('session_key'), $id);
		}
		else
		{
			$this->_ci->session->unset_userdata($this->config('session_key'));
		}
	}

	/**
	 * Easyauth::user()
	 *
	 * Returns the user object (all row fields)
	 *
	 * @return object or FALSE
	 */
	function user()
	{
		if (!$this->_user)
		{
			$user_id = $this->user_id();
			$query = $this->_ci->db->get_where($this->config('table'), array('id' => $this->user_id()), 1);
			$this->_user = $query->row_object();
		}
		return $this->_user;
	}

	/**
	 * Easyauth::set_user()
	 *
	 * Sets the user object
	 *
	 * @param object $object User object
	 * @return void
	 */
	function set_user($object)
	{
		$this->_user = $object;
	}

	// Encoding ====================================================================================

	/**
	 * Easyauth::encode()
	 *
	 * Encodes a password conform the config settings
	 *
	 * @param object $object User object
	 * @return The encoded string
	 */
	function encode($password)
	{
		switch ($this->config('encoding', 'md5'))
		{
			case 'sha1':
				return sha1($password);
				break;
			case 'md5':
				return md5($password);
				break;
		}
		return $password;
	}
}
