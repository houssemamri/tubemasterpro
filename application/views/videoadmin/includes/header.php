<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $o['title']; ?></title>
        <meta name="description" content="<?php echo $description; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Extra metadata -->
        <?php echo $metadata; ?>
        <!-- / -->

        <!-- favicon.ico and apple-touch-icon.png -->

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>">
        <!-- Font Awesome CSS -->
        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>">
        <!-- Custom styles -->
        <link rel="stylesheet" href="<?php echo assets_url('css/main.css'); ?>">
         <link rel="stylesheet" href="<?php echo assets_url('js/font-awesome/css/font-awesome.min.css'); ?>">
         <link rel="stylesheet" href="<?php echo assets_url('css/chat/style.css'); ?>">
         <link rel="stylesheet" href="<?php echo assets_url('css/chat.css'); ?>">
         <link rel="stylesheet" href="<?php echo assets_url('js/MagnificPopup/dist/magnific-popup.css'); ?>">
         <link rel="stylesheet" href="<?php echo assets_url('css/jqueryui/jquery-ui.min.css'); ?>">
        <?php echo $css; ?>
        <!-- / -->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="<?php echo assets_url('js/html5shiv.min.js'); ?>"></script>
            <script src="<?php echo assets_url('js/respond.min.js'); ?>"></script>
        <![endif]-->
<style>
/* message and content boxes
---------------------------------*/
.msg {margin: 20px 0; padding: 11px 10px 11px 45px; border: 1px solid #dfdfdf; background-repeat: no-repeat; background-position: 13px 10px; box-shadow: inset 0 0 15px rgba(0,0,0,0.04);}
.msg a.hide {float: right; overflow: hidden; width: 18px; height: 18px; margin-left: 10px; background: url("<?php echo assets_url('images/x.png'); ?>") 50% 50% no-repeat; text-indent: -50em; opacity: 0.2;}
.msg.info {border-color: #c5dce7; background-color: #e7f7ff; background-image: url("<?php echo assets_url('images/msg-info.png'); ?>"); color: #4f9ec2;}
.msg.success {border-color: #cbe3b4; background-color: #eeffda; background-image: url("<?php echo assets_url('images/msg-success.png'); ?>"); color: #8ab04f;}
.msg.notice {border-color: #e9dab1; background-color: #fff9d8; background-image: url("<?php echo assets_url('images/msg-notice.png'); ?>"); color: #caa533;}
.msg.error {border-color: #ebbcb5; background-color: #ffe6dc; background-image: url("<?php echo assets_url('images/msg-error.png'); ?>"); color: #ef4437;}
.msg ul, .msg p {margin: 1em 0 0;}
.msg ul {list-style: none;}
.msg ul:first-child, .msg p:first-child {margin: 0;}
.msg ul li {margin-left: 0;}
.msg ul li:before {float: left; clear: left; overflow: hidden; width: 8px; height: 20px; margin-right: 5px; content: "-";}
.file_remove { color: #d9534f;}

.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
.show_uploaded_video { display: none;}
</style>
  
    </head>
<body>
    <input type="hidden" name="usrloginName" id="usrloginName" value="<?php $user = $this->ion_auth->user()->row(); echo $user->first_name; ?>">
    <input type="hidden" name="user_login_id" id="user_login_id" value="<?php $user = $this->ion_auth->user()->row(); echo $user->id; ?>">
    <input type="hidden" name="baseurl" id="baseurl" value="<?php echo site_url(); ?>"> 
    <header class="navbar navbar-fixed-top navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?php echo site_url('dashboard'); ?>" class="navbar-brand" style="padding-top:4px;width: 14%;position: absolute;
left: 20px;"><img src="<?php echo assets_url(); ?>images/TLF-logo-2.png" style="width: 130px;"></a>
        </div>

        <nav class="collapse navbar-collapse">
          <ul class="nav navbar-nav" style="padding-left: 100px;">
                <li><a href="#">Welcome back <?php echo $o['user']['first_name']; ?></a>
                </li>
                
              
              </ul>
            <ul class="nav navbar-nav navbar-right">
               
                    <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dashboards <i class="fa fa-chevron-down i-xs"></i></a>
                    <ul class="dropdown-menu animated half flipInX">
                     	<li><a href="<?php echo site_url('emaildemo'); ?>"> Email users</a></li>
                     	<li><a href="<?php echo site_url('emaildemo/support_message'); ?>"> Send support message</a></li>
                     	<li><a href="<?php echo site_url('emaildemo/support_message_group'); ?>"> Send support message - Group</a></li>
                        <li><a href="<?php echo site_url('affiliateadmin'); ?>">Affiliate</a></li>
                        <li><a href="<?php echo site_url('videoadmin'); ?>">Video</a></li>
                        
                        
                    </ul>
                </li>
                 <li><a href="<?php echo site_url('adminsupport'); ?>"> <span id="conversation_count_top"></span>Support</a></li>
                 <li><a href="<?php echo site_url('promocode'); ?>">Promo Codes</a></li>
                 <li><a href="<?php echo site_url('admin'); ?>">Reports</a></li>
                 <!--
                <li><a href="<?php echo site_url('affiliateadmin'); ?>">Affiliate Dashboard</a></li>
                <li><a href="<?php echo site_url('videoadmin'); ?>">Video Dashboard</a></li>
                 <li ><a href="<?php echo site_url('adminsupport'); ?>">Support Dashboard</a> </li>
                 -->
                <li><a href="<?php echo site_url('auth/logout'); ?>">SIGN OUT</a></li>
            </ul>
        </nav>
    </div>
</header>
<!----- BODY HERE ---->
<div class="container message_content" style="margin-top:70px;">
<?php if($o['affiliate_admin_header']) { ?>
    <div class="row" >
        <div class="col-lg-12">
          <ul class="nav nav-tabs">
              <li role="presentation" <?php if($o['page'] == 'pending') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('affiliateadmin/'); ?>"><span class="glyphicon glyphicon-time" ></span> Requests  <span class="badge" style="background-color: #b94a48"><?php echo $o['count_affiliate_request']; ?></span></a></li>
              <li role="presentation" <?php if($o['page'] == 'approved') echo 'class="active"';?>><a href="<?php echo site_url('affiliateadmin/affiliate_member'); ?>"><span class="glyphicon glyphicon-cog" ></span> Affiliate Members <span class="badge" style="background-color: #b94a48"><?php echo $o['count_affiliate_approved']; ?></span></a></li>
               <li role="presentation" <?php if($o['page'] == 'rejected') echo 'class="active"';?>><a href="<?php echo site_url('affiliateadmin/rejected'); ?>"><span class="glyphicon glyphicon-exclamation-sign" ></span> Rejected <span class="badge"><?php echo $o['count_affiliate_rejected']; ?></span></a></li>
               <li role="presentation" <?php if($o['page'] == 'payout') echo 'class="active"';?>><a href="<?php echo site_url('affiliateadmin/payout'); ?>"><span class="fa fa-money" ></span> Payout </span></a></a></li>
               <li role="presentation" <?php if($o['page'] == 'chargeback') echo 'class="active"';?>><a href="<?php echo site_url('affiliateadmin/chargeback'); ?>"><span class="fa fa-reply-all" ></span> Chargeback </span></a></a></li>
            </ul> 
        </div>
<?php
}
if($o['promocode_header']){
?>
    <div class="row" >
        <div class="col-lg-12">
          <ul class="nav nav-tabs">
              <li role="presentation" <?php if($o['page'] == 'available_code') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('promocode/'); ?>"><span class="fa fa-unlock" ></span> Available Code </a></li>
               <li role="presentation" <?php if($o['page'] == 'create_code') echo 'class="active"';?>><a href="<?php echo site_url('promocode/add_code'); ?>"><span class="fa fa-plus-circle" ></span> Create Promo Code </a></li>
            </ul> 
        </div>
<?php
}
if($o['video_admin_header']) {
?>
    <div class="row" >
        <div class="col-lg-12">
          <ul class="nav nav-tabs">
              <li role="presentation" <?php if($o['page'] == 'pending') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('videoadmin/'); ?>"><span class="glyphicon glyphicon-time" ></span> Pending Videos  <span class="badge" style="background-color: #b94a48"><?php echo $o['count_video_uploaded']; ?></span></a></li>
              <li role="presentation" <?php if($o['page'] == 'process') echo 'class="active"';?>><a href="<?php echo site_url('videoadmin/process'); ?>"><span class="glyphicon glyphicon-cog" ></span> Process <span class="badge"><?php echo $o['count_video_process']; ?></span></a></li>
               <li role="presentation" <?php if($o['page'] == 'done') echo 'class="active"';?>><a href="<?php echo site_url('videoadmin/done'); ?>"><span class="glyphicon glyphicon-exclamation-sign" ></span> Completed <span class="badge"><?php echo $o['count_video_done']; ?></span></a></li>
            </ul> 
        </div>

<?php
}
if($o['support_admin_header']){
?>
    <div class="row" >
<?php   
}
if($o['emaildemo_header']){
?>
    <div class="row" >
        <div class="col-lg-12">
          <ul class="nav nav-tabs">
              <li role="presentation" <?php if($o['page'] == 'demo_users') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('emaildemo/email_demo'); ?>"><span class="glyphicon glyphicon-time" ></span> Email Demo users </a></li>
              <li role="presentation" <?php if($o['page'] == 'send_support') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('emaildemo/support_message'); ?>"><span class="glyphicon glyphicon-envelope" ></span> Send Support message</span></a></li>
              <li role="presentation" <?php if($o['page'] == 'send_support_group') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('emaildemo/support_message_group'); ?>"><span class="glyphicon glyphicon-user" ></span> Send Support message - GROUP</a></li>
              
            </ul> 
        </div>
<?php  
}
?>
    </div> <!-- /row -->
    <?php if($o['msg']){ ?>
    <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="alert alert-<?php echo $o['msg_type']; ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p><?php echo $o['msg']; ?></p>
            </div>
        </div>
    </div>
    <?php } ?>
