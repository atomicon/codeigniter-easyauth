<div class="auth-container">
	<?php echo $messages; ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<strong><?php echo __('Forgot password?') ?></strong>
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
					<div class="row">
						<div class="col-sm-6">
							<?php echo anchor('/', __('Cancel'), 'class="btn btn-default btn-block"'); ?>
						</div>
						<div class="col-sm-6">
							<?php echo form_submit('action', __('Send reset link'), 'class="btn btn-block btn-default btn-primary"') ?>
						</div>
					</div>
				</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
