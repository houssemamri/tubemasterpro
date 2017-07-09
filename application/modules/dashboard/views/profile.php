<h1>Profile</h1>
<div class="col-sm-12">
	<div <?php ( ! empty($message)) && print('class="alert alert-info"'); ?> id="infoMessage"><?php echo $message;?></div>
	<form id="profile_form" method="post" data-action="<?php echo site_url('dashboard/dashboard_ajax/save_profile'); ?>" autocomplete="off" class="form-horizontal">
	    <div class="form-group">
	    	<label class="col-sm-3 control-label" for="profile_pic">
	    		Profile Picture:
	        </label>
		    <div class="js-custom-control col-sm-9">
	            <div class="media">
	                <div class="pull-left" style="width: 50px;">
	                    <img id="image-holder" class="img-thumbnail"
	                        src="<?php echo ($user->profile_pic) ? assets_url('avatar/thumbnail').'/'.$user->profile_pic : assets_url('avatar/nophoto.png'); ?>"
	                        style="width: 50px; height: 50px;"
	                    >
	                </div>
	                <div class="media-body">
	                    <div style="margin-bottom: 5px;"><?php echo $pagelet_upload_control; ?></div>
	                    <div class="progress progress-striped active hide">
	                        <div class="progress-bar progress-bar-primary"></div>
	                    </div>
	                </div>
	            </div>
	        </div>
		</div>

	    <div class="form-group">
	    	<label class="col-sm-3 control-label" for="name">
	    		Name:
	        </label>
	        <div class="col-sm-9" style="padding-top:5px;">
	        	<?php echo $user->first_name." ".$user->last_name; ?>
	    	</div>
	    </div>
	    
	    <div class="form-group">
	    	<label class="col-sm-3 control-label" for="email">
	        	Email:
	        </label>
	        <div class="col-sm-9" style="padding-top:5px;">
	        	<?php echo $user->email;?>
	        </div>
	    </div>

	    <div class="form-group">
	    	<label class="col-sm-3 control-label" for="password">
	            Old Password
	        </label>
	        <div class="col-sm-9 has-error has-feedback">
	        	<input class="form-control validify_required validify_old_password" type="password" name="old_password" id="old_password" data-trigger="manual" data-toggle="popover" data-placement="top" data-content="Old password does not match" />
				<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
				<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
	        </div>
	    </div>

	    <div class="form-group">
	    	<label class="col-sm-3 control-label" for="password">
	            New Password
	        </label>
	        <div class="col-sm-9 has-error has-feedback">
	        	<input class="form-control validify_required validify_password" type="password" name="password" id="new_password" data-trigger="focus" data-toggle="popover" data-placement="top" data-content="Minimum of 8 characters" />
				<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
				<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
	        </div>
	    </div>
	    
	    <div class="form-group">
	        <label class="col-sm-3 control-label" for="confirm_password">
	            Confirm New Password
	        </label>
	        <div class="col-sm-9 has-error has-feedback">
	        	<input class="form-control validify_required validify_password_confirm" type="password" name="confirm_password" id="confirm_password" data-trigger="focus" data-toggle="popover" data-placement="top" data-content="Must match new password" />
				<span style="display:none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
				<span style="display:none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
	        </div>
	    </div>

	    <div class="form-group">
	    	<div class="col-sm-offset-9 col-sm-3 text-right"><button type="submit" class="btn btn-primary validify_button">Save</button></p></div>
	    </div>
	</form>
</div>