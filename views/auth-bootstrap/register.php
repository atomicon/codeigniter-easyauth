<div class="profile">

    <?php echo $messages; ?>

    <div class="page-header">
        <?php echo heading(__('Register')); ?>
    </div>

	<?php echo form_open(); ?>

    <div class="control-group">
    <?php
        echo form_label(__('Email'), 'email');
        echo form_input('email', $email);
    ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Password'), 'password');
        echo form_password('password', $password);
    ?>
    </div>

    <div class="control-group">
    <?php
        echo form_label(__('Password again'), 'passconf');
        echo form_password('passconf', $passconf);
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
