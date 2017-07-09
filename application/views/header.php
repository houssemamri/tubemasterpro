<?php
$uri_segment = $this->uri->segment(2);

$is_logged_in  = $this->ion_auth->logged_in();
$user = $this->ion_auth->user()->row();
$user_aff_status = $user->aff_status;

if ( $is_logged_in && $this->ion_auth->in_group(3) && !$user->has_review ) {
	$end_date = new DateTime(date('Y/m/d H:i:s',strtotime('+7 days', $user->first_login)));
	//$end_date = new DateTime(date('Y/m/d H:i:s',strtotime('+5 minutes', $user->first_login)));
	?>
	<div class="text-center">
		<div id="getting-started" class="alert alert-danger col-sm-3 col-sm-offset-4" data-date-end="<?php echo $end_date->getTimestamp(); ?>"></div>
	</div>
	<?php
}
?>

<header class="navbar navbar-fixed-top navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?php echo site_url('dashboard'); ?>" class="navbar-brand" style="padding-top:4px;width: 14%;position: absolute;
left: 20px;"><img src="<?php echo assets_url(); ?>images/TMP-LOGO-small.png" style="width: 130px;"></a>
        </div>
        <nav class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
            	<?php if ( $is_logged_in ) : ?>
                      <li <?php echo ($uri_segment == 'support') ? 'class="dropdown active"' : 'dropdown'; ?>>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">SUPPORT <i class="fa fa-chevron-down i-xs"></i></a>
                        <ul class="dropdown-menu animated half flipInX">
                            <!--<li><a href="#" onclick="popup_support('<?php echo site_url('support'); ?>','iframe'); return false;">Message Support</a></li> -->
                            <li><a href="<?php echo site_url('livesupport/'); ?>"><span id="conversation_count_top"></span>Support Chat</a></li>
                            <!-- <li><a href="<?php echo site_url('livesupport/groupchat'); ?>">TubeTargetPro Chat</a></li>-->
                        </ul>
                    </li>
                    
                    <?php if ($this->ion_auth->in_group(3) ): ?>
	                <li ><a href="<?php echo site_url('subscription'); ?>" style="color:#27ae60;font-weight:bold">UNLOCK FULL VERSION</a></li>
	                <li ><a href="#" id="becomeAffiliate" style="color:#27ae60;font-weight:bold">AFFILIATE</a></li>
	                <?php endif; ?>
	                <!-- <li <?php echo ($uri_segment == 'my_account') ? 'class="active"' : ''; ?>><a href="<?php echo site_url('dashboard/my_account'); ?>">MY ACCOUNT</a></li> -->
	                               <?php if($user->is_jvzoo == 1){ ?>
                        <li ><a href="#"  style="color:#27ae60;font-weight:bold">JVZOO AFFILIATE</a></li>
                    <?php
                    }
                     ?>   
	                 <?php if($user->su_is_aff == 0 && !$user->is_jvzoo == 1){ ?>
                    <?php if ( !$this->ion_auth->in_group(3) && $user->is_aff && $user_aff_status != 'rejected' ): ?>
                        <li <?php echo ($uri_segment == 'affiliate') ? 'class="dropdown active"' : 'dropdown'; ?>>
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">AFFILIATE <i class="fa fa-chevron-down i-xs"></i></a>
                                    <ul class="dropdown-menu animated half flipInX">
                                        <li><a href="<?php echo site_url('affiliate/dashboard'); ?>">Dashboard</a></li>
                                        <li><a href="<?php echo site_url('affiliate/updetails'); ?>">Update Details</a></li>
                                        <li><a href="<?php echo site_url('affiliate/uppixels'); ?>">Update Pixels</a></li>
                                        <li><a href="<?php echo site_url('affiliate/payout'); ?>">Payout</a></li>
                                    </ul>
                                </li>
	                <?php elseif ( !$this->ion_auth->in_group(3) && !$user->is_aff && $user_aff_status != 'rejected'): ?>
	                 <?php if($user->is_aff_tos != '2' && !$user->is_jvzoo == 1){ ?>
	                <li <?php echo ($this->uri->segment(1) == 'affiliate') ? 'class="active"' : ''; ?>>
                        <a href="<?php echo site_url('affiliate/signup'); ?>"  style="color:#27ae60;font-weight:bold">AFFILIATE</a></li>
					<?php } ?>		
                    <?php endif; ?>
                    <?php } ?>
                    <?php if ($this->ion_auth->in_group(1) ): ?>
                    <li><a href="<?php echo site_url('affiliateadmin'); ?>"><span id="conversation_count_admin_top"></span>ADMIN</a></li>
                    <?php endif; ?>
	                <li>
                        <a href="<?php echo site_url('auth/logout'); ?>" class="dropdown-toggle" data-toggle="dropdown">ACCOUNT <i class="fa fa-chevron-down i-xs"></i></a>
                        <ul class="dropdown-menu animated half flipInX">
                            <li><a href="<?php echo site_url('dashboard/profile'); ?>">PROFILE</a></li>
                            <li><a href="<?php echo site_url('auth/logout'); ?>">SIGN OUT</a></li>
                            <li><a href="<?php echo site_url('auth/logout'); ?>?forget=true">SIGN OUT & FORGET ME</a></li>
                        </ul>
                    </li>
                <?php elseif ( uri_string() != 'warroom/signup' ): ?>
                    <li><a href="#" onclick="popup_support('<?php echo site_url('support'); ?>','iframe'); return false;">CONTACT US</a></li>
	                <li><a href="<?php echo site_url('warroom/signup'); ?>">SIGN UP</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>