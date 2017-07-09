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
        <style>
/* message and content boxes
---------------------------------*/
.msg {margin: 20px 0; padding: 11px 10px 11px 45px; border: 1px solid #dfdfdf; background-repeat: no-repeat; background-position: 13px 10px; box-shadow: inset 0 0 15px rgba(0,0,0,0.04);}
.msg a.hide {float: right; overflow: hidden; width: 18px; height: 18px; margin-left: 10px; background: url("../images/x.png") 50% 50% no-repeat; text-indent: -50em; opacity: 0.2;}
.msg.info {border-color: #c5dce7; background-color: #e7f7ff; background-image: url("../images/msg-info.png"); color: #4f9ec2;}
.msg.success {border-color: #cbe3b4; background-color: #eeffda; background-image: url("../images/msg-success.png"); color: #8ab04f;}
.msg.notice {border-color: #e9dab1; background-color: #fff9d8; background-image: url("../images/msg-notice.png"); color: #caa533;}
.msg.error {border-color: #ebbcb5; background-color: #ffe6dc; background-image: url("../images/msg-error.png"); color: #ef4437;}
.msg ul, .msg p {margin: 1em 0 0;}
.msg ul {list-style: none;}
.msg ul:first-child, .msg p:first-child {margin: 0;}
.msg ul li {margin-left: 0;}
.msg ul li:before {float: left; clear: left; overflow: hidden; width: 8px; height: 20px; margin-right: 5px; content: "-";}
.file_remove { color: #d9534f;}
</style>
    </head>
    <body>
<div class="container message_content" style="margin-top:0px;">
    <div class="row">
        <!-- <div class="col-lg-offset-3 col-lg-6"> -->
<div class="col-lg-6">
       
<h3>Get Supported!</h3>
<p>Get in touch with us   <input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>"></p>
<form id="data" method="post" enctype="multipart/form-data">
<div id="notification"></div>
<?php if($o['user_is_logged_in'] != 1){ ?> 
<p>
    <label for="name">Name:</label> 
    <input type="text" name="name" value="" id="name" autocomplete="off" class="form-control" placeholder="Enter your Full Name" />    
</p>

<p>
    <label for="name">Email:</label> 
    <input type="text" name="email" value="" id="email" autocomplete="off" class="form-control" />    
</p>
<?php  } else {?>
 <input type="hidden" name="name" value="<?php echo $o['name']; ?>" id="name" autocomplete="off" class="form-control" placeholder="Enter your Full Name" />    
 <input type="hidden" name="email" value="<?php echo $o['email']; ?>" id="email" autocomplete="off" class="form-control" /> 
<?php
}
?>
<p>
    <label for="name">Message:</label>
    <textarea name="comments" id="comments" rows="4" cols="20" class="form-control" placeholder="Give us as much detail on your issue <?php if($o['user_is_logged_in'] == 1){ ?>- and attach screenshots below - they always helps us quicker!
<?php  } ?>"></textarea>   
</p>
<p class="pull-right counter_text_color" style="color:#d9534f;"><strong><span class="counter_text">0</span> of 280 characters</strong></p>
<br>
<?php if($o['user_is_logged_in'] == 1){ ?> 
<p>
    <label for="file">Add up to 3 screen shots!:</label> <br />
   <p><input type="file" name="afile" id="afile" accept=".pdf, .xls,.xlxs, .docx, .doc, .png, .jpeg, .jpg" style="float:left;"/> <span class="glyphicon glyphicon-remove file_remove" id="clear1"></span> </p> 
   <p style="clear:both;"></p>
   <p><input type="file" name="afile2" id="afile2" accept=".pdf, .xls,.xlxs, .docx, .doc, .png, .jpeg, .jpg" style="float:left;"/> <span class="glyphicon glyphicon-remove file_remove" id="clear2"></span></p> 
   <p style="clear:both;"></p>
   <p><input type="file" name="afile3" id="afile3" accept=".pdf, .xls,.xlxs, .docx, .doc, .png, .jpeg, .jpg" style="float:left;"/> <span class="glyphicon glyphicon-remove file_remove" id="clear3"></span></p> 
</p>
<p style="clear:both;"></p>
<?php }
?> 
<!-- <p><button name="send" type="submit" class="btn submit_button" id="submit_contact_form">Send message</button></p> -->
<div class="col-sm-12 text-center">
<button name="send" type="submit" class="btn submit_button" id="submit_contact_form">Send message</button><br><br>
</div>
<div style="clear:both"></div>
</form>
        </div>
    </div> <!-- /row -->
</div> <!-- /container -->
        <!-- / -->

        <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/main.js'); ?>"></script>
        <script src="<?php echo assets_url('js/contactform.js'); ?>"></script>
        <script>
        $(document).ready( function() { 
        $("#clear1").css('display','none');
        $("#clear2").css('display','none');
        $("#clear3").css('display','none');
        var control = $("#afile");

        $("#clear1").on("click", function () {
            control.replaceWith( control = control.clone( true ) );
            $("#clear1").css('display','none');
        });
        var control2 = $("#afile2");
        $("#clear2").on("click", function () {
            control2.replaceWith( control2 = control2.clone( true ) );
            $("#clear2").css('display','none');
        });     

        var control3 = $("#afile3");
        $("#clear3").on("click", function () {
            control3.replaceWith( control3 = control3.clone( true ) );
            $("#clear3").css('display','none');
        }); 

        $('#afile').on('change', function(e) {
            var file = $("#afile").val();
              if(file == ''){
               $("#clear1").css('display','none');
              }else{
                $("#clear1").css('display','block');
              }

        });
        $('#afile2').on('change', function(e) {
            var file = $("#afile2").val();
              if(file == ''){
               $("#clear2").css('display','none');
              }else{
                $("#clear2").css('display','block');
              }

        });

        $('#afile3').on('change', function(e) {
            var file = $("#afile3").val();
              if(file == ''){
               $("#clear3").css('display','none');
              }else{
                $("#clear3").css('display','block');
              }

        });

        $('.submit_button').addClass('disabled');
        var maxLen = 280;

        $('#comments').keypress(function(event){
            var Length = $("#comments").val().length;
            var AmountLeft = maxLen - Length;

            if(Length >= maxLen){
                if (event.which != 8) {
                    return false;
                }
            }
         });

        $('#comments').keyup(function(event){
            var Length = $("#comments").val().length;
            var AmountLeft = maxLen - Length;
            $('.counter_text').html(Length);
            check_enable_button();
            /*
            if(Length > 140 && Length < 280){
                $('.counter_text_color').css('color', '#428bca');
                $('.submit_button').removeClass('disabled');
                $('.submit_button').addClass('btn-primary');
            }else{
                $('.submit_button').addClass('disabled');
                $('.submit_button').removeClass('btn-primary');
                 $('.counter_text_color').css('color', '#d9534f');
            }
            */
        });

        });
    
        var is_valid_email  = 0;
        $('#email').bind('keypress', function (event) {
            var regex = new RegExp("^[a-zA-Z0-9_@.-]*$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
               event.preventDefault();
               return false;
            }
        });

        $('#email').on('change', function(e){
        }).keyup(function(e){

             var email = $("#email").val();
            
            if(email.length < 2 ){
               is_valid_email = 0;
            }else if(isValidEmailAddress(email) == false){
                is_valid_email = 0;
            }   
            else{
                is_valid_email = 1;
            }
            check_enable_button();
        });

        function check_enable_button(){
            var Length = $("#comments").val().length;
            <?php if($o['user_is_logged_in'] != 1){ ?> 
                if((Length > 140 && Length < 280) && is_valid_email == 1){
                    $('.counter_text_color').css('color', '#428bca');
                    $('.submit_button').removeClass('disabled');
                    $('.submit_button').addClass('btn-primary');
                }else{
                    $('.submit_button').addClass('disabled');
                    $('.submit_button').removeClass('btn-primary');
                     $('.counter_text_color').css('color', '#d9534f');
                } 
            <?php
            }else{
            ?>
                if((Length > 140 && Length < 280)){
                    $('.counter_text_color').css('color', '#428bca');
                    $('.submit_button').removeClass('disabled');
                    $('.submit_button').addClass('btn-primary');
                }else{
                    $('.submit_button').addClass('disabled');
                    $('.submit_button').removeClass('btn-primary');
                     $('.counter_text_color').css('color', '#d9534f');
                } 
            <?php
            }
            ?>



           
        }
        function isValidEmailAddress(emailAddress) {
            //var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
            var pattern = new RegExp(/^([a-zA-Z0-9_.-]{2,})+@([a-zA-Z0-9_.-]{2,})+\.([a-zA-Z])+([a-zA-Z])+/);
            // alert( pattern.test(emailAddress) );
            return pattern.test(emailAddress);
        };

        </script>
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