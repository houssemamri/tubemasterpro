<?php include('includes/header.php');?>
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
<div class="show_uploaded_video">
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
</div>
<p>
    <label for="name">Update Status: </label> 
         <?php  if($o['gc']['upload_status'] == '3') { ?>
     <strong style="color:#428bca;">&nbsp;<span class="glyphicon glyphicon-thumbs-up" ></span> <?php  echo $o['gc']['upload_status_text'];?></strong>
     <?php } else { ?>
            <select name="upload_status" class="form-control upload_status">
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

        <script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
        <script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
        <script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>

        <script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/slimscroll.config.js'); ?>"></script>

        <!-- Extra javascript -->
        <?php echo $js; ?>
        <!-- / -->
        <?php if($o['update_pp_exp_table']) { ?>
        <script>
      //  $('.submit_button').addClass('disabled');
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
      $(".attach_video_file_notif").css('display' , 'block');
      setTimeout("disable_progress_disaply()", 1500);
      var resp = JSON.parse(this.response);
      console.log('Server got:', resp);

        if(resp.error == 1){
              $(".attach_video_file_notif").html("<div class=\"alert alert-danger\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> " + resp.error_msg +"</div>");
               //$('.submit_button').addClass('disabled');

        }else{
               //$('.submit_button').removeClass('disabled');
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
    $(".attach_video_file_notif").css('display' , 'none');
    $(".attach_video_file").css('display' , 'block');
    $("#new_filename").val('');       

 }

window.reset = function (e) {
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
}

$(function() {
    $(".upload_status").change(function() {
        var status_changed = $('option:selected', this).text();
        if(status_changed == "Done"){
           $(".show_uploaded_video").css('display' , 'block');
        }else{
          remove_uploaded_video();
          $(".show_uploaded_video").css('display' , 'none');
        }
    });
});
</script>
<?php
}
?>
<script>
    $(document).ready(function() 
    {
        //show_chat_content('12');

        socket.on( 'user_connected', function( data ) { 
              if(data.is_connected == 1){
                var user_login_bv = $("#usrloginName").val();
                var user_login_id_bv = $("#user_login_id").val();

                console.log("Connected " + data.is_connected + " as " + user_login_bv);  
                socket.emit( 'save_online', {username: user_login_bv, user_id: user_login_id_bv } );
              }
            });

        socket.on("isTyping", function(data) {  
          $(".list_typing_anim_" + data.room_id).html("<img src='"+ baseurl +"assets/images/chat/isTypingRoomList.GIF'>");
        });

        socket.on("remove_typing_room_list", function(data) {  
          $(".list_typing_anim_" + data.room_id).html(""); 
        });
    });  

    /* admin panel*/
    socket.on( 'check_chatlist', function( data ) {
    show_chatlist();
});
//socket.emit( 'check_chatlist', true );
function show_chatlist(){
    $.ajax
        ({
        type: "POST",
        url: baseurl + "livesupport/checkchatlist",
        cache: false,
        beforeSend: function()
        {
        },
        success: function(result)
        {
          var res = result.split("__SPLITRESULT__");

          $("#chatlist_content").html(res[1]);
          if(res[0] > 0){
            $("#conversation_count").text("(" + res[0] + ")");

            //$("#conversation_count_top").html("<i class=\"fa fa-envelope\"></i><span class=\"label label-danger absolute\">" + res[0] + "</span>");
            //$("#conversation_count_top").html("<span class=\"badge \">"+ res[0] +"</span>");
            $("#conversation_count_top").html("<span class=\"fa-stack\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b style='font-size:0.8em'>" + res[0] + "</b></i></span>");
            $("#conversation_count_sm").text("(" + res[0] + ")");
          }else{
            $("#conversation_count").text("");
            $("#conversation_count_sm").text("");
            $("#conversation_count_top").html("");
          }

          var count_aff_chat = parseInt($(".chat_room_notif_17").text());
          var count_group_chat = parseInt($(".chat_room_notif_11").text());

          if(count_aff_chat > 0){
              if(count_aff_chat > 99){
                  count_aff_chat = "99+"
              }
              $("#conversation_count_affiliate").text("("+ count_aff_chat +")");
            }else{
              $("#conversation_count_affiliate").text("");
            }

          if(count_group_chat > 0){
              if(count_group_chat > 99){
                  count_group_chat = "99+"
              }
              $("#conversation_count_group").text("("+ count_group_chat +")");
            }else{
              $("#conversation_count_group").text("");
           }  
         
          
        }        
      });   
}
setTimeout("show_chatlist()",1000);
</script>
        
<?php include('includes/footer.php');?>


