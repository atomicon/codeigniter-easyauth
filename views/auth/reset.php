<?php
	echo $messages;

	echo heading(__('Reset password'));

	echo form_open();

	echo form_label('New password:', 'password') . br();
	echo form_password('password', '') . br();

	echo form_label('Password again:', 'password2') . br();
	echo form_password('password2', '') . br();

	echo form_submit('action', 'Reset password');

	echo form_close();
