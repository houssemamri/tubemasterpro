<h1>Affiliate Sign Up</h1>
<p>Please enter additional information below.</p>

<div <?php ( ! empty($message)) && print('class="alert alert-info"'); ?> id="infoMessage"><?php echo $message;?></div>

<?php echo form_open("affiliate/affiliate_ajax/signup", array("id" => "signup_form", "rel" => "async", "autocomplete" => "off"));?>

    <p>
        <label for="name">Name:</label> <br />
        <?php echo $user->first_name." ".$user->last_name; ?>
    </p>
    
    <p>
        <label for="email">Email:</label> <br />
        <?php echo $user->email;?>
    </p>
  
      <div class="form-group has-error has-feedback">
        <label for="website">Paypal Email:</label><br />
        <?php echo bs_form_input($paypal_email);?>
      <span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
      <span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
      </div>

    	<div class="form-group  has-error has-feedback">
        <?php echo lang('create_user_company_label', 'company');?> <br />
        <?php echo bs_form_input($company);?>
			<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    	</div>
    	
    	<div class="form-group has-feedback">
	        <label for="countries_phone">Country:</label> <br />
	        <select id="countries_phone" name="country" class="form-control validify_required"></select>
    	</div>
		<div class="form-group has-error has-feedback">
			<label for="mobile">Mobile:</label> <br />
			<input id="mobile" type="tel" name="mobile" placeholder=" " class="form-control validify_required validify_mobile">
			<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
		</div>
		
    	<div class="form-group has-feedback">
        <label for="whatsApp">WhatsApp (Optional):</label><br />
        <?php echo bs_form_input($whatsApp);?>
    	</div>
    	
    	<div class="form-group has-error has-feedback">
        <label for="website">Website:</label><br />
        <?php echo bs_form_input($website);?>
			<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    	</div>
    	
    	<div class="form-group has-error has-feedback">
        <label for="twitter">Twitter Handle:</label><br />
        <?php echo bs_form_input($twitter);?>
			<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    	</div>

    	<div class="form-group has-error has-feedback">
        <label for="fb">Facebook Personal Page:</label><br />
        <?php echo bs_form_input($fb);?>
			<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    	</div>

    	<div class="form-group has-error has-feedback">
        <label for="ln">LinkedIn Personal Profile:</label><br />
        <?php echo bs_form_input($ln);?>
			<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
			<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
    	</div>       


    <p><?php echo bs_form_submit('submit', 'Sign Up', 'class="validify_button"');?></p>

<?php echo form_close();?>

<div class="modal fade text-left" id="show_success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">SUCCESS</h4>
      </div>
      <div class="modal-body">
          Thank you! We'll be in touch within a couple of hours.
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
          Can't Signup at the moment. Please try again later. Thanks!
      </div>
      <div class="modal-footer">
        <button id="modal-no" type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>