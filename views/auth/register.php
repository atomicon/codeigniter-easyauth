<div class="col-md-offset-4">
	<div class="col-md-4">
		<?php echo $messages; ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong><?php echo __('Register') ?></strong>
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
						<?php echo form_label(__('Password confirmation'), 'passconf') ?>
						<?php echo form_password('passconf', set_value('passconf'), 'class="form-control"') ?>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<?php echo anchor('/', __('Cancel'), 'class="btn btn-default btn-block"'); ?>
							</div>
							<div class="col-sm-6">
								<?php echo form_submit('action', __('Register'), 'class="btn btn-block btn-default btn-primary"') ?>
							</div>
						</div>
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
