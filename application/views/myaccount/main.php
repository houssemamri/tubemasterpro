<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>My Account</title>
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
<header class="navbar navbar-fixed-top navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?php echo site_url(); ?>" class="navbar-brand" style="padding-top:4px;width: 14%;position: absolute;
left: 20px;"><img src="<?php echo assets_url(); ?>images/TLF-logo-2.png" style="width: 130px;"></a>
        </div>
        <nav class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
            		<li><a href="#">Welcomeo back <?php echo $o['user']['first_name']; ?></a></li>
	                <li><a href="<?php echo site_url('auth/logout'); ?>">SIGN OUT</a></li>
            </ul>
        </nav>
    </div>
</header>
<!----- BODY HERE ---->
<div class="container message_content" style="margin-top:70px;">
    <div class="row" >
        <div class="col-lg-12">
        	<ul class="nav nav-tabs">
              <li role="presentation" <?php if($o['page'] == 'pending') { echo 'class="active"';} else { echo '';}?> ><a href="<?php echo site_url('videoadmin/'); ?>"><span class="glyphicon glyphicon-time" ></span> Pending  <span class="badge"><?php echo $o['count_video_uploaded']; ?></span></a></li>
              <li role="presentation" <?php if($o['page'] == 'process') echo 'class="active"';?>><a href="<?php echo site_url('videoadmin/process'); ?>"><span class="glyphicon glyphicon-cog" ></span> In Progress <span class="badge"><?php echo $o['count_video_process']; ?></span></a></a></li>
              <li role="presentation" <?php if($o['page'] == 'done') echo 'class="active"';?>><a href="<?php echo site_url('videoadmin/done'); ?>"><span class="glyphicon glyphicon-thumbs-up" ></span> Completed <span class="badge"><?php echo $o['count_video_done']; ?></span></a></a></li>
            </ul>	
        </div>

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
    <!--- PENDING PROCESS TABLE -->

    <?php if($o['show_table_list']) { ?>
 <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"><?php echo $o['title']; ?> </div>
  <!-- Table -->
  <table class="table">
<table class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Filename</th>
            <th>Date Added</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php
                foreach($o['show_pend'] as $sp)
                 {

             ?>
          <tr>
            <th scope="row"><?php echo ucwords($sp['user_name']); ?></th>
            <td><?php echo $sp['orig_filename']; ?></td>
            <td><?php echo $sp['date_uploaded']; ?></td>
            <td><a href="<?php echo site_url('/videoadmin/update_content/' . $sp['id']);?>"><span class="glyphicon glyphicon-edit"></span> View Content</a></td>
          </tr>
        <?php
            }
        ?>
        </tbody>
      </table>
  </table>
</div>
            </div>
        </div>
    </div>
    <?php } ?>

   
    <!--- END PENDING PROCESS TABLE -->
 <!-- UPDATE CONTENT -->
     <?php if($o['update_pp_exp_table']) { ?>
 <div class="row" style="padding-top: 15px;" >
       <div class="col-lg-offset-2 col-lg-8">
              <h4>Check Upload Details ( <span class="label <?php  echo $o['gc']['label_det'];?>"><?php  echo $o['gc']['upload_status_text'];?></span> )</h4>
<p>
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">
</p>
<form id="data" method="post" enctype="multipart/form-data" action="<?php echo site_url('videoadmin/update_content'); echo "/" . $o['gc']['id']; ?>">
<div id="notification"></div>
<p>
    <label for="name">Name:</label> 
    <?php echo ucwords($o['gc']['user_name']);?>
</p>

<p>
    <label for="name">Paypal #: </label> 
     <?php echo $o['gc']['pp_id'];?>
</p>
<p>
    <label for="name">Paypal Status: </label> 
    <strong><?php echo strtoupper($o['gc']['ppstatus']);?></strong>
</p>
<hr noshade>
<p>
    <label for="name">Filename: </label> 
     <?php echo $o['gc']['orig_filename'];?> | <a href="<?php echo site_url('videoadmin/download_file'); echo "/" . $o['gc']['id']; ?>"><span class="glyphicon glyphicon-download"></span> Download file</a>
</p>
<?php  if($o['gc']['upload_status'] == '2') { ?>
<p>
    <label for="name">Upload Processed Video: </label> 
    <input type="hidden" name="new_filename" value="<?php echo $o['gc']['video_path_done'];?>" id="new_filename" class="form-control" /> 
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
<?php } ?>
<p>
    <label for="name">Update Status: </label> 
         <?php  if($o['gc']['upload_status'] == '3') { ?>
     <strong style="color:#428bca;">&nbsp;<span class="glyphicon glyphicon-thumbs-up" ></span> <?php  echo $o['gc']['upload_status_text'];?></strong>
     <?php } else { ?>
            <select name="upload_status" class="form-control">
      <option value="1" <?php  if($o['gc']['upload_status'] == '1') { echo "selected";}?>>Uploaded</option>
      <option value="2" <?php  if($o['gc']['upload_status'] == '2') { echo "selected";}?>>Process</option>
      <option value="3" <?php  if($o['gc']['upload_status'] == '3') { echo "selected";}?>>Done</option>
      <option value="4" <?php  if($o['gc']['upload_status'] == '4') { echo "selected";}?>>Reject</option>
    </select>
    <?php } ?>

</p>
<p>
    <label for="name">Notes <?php  if($o['gc']['upload_status'] != '3') { echo "<small>(Optional)"; } ?></small>: </label> 
     <?php  if($o['gc']['upload_status'] == '3') { ?>
     <?php echo $o['gc']['notes'];?>
     <?php } else { ?>
        <textarea name="notes" id="notes" rows="4" cols="20" class="form-control">  <?php echo $o['gc']['notes'];?></textarea>
    <?php } ?>
</p>
<p>
<?php  if($o['gc']['upload_status'] == '3') { ?>
<button name="p" type="button" class="btn btn-danger" id="update_video_status" onclick="window.location.replace('<?php echo site_url('videoadmin/done');?>');">Back</button>
<?php } else { ?>
<button name="p" value="update_video_status" type="submit" class="btn btn-primary submit_button" id="update_video_status">Update Detail</button>
 <?php } ?>
<input type="hidden" name="video_id" id="video_id" value="<?php echo $o['gc']['id']; ?>">
</p>

</form>
        </div>
        </div>
 </div>
     <?php } ?>
  <!--END UPDATE CONTENT -->          
    
</div> <!-- /container -->
<!----- END BODY HERE ---->
<div id="footer">
    <div class="container">
    	<p class="footer-content">
		  
		</p>
    </div>
</div>


        <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/main.js'); ?>"></script>
        
        <!-- Extra javascript -->
        <?php echo $js; ?>
        <!-- / -->
        <script>
        <?php  
            if($o['gc']['upload_status'] == '2') 
            {
             echo "$('.submit_button').addClass('disabled');";
            } 
        ?>
<?php  
        if($o['gc']['upload_status'] == '2') 
{
?>
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
  xhr.open('POST', baseurl + 'videoadmin/upload_file', true);
 
  function detach() {
        // remove listeners after they become irrelevant
    cancel_upload.removeEventListener('click', canceling, false);
  }
 function canceling() {
      detach();
      xhr.abort();
      console.log('Cancel upload');
      disable_progress_disaply();
        <?php  
            if($o['gc']['upload_status'] == '2') 
            {
             echo "$('.submit_button').addClass('disabled');";
            } 
        ?>
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
                <?php  
                    if($o['gc']['upload_status'] == '2') 
                    {
                     echo "$('.submit_button').addClass('disabled');";
                    } 
                ?>

        }else{
               
                <?php  
                    if($o['gc']['upload_status'] == '2') 
                    {
                      echo "$('.submit_button').removeClass('disabled');";
                    } 
                ?>
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

<?php
    }
?>
function disable_progress_disaply(){
    $(".progress-bar").css('width' ,  '0%');
    $(".progress").css('display' , 'none');
    $(".cancel_button").css('display' , 'none');
}
 function remove_uploaded_video(){
    $(".attach_video_file_notif").css('display' , 'none');
    $(".attach_video_file").css('display' , 'block');
    $("#new_filename").val('');       

 }
        </script>
        
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


