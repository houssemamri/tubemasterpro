<?php if($confirm_table){ ?>
<h1>Confirm Subscription</h1>
<p>Monthly subscription for TubeTargetPro</p>
<?php echo form_open("subscription/send_confirmation");?>

<?php
	$user  = $this->ion_auth->user()->row();

	$unix_pay_day 	= strtotime($user->advanced_pay_date);
	$unix_time_now	= time();
	
	if(($user->advanced_pay_amt != 0 && $user->advanced_pay_date != 0) && ($unix_pay_day > $unix_time_now)){
		$is_advanced_payer = true;

	}
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
<?php if($is_advanced_payer) { ?>
  <p><b>Plan:</b> USD <?php echo $user->advanced_pay_amt;?>/month</p>
  <p><b>Start Billing date:</b> <?php echo $user->advanced_pay_date;?></p>
<?php 
}
else{


if ( $user->has_review == 0 ) : ?>
	<?php if ( $users_remaining < 50 && !$is_client ) : ?>
	    <!-- <p><b>Transaction id:</b> <?php echo $plan_created['id'];?></p> -->
	    <p><b>Plan:</b> USD $47/month</p>

	<?php else: ?>
		<p><b>Plan:</b> USD $47/month</p>
	<?php endif; ?>
<?php else: ?>
	<p><b>Plan:</b> USD $47/month</p>
<?php endif; ?>

<?php 
}
?>
	    <?php
	    if($ppd['discount_per'] != 0 ){
	    ?>
	    	<div class="alert alert-success" role="alert">
		    	<p><b>PROMO CODE!</b></p>
		    	<p><b>Original Plan:</b> USD <?php echo number_format($ppd['original_amt'],2);?>/month</p>
		    	<p><b>Discount:</b> <?php echo ($ppd['discount_per'] * 100) . "%";?></p>
		    	<?php if($cpc['is_onetime'] == 1){ ?>
		    	<p><b>You only pay:</b> USD <?php echo number_format($ppd['discount_amt'],2);?> on your first month</p>
		    	<?php } else { ?>
		    	<p><b>You only pay:</b> USD <?php echo number_format($ppd['discount_amt'],2);?>/month </p>
		    	<?php } ?> 
		    	<p><b>Note:</b> <?php echo $cpc['option_desc'];?></p>
		    </div>
	    <?php	
	    }else{

	    }
	    ?>
	    <input type="hidden" name="pp_id" id="pp_id" value="<?=$ppd['id'];?>">
    <p><button type="submit" name="p" id="p" class="btn btn-danger" value="cancel">Go back and enter promo code</button> &nbsp; <button type="submit" name="p" id="p" class="btn btn-primary" value="confirm">Confirm</button></p>

<?php 
echo form_close();
}

if($cancel_table){
?>
<h3>Your transaction has been cancelled. <a href="<?php echo $o['baseurl']; ?>subscription/">Subscribe again!</a> </h3>
<?php    
}
if($session_error_table){
?>
<h3>Session timeout. <a href="<?php echo $o['baseurl']; ?>subscription/">Subscribe again!</a> </h3>
<?php    
}
?>
