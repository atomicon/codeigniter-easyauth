<div class="profile">

    <?php echo $messages; ?>

    <div class="page-header">
        <?php echo heading(__('Profile')); ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Email'), 'email');
        echo form_input('email', $email);
    ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Password'), 'password');
        echo form_password('password', '');
    ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Password again'), 'password2');
        echo form_password('password2', '');
    ?>
    </div>

    <div class="form-actions">
    <?php
        echo anchor('/', __('Cancel'), 'class="btn"');
        echo nbs();
        echo form_submit('action', __('Save'), 'class="btn btn-primary"');
    ?>
    </div>

	<?php echo form_close(); ?>

</div>
