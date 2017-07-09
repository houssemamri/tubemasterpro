<h1>Sign Up</h1>
<p>Please enter your information below.</p>

<div <?php ( ! empty($message)) && print('class="alert alert-info"'); ?> id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("warroom/warroom_ajax/signup", array("id" => "signup_form", "rel" => "async", "autocomplete" => "off"));?>

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


    <p><?php echo bs_form_submit('submit', 'Sign Up', 'class="validify_button"');?> <input type="hidden" name="signuptoken" value="<?php echo $o['signup_token'];?>" id="signuptoken" /></p>

<?php echo form_close();?>

<div class="modal fade text-left" id="show_success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">SUCCESS</h4>
      </div>
      <div class="modal-body">
          Now go check your inbox for your account confirmation email from us.
      </div>
      <div class="modal-footer">
        <a href="<?php echo site_url(); ?>" id="modal-no" type="button" class="btn btn-success">Ok</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="invalid_token" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">ERROR</h4>
      </div>
      <div class="modal-body">
          INVALID TOKEN, Please refresh page and try again
      </div>
      <div class="modal-footer">
        <a href="<?php echo site_url('warroom/signup'); ?>" id="modal-no" type="button" class="btn btn-success">Ok</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="signup_error" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">ERROR</h4>
      </div>
      <div class="modal-body">
          Cannot signup, please try again later....
      </div>
      <div class="modal-footer">
        <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade text-left" id="show_failed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Message</h4>
      </div>
      <div class="modal-body">
          Can't Signup anymore.
      </div>
      <div class="modal-footer">
        <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>