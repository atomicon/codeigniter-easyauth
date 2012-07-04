<div class="login">

    <?php echo $messages; ?>

    <div class="page-header">
        <?php echo heading(__('Login')); ?>
    </div>

    <?php echo form_open(null, 'class="form"'); ?>

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
        echo form_label( form_checkbox('remember', '1', $remember) . __('Remember me'), 'remember', array('class' => 'checkbox')) . br();
    ?>
    </div>

    <div class="form-actions">
    <?php
        echo anchor('auth/forgot', __('Forgot your password?'), 'class="btn"');
        echo nbs();
        echo form_submit('action', __('Login'), 'class="btn btn-primary"');
    ?>
    </div>

    <?php echo form_close(); ?>
</div>