<div class="auth-container">
	<?php echo $messages; ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<strong><?php echo __('Login') ?></strong>
			</h3>
		</div>
		<div class="panel-body">
			<?php echo form_open(); ?>
			<?php echo form_hidden('redirect', $redirect) ?>
				<div class="form-group">
					<?php echo form_label(__('Email'), 'email') ?>
					<?php echo form_input('email', set_value('email'), 'class="form-control"') ?>
				</div>
				<div class="form-group">
					<?php echo form_label(__('Password'), 'password') ?>
					<?php echo form_password('password', set_value('password'), 'class="form-control"') ?>
				</div>

				<div class="form-group">
					<div class="checkbox">
						<?php echo anchor('forgot-password', __('Forgot password?'), 'class="pull-right"') ?>
						<label>
							<input name="remember" type="checkbox" value="Remember Me">
							<?php echo __('Remember Me') ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<?php echo form_submit('action', __('Login'), 'class="btn btn-block btn-default btn-primary"') ?>
				</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
