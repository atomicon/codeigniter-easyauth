<?php
	echo $messages;

	echo heading(__('Profile'));

	echo form_open();

	echo form_label(__('Email'), 'email') . br();
	echo form_input('email', $email) . br();

	echo form_label(__('Password'), 'password') . br();
	echo form_password('password', '') . br();

	echo form_label(__('Password again'), 'passconf') . br();
	echo form_password('passconf', '') . br();

	echo form_submit('action', __('Save'));

	echo form_close();
