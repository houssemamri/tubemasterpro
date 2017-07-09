<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Leave Review</title>
        <meta name="description" content="Leave Review">
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

</style>
    </head>
    <body>
<div class="container message_content" style="margin-top:0px;">
    <div class="row">   
        <div class="col-lg-offset-3 col-lg-6">

  <input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">
  <form id="data" method="post" enctype="multipart/form-data">
  <h3>Post Review</h3>      
  <div id="notification"></div>
<p>
    <label for="name">Message: <small>your review needs to be at least 140 characters!</small></label> <br />
    <textarea name="comments" id="comments" rows="5" cols="20" class="form-control" placeholder="Hint text: Please let others know what you liked and how much time it saved you etc!"></textarea>   
</p>
<p class="pull-right counter_text_color" style="color:#d9534f;"><strong><span class="counter_text">0</span> of 600 characters</strong></p>
<br style="clear:both;">
<p>
    <label for="file">Attach picture of you:</label> <br />
   <p><input type="file" name="afile" id="afile" accept=".png, .jpeg, .jpg" style="float:left;"/> <span class="glyphicon glyphicon-remove file_remove" id="clear1"></span> </p> 
   <p style="clear:both;"></p>
</p>
<p>
    <label for="file">Attach Video Testimonial (Optional) (Max 100Mb):</label> <br />
    <input type="hidden" name="new_filename" value="" id="new_filename" class="form-control" /> 
    <input type="hidden" name="original_filename" value="" id="original_filename" class="form-control" /> 
    <div class="attach_video_file_notif"></div>
    <p class="attach_video_file"><input type="file" name="afile1" id="afile1" accept=".png, .jpeg, .jpg,.avi, .mp4"/> </p> 
                <div class="progress progress-sm progress-striped"  style="display:none;">
                <div class="progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                <span class="sr-only">0% Complete</span>

                </div>
              </div>
              <p class="text-center cancel_button" style="display:none;"><button type="button" class="btn btn-warning btn-xs" id="cancel_upload">Cancel Upload</button></p>

   <p style="clear:both;"></p>
</p>
<p style="padding-top:20px;"><button name="send" type="submit" class="btn submit_button" id="submit_review_text">Submit</button></p>
</form>
        </div>
    </div> <!-- /row -->
</div> <!-- /container -->
        <!-- / -->

        <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/main.js'); ?>"></script>
        <script>
        $(document).ready( function() { 
            var control = $("#afile");
            $("#clear1").css('display','none');
            $("#clear1").on("click", function () {
                control.replaceWith( control = control.clone( true ) );
                $("#clear1").css('display','none');
                 check_disable_button();
            });
            $('#afile').on('change', function(e) {
                 var file = $("#afile").val();
                  if(file == ''){
                   $("#clear1").css('display','none');
                    check_disable_button();
                  }else{
                    $("#clear1").css('display','block');
                    check_disable_button();


                  }

            });

            $('.submit_button').addClass('disabled');
            var maxLen = 600;

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
                $('.counter_text').html(Length);
                check_disable_button();
                if(Length > 140 && Length < 600){
                    $('.counter_text_color').css('color', '#428bca');
                    //$('.submit_button').removeClass('disabled');
                   // $('.submit_button').addClass('btn-primary');
                }else{
                   // $('.submit_button').addClass('disabled');
                    //$('.submit_button').removeClass('btn-primary');
                    $('.counter_text_color').css('color', '#d9534f');
                }
            });
       

            function check_disable_button(){
                 var file = $("#afile").val();
                 var Length = $("#comments").val().length;

                   if(Length > 140 && Length < 600 && file != ''){
                    $('.submit_button').removeClass('disabled');
                    $('.submit_button').addClass('btn-primary');
                }else{
                    $('.submit_button').addClass('disabled');
                    $('.submit_button').removeClass('btn-primary');
                }              
            }

        });

        $("#back_to_review").click(function(e){
             var baseurl     = $("#baseurl").val();
             window.location.replace(baseurl + "review/main");
        });
        /* Submit review module */

 
  function change_submit_button(){
    $("#submit_review_text").text("Sending, please wait...");
  }       
		$("form#data").submit(function(e){
      
     
	    var baseurl     = $("#baseurl").val();
	    var formData = new FormData($(this)[0]);
	    var form = $(this);

     $.ajax({
       url: baseurl + "review/send_text",
       type: 'POST',
       data: formData,
       async: false,
        beforeSend: function()
        {
            $("#submit_review_text").text("Sending, please wait...");
            $("#notification").html("<div class=\"alert alert-info\" role=\"alert\">Submitting, please wait...</div>");
        },        
        success: function (response) {
          console.log(response);
        	var status = response.msgStatus;
            var msg = response.message;

          $("#submit_review_text").text("Submit");
			     if(status == "ok") 
            {
                window.parent.workPlease();
                // $(".message_content").html('<p class="msg success"><a class="hide" href="#">hide this</a>' + msg + '</p>');
                // form.find("input, select, textarea, file").val("");
                // var valField = form.find(".select .value");
                // var selectField = valField.siblings("select");
                // var selectedText = selectField.find("option").eq(0).html();
                // valField.html(selectedText);                
                // $("#clear1").css('display','none');
               // window.parent.location.replace(baseurl + 'subscription');
                
            } else if(status == "error") {
                if(response.errorFields.length) {
                    var fields = response.errorFields;
                    
                    var errors = response.errors;
                    var errorList = "<ul>";
                    for (i = 0; i < errors.length; i++) {
                        errorList += "<li>" + errors[i] + "</li>";
                    }
                    errorList += "</ul>";
                    $("#notification").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><p>There were errors in your form:</p>' + errorList + '<p>Please make the necessary changes and re-submit your form</p></div>');

                } else $("#notification").html('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><p>' + msg + '</p></div>');
            }
        },
        cache: false,
        contentType: false,
        processData: false

    });

    return false;

});


/* UPLOAD FILE*/
document.querySelector('#afile1').addEventListener('change', function(e) {
    var baseurl     = $("#baseurl").val();
    var cancel_upload = document.getElementById('cancel_upload');
    var file_name = $("#afile1").val();
    var file = this.files[0];

  if(file_name == ''){
    return;
  }
  var fd = new FormData();
  fd.append("afile1", file);

  var xhr = new XMLHttpRequest();
  xhr.open('POST', baseurl + 'review/upload_file', true);
 
  function detach() {
        // remove listeners after they become irrelevant
    cancel_upload.removeEventListener('click', canceling, false);
  }
 function canceling() {
      reset($('#afile1'))
      detach();
      xhr.abort();
      console.log('Cancel upload');
      disable_progress_disaply();

  }

  xhr.upload.onprogress = function(e) {
    if (e.lengthComputable) {
      var percentComplete = (e.loaded / e.total) * 100;

      $(".progress").css('display' , 'block');
      $(".cancel_button").css('display' , 'block');
      
      $(".progress-bar").attr('aria-valuenow' , percentComplete);
      $(".progress-bar").css('width' , percentComplete +'%');
      $(".progress-bar").text(Math.round(percentComplete) + "%");
      console.log(percentComplete + '% uploaded');
    }
  };


  xhr.onload = function() {
    if (this.status == 200) {
      setTimeout("disable_progress_disaply()", 1500);
      var resp = JSON.parse(this.response);
      console.log('Server got:', resp);

        if(resp.error == 1){
              $(".attach_video_file_notif").html("<div class=\"alert alert-danger\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> " + resp.error_msg +"</div>");
        }else{
              $("#original_filename").val(resp.orig_filename);
              $("#new_filename").val(resp.filename);

              $(".attach_video_file").css('display' , 'none');
              $(".attach_video_file_notif").html("<div class=\"alert alert-success\" role=\"alert\">Successfully attached a video <br> Filename: "+ resp.orig_filename +" [ <a href=\"#\" onclick='remove_uploaded_video(); return false;'>cancel</a> ]</div>");
        }
    };
  };

  xhr.send(fd);

    // and, of course, cancel if "Cancel" is clicked
    cancel_upload.addEventListener('click', canceling, false);

}, false);

function disable_progress_disaply(){
    $(".progress-bar").css('width' ,  '0%');
    $(".progress").css('display' , 'none');
    $(".cancel_button").css('display' , 'none');
}
 function remove_uploaded_video(){
     reset($('#afile1'))
    //$(".attach_video_file_notif").css('display' , 'none');
    $(".attach_video_file_notif").html("");
    $(".attach_video_file").css('display' , 'block');
    $("#original_filename").val('');
    $("#new_filename").val(''); 


 }

window.reset = function (e) {
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}

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


