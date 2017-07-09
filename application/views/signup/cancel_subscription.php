<?php if($cancel_confirm){ ?>
<h1>Cancel Subscription</h1>
<p>Are you sure you want to cancel your subscription?</p>
<?php echo form_open("subscription/cancel_subscription");?>

    <p><b>Transaction id:</b> <?php echo $gp['plan_id'];?></p>

<?php
	$user  = $this->ion_auth->user()->row();
	$users = $this->ion_auth->users(array('2'))->result();
	$has_review = 0;
	$user_limit = 50;
	if ( $users && count($users) > 0 ) {
		foreach ($users as $key => $value) {
			if ( $value->has_review == 1 ) $has_review++;
		}
	}
	$users_remaining = $user_limit - $has_review;
    //- Check if client affiliate
    $sql = "select aff_id from affiliates where user_id_aff = '".$user->id."' LIMIT 1";
    $check_new_user = $this->db->query($sql);
    $is_client = false;
    if( $check_new_user->num_rows() > 0 ){
        $is_client = true;
    }
?>
<?php if ( $user->has_review == 0 ) : ?>
	<?php if ( $users_remaining < 50 && !$is_client ) : ?>
	    <!-- <p><b>Transaction id:</b> <?php echo $plan_created['id'];?></p> -->
	    <p><b>Plan:</b> USD $97/month</p>
	<?php else: ?>
		<p><b>Plan:</b> USD $97/month</p>
	<?php endif; ?>
<?php else: ?>
	<p><b>Plan:</b> USD $97/month</p>
<?php endif; ?>

     <p><b>Status:</b> <?php echo strtoupper($gp['p_status']);?></p>
    <p><button type="button" name="p" id="p" class="btn btn-danger" onclick="window.location.replace('<?php echo $o['baseurl']; ?>dashboard/keyword_search');">Cancel</button> &nbsp;<button type="submit" name="p" id="p" class="btn btn-primary" value="confirm_cancel">Confirm</button></p>

<?php 
echo form_close();
}
if($cancel_complete_table){
?>
<h1>Goodbye!</h1>
<p>Thank you for using our product, hope you come back again</p>

    <p><button type="button" name="p" id="p" class="btn btn-primary" value="confirm" onclick="window.location.replace('<?php echo $o['baseurl']; ?>/dashboard/keyword_search');">Go to Dashboard</button></p>

<?php
}
?>