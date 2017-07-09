<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Upload Review Pictures</title>
        <meta name="description" content="Upload Review Pictures">
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
</style>
    </head>
    <body>
<div class="container">
    <div class="row">
        <div class="col-lg-offset-3 col-lg-6">

  <input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">
  <form id="data" method="post" enctype="multipart/form-data">
  <h1>Upload Review Pictures</h1>      
  <div id="notification"></div>
  <p>Valid format: *.jpg, *.jpg, *jpeg</p>
<p><input type="file" name="afile" id="afile" accept=".png, .jpg"/></p> 
<p><button name="send" type="button" class="btn btn-danger" id="back_to_review">Back</button> 
    <button name="send" type="submit" class="btn btn-primary" id="submit_upload_pictures">Submit</button></p>
</form>
        </div>
    </div> <!-- /row -->
</div> <!-- /container -->
        <!-- / -->

        <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/main.js'); ?>"></script>
        <script>
        $("#back_to_review").click(function(e){
             var baseurl     = $("#baseurl").val();
             window.location.replace(baseurl + "review/main");
        });
        /* Creat chatroom module */
		$("form#data").submit(function(){
	    var baseurl     = $("#baseurl").val();
	    var formData = new FormData($(this)[0]);
	    var form = $(this);
	    var url = form.attr("action");

    $.ajax({
        url: baseurl + "review/send_photo",
        type: 'POST',
        data: formData,
        async: false,
        beforeSend: function()
        {
            $("#submit_upload_pictures").text("Sending...");
            $("#notification").html("<div class=\"alert alert-info\" role=\"alert\">Loading, please wait...</div>");
        },        
        success: function (response) {
        	var status = response.msgStatus;
            var msg = response.message;
             $("#submit_upload_pictures").text("Submit");
			if(status == "ok") 
            {
                $("#notification").html('<p class="msg success"><a class="hide" href="#">hide this</a>' + msg + '</p>');
                 window.parent.location.replace(baseurl + 'subscription');
               
                
            } else if(status == "error") {
                if(response.errorFields.length) {
                    var fields = response.errorFields;
                    
                    var errors = response.errors;
                    var errorList = "<ul>";
                    for (i = 0; i < errors.length; i++) {
                        errorList += "<li>" + errors[i] + "</li>";
                    }
                    errorList += "</ul>";
                    $("#notification").html('<div class="msg error"><a class="hide" href="#">hide this</a><p>There were errors in your form:</p>' + errorList + '<p>Please make the necessary changes and re-submit your form</p></div>');

                } else $("#notification").html('<p class="msg error"><a class="hide" href="#">hide this</a>' + msg + '</p>');
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
});
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


