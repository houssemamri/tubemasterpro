<br><br>
<?php if($show_user_elapsed){ ?>
<h1 align="center"><span class="label label-danger">SUBSCRIPTION EXPIRED</span></h1>
<br><br><br>
<p align="center"><a href="<?php echo site_url('/subscription'); ?>"><strong>Click HERE to go and pay for your subscription</strong></a></p>
<?php
}
if($show_aff_elapsed){
?>
<h1 align="center"><span class="label label-danger">SUBSCRIPTION EXPIRED</span></h1>
<br>
<p>SORRY!<p>

<p>Your Affiliate status is now VOID due to your Affiliate subscription lapsing with us.</p>

<p>You will no longer be paid commissions for past or future sales. <br>
You may not sign up again to be an Affiliate of TubeTargetPro. <br> <br>
This decision is final.</p>

<p align="center"><a href="<?php echo site_url('/subscription'); ?>"><strong>Click HERE to go and pay for your subscription</strong></a></p>
<?php
}
?>

