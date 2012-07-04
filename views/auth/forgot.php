<?php
	echo $messages;

	echo heading(__('Forgot your password?'));

	echo form_open();

	echo form_label(__('Email'), 'email') . br();
	echo form_input('email', $email) . br();

	echo form_submit('action', __('Send reset link'));

	echo form_close();
