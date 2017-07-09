<h1>Sign Up</h1>
<p>Please enter your information below.</p>

<div <?php ( ! empty($message)) && print('class="alert alert-info"'); ?> id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("signup", array("id" => "signup_form", "autocomplete" => "off"));?>

    <p>
        <?php echo lang('create_user_fname_label', 'first_name');?> <br />
        <?php echo bs_form_input($first_name);?>
    </p>

    <p>
        <?php echo lang('create_user_lname_label', 'last_name');?> <br />
        <?php echo bs_form_input($last_name);?>
    </p>
    
    <p>
        <?php echo lang('create_user_email_label', 'email');?> <br />
        <?php echo bs_form_input($email, null,'data-placement="top" data-trigger="manual" title="Email already exists!"');?>
    </p>

    <p>
        <?php echo lang('create_user_password_label', 'password');?> <br />
        <?php echo bs_form_input($password, null,'data-placement="top" data-toggle="tooltip" data-trigger="focus" title="min 8 characters"');?>
    </p>

    <p>
        <?php echo lang('create_user_password_confirm_label', 'password_confirm');?> <br />
        <?php echo bs_form_input($password_confirm);?>
    </p>


    <p><?php echo bs_form_submit('submit', 'Sign Up', 'class="validify_button"');?></p>
    
<?php echo form_close();?>