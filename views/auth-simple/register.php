<?php
	echo $messages;

	echo heading(__('Register'));

	echo form_open();

	echo form_label(__('Email'), 'email') . br();
	echo form_input('email', $email) . br();

	echo form_label(__('Password'), 'password') . br();
	echo form_password('password', $password) . br();

	echo form_label(__('Password confirmation'), 'passconf') . br();
	echo form_password('passconf', $passconf) . br();

	echo form_submit('action', __('Register'));

	echo form_close();
