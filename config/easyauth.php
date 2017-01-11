<?php

$config['easyauth'] = array(
	'session_key'          => 'easyauth_user_id', //session key
	'remember'             => 'easyauth_remember', //cookie name
	'expire'               => 60 * 60 * 24 * 365, //cookie expiration (year)

	'table'                => 'users', //database table

	'encoding'             => 'md5', //encoding (md5/sha1)

	'install'              => true, //auto installs the user table

	'email_from'           => 'me@domain.com', //e-mail from (when sending password reset mails)

	'email_forgot_subject' => 'Please reset your password',
	'email_forgot_message' => "Hello,\n\nYou stated that you lost your password.\nPlease click or copy the following link to reset your password.\n\n{link}",

	'html_message'         => '<div class="alert alert-{type}"><a class="close" data-dismiss="alert" href="#">&times;</a>{message}</div>', //optimized for bootstrap
);
