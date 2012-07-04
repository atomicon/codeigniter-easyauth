<div class="forgot">

    <?php echo $messages; ?>

    <div class="page-header">
        <?php echo heading(__('Forgot your password?')); ?>
    </div>

	<?php echo form_open(); ?>

    <div class="control-group">
    <?php
        echo form_label(__('Email'), 'email') . br();
        echo form_input('email', $email) . br();
    ?>
    </div>

    <div class="form-actions">
    <?php
        echo anchor('/', __('Cancel'), 'class="btn"');
        echo nbs();
        echo form_submit('action', __('Send reset link'), 'class="btn btn-primary"');
    ?>
    </div>

	<?php echo form_close(); ?>
</div>
