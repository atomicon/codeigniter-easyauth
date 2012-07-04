<div class="profile">

    <?php echo $messages; ?>

    <div class="page-header">
        <?php echo heading(__('Register')); ?>
    </div>

	<?php echo form_open(); ?>

    <div class="control-group">
    <?php
        echo form_label(__('Email'), 'email') . br();
        echo form_input('email', $email) . br();
    ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Password'), 'password') . br();
        echo form_password('password', $password) . br();
    ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Password again'), 'password2') . br();
        echo form_password('password2', $password2) . br();
    ?>
    </div>

    <div class="form-actions">
    <?php
        echo anchor('/', __('Cancel'), 'class="btn"');
        echo nbs();
        echo form_submit('action', __('Register'), 'class="btn btn-primary"');
    ?>
    </div>

	<?php echo form_close(); ?>

</div>
