<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $title; ?></title>
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
        <?php echo $css; ?>
        <!-- / -->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="<?php echo assets_url('js/html5shiv.min.js'); ?>"></script>
            <script src="<?php echo assets_url('js/respond.min.js'); ?>"></script>
        <![endif]-->

    </head>
    <body>
<div class="container">
    <div class="row">
        <div class="col-lg-offset-3 col-lg-6">

  <input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">      
<p style="padding-bottom: 15px;">Hey <?php echo $o['username']; ?>! Thanks for trialling our system - much appreciated!</p>
<p style="padding-bottom: 15px;">OK, if you liked your trial with us, we’d be stoked if you gave us a great written review here, along with your best picture of yourself. Bonus karma points if you can give us a video review! You’ll get our undying gratitude and a xmas card. Ahem
</p>
<p style="padding-bottom: 15px;"><a href="<?php echo $o['baseurl']; ?>review/leave_review">Click HERE to leave your review</a></p>
<p style="padding-bottom: 15px;">As soon as all two (three please?!) are done, we'll go right ahead and unlock the system for your special price from this point!</p>
<p style="padding-bottom: 15px;">Thanks heaps!</p>
<p style="padding-bottom: 15px;">The TubeMasterPro Team</p>
        </div>
    </div> <!-- /row -->
</div> <!-- /container -->
        <!-- / -->

        <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/main.js'); ?>"></script>
        <script src="<?php echo assets_url('js/contactform.js'); ?>"></script>
        <!-- Extra javascript -->
        <?php echo $js; ?>
        <!-- / -->

        <?php if ( ! empty($ga_id)): ?><!-- Google Analytics -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','<?php echo $ga_id; ?>');ga('send','pageview');
        </script>
        <?php endif; ?><!-- / -->
    </body>
</html>


