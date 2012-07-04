<?php
	echo $messages;

	echo heading(__('Login'));

	echo form_open();

	echo form_label(__('Email'), 'email') . br();
	echo form_input('email', $email) . br();

	echo form_label(__('Password'), 'password') . br();
	echo form_password('password', $password) . br();

	echo form_checkbox('remember', '1', $remember);
	echo form_label(__('Remember me'), 'remember') . br();

	echo anchor('auth/forgot', __('Forgot your password?'));

	echo form_submit('action', 'Login');

	echo form_close();
