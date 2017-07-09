<h1><?php echo lang('forgot_password_heading');?></h1>
<p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>

<div <?php ( ! empty($message)) && print('class="alert alert-info"'); ?> id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("auth/auth_ajax/forgot_password", array("id" => "signup_form", "rel" => "async", "autocomplete" => "off"));?>

    <div class="form-group has-error has-feedback">
        <label for="email"><?php echo sprintf(lang('forgot_password_email_label'), $identity_label);?></label> <br />
        <?php echo bs_form_input($email, '', 'class="forgot_form validify_required validify_email"');?>
		<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
		<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    </div>

    <p><?php echo bs_form_submit('submit', lang('forgot_password_submit_btn'), 'class="validify_button"');?></p>

<?php echo form_close();?>

<div class="modal fade text-left" id="show_success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="color:#27ae60;" id="myModalLabel">SUCCESS</h4>
      </div>
      <div class="modal-body">
          Now go check your inbox (and sometimes your spam folder).
      </div>
      <div class="modal-footer">
        <a href="<?php echo site_url('dashboard'); ?>" id="modal-no" type="button" class="btn btn-success">Ok</a>
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
          
      </div>
      <div class="modal-footer">
        <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>