<h1>
    My Account
</h1>

<div role="tabpanel">

  <!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="active" role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
		<?php if ( !$this->ion_auth->in_group(3) ): ?>
		<li role="presentation"><a href="#affiliate" aria-controls="messages" role="tab" data-toggle="tab">Affiliate</a></li>
		<?php endif; ?>
	</ul>

  <!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="profile">
			<form id="update_user_form" data-action="" autocomplete="off" class="form-horizontal">
			    <p>
			        <label for="name">Name:</label> <br />
			        <input type="text" name="company" value="" id="company" class="form-control validify_required validify_alpha">
			        <?php echo $user->first_name." ".$user->last_name; ?>
			    </p>
			    
			    <p>
			        <label for="email">Email:</label> <br />
			        <?php echo $user->email;?>
			    </p>
			    
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
					<input id="mobile" type="tel" name="mobile" class="form-control validify_required validify_mobile">
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
			</form>
		</div>
		<div role="tabpanel" class="tab-pane fade" id="affiliate">
			<?php if ( !$this->ion_auth->in_group(3) ): ?>
				<?php if ( $this->ion_auth->is_admin() ) : ?>
					
				<?php else: ?>
					<form id="update_user_form" data-action="" autocomplete="off" class="form-horizontal">
					    <p>
					        <label for="name">Name:</label> <br />
					        <input type="text" name="company" value="" id="company" class="form-control validify_required validify_alpha">
					        <?php echo $user->first_name." ".$user->last_name; ?>
					    </p>
					    
					    <p>
					        <label for="email">Email:</label> <br />
					        <?php echo $user->email;?>
					    </p>
					    
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
							<input id="mobile" type="tel" name="mobile" class="form-control validify_required validify_mobile">
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
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>

</div>
