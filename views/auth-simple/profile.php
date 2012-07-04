<?php
	echo $messages;

	echo heading(__('Profile'));

	echo form_open();

	echo form_label(__('Email'), 'email') . br();
	echo form_input('email', $email) . br();

	echo form_label(__('Password'), 'password') . br();
	echo form_password('password', '') . br();

	echo form_label(__('Password again'), 'password2') . br();
	echo form_password('password2', '') . br();

	echo form_submit('action', __('Save'));

	echo form_close();
