<?php
	echo $messages;

	echo heading(__('Reset password'));

	echo form_open();

	echo form_label('New password:', 'password') . br();
	echo form_password('password', '') . br();

	echo form_label('Password again:', 'passconf') . br();
	echo form_password('passconf', '') . br();

	echo form_submit('action', __('Reset password'));

	echo form_close();
