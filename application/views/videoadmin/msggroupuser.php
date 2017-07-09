<?php include('includes/header.php');?>
<style>

thead, tbody, tr, td, th { display: block; }

tr:after {
    content: ' ';
    display: block;
    visibility: hidden;
    clear: both;
}

thead th {
    height: 30px;

    /*text-align: left;*/
}

tbody {
    height: 250px;
    overflow-y: auto;
}

thead {
    /* fallback */
}


  tbody td, thead th {
      width: 30%;
      float: left;
  } 

</style>
  <div class="container message_content" style="margin-top:0px;">
    <div class="row">   
        <div class="col-lg-offset-3 col-lg-6">

  <input type="hidden" name="baseurl" id="baseurl" value="<?php echo $o['baseurl']; ?>">
  <form id="data" method="post" enctype="multipart/form-data">
  <h3><?php echo $o['header_text']; ?></h3>   
  <div id="notification"></div>
  <p>
    <div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading">users</div>

      <!-- Table -->
<table class="table table-striped">
    <thead>
    <tr>
        <th><input type="checkbox" onchange="checkAll(this)" name="chk[]" checked /></th>
        <th>Name</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php 
      foreach($o['show_users'] as $su)
      {
    ?>
    <tr>
        <td class="filterable-cell" style="width:25px;"><input type="checkbox" name="checked[]" id="checked[]" value="<?php echo $su['id']; ?>" checked></td>
        <td class="filterable-cell" style="width:200px;"><?php echo $su['first_name'] . " "  .$su['last_name']; ?></td>
        <td class="filterable-cell">&nbsp;</td>
    </tr>
    <?php
      }
    ?>
    </tbody>
      </table>
    </div>
    
  </p>  
<p>
    <label for="name">Message:</label> <br />
    <textarea name="message" id="message" rows="5" cols="20" class="form-control" placeholder=""></textarea>   
</p>
<p style="padding-top:20px;"><button name="send" type="button" class="btn btn-success" id="submit_message_group">Send</button> 
</form>
        </div>
    </div> <!-- /row -->
</div> 
</div> <!-- /container -->
<div id="footer">
    <div class="container">
      <p class="footer-content">
      
    </p>
    </div>
</div>


<script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/jqueryui/jquery-ui.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/main.js'); ?>"></script>
<script src="<?php echo assets_url('js/intl-tel-input/intlTelInput.min.js'); ?>"></script>

<script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
<script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
<script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>

<script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/slimscroll.config.js'); ?>"></script>
<script>

$('#submit_message_group').click(function(e){

  var baseurl     = $("#baseurl").val();
  var message     = $("#message").val();
  var data = { 'checked[]' : [],"message" : message};
    $(":checked").each(function() {
      if($(this).val() != 'on'){
     data['checked[]'].push($(this).val());
    }
  });

  if(message.length < 5){
    $('#notification').html('<div class="alert alert-warning" role="alert"> <strong>Warning!</strong> message atleast 5 characters</div>');
  }else if(data['checked[]'].length == 0)
  {
    $('#notification').html('<div class="alert alert-warning" role="alert"> <strong>Warning!</strong> Please select atleast 1 user</div>');
  }else{
        $.ajax
        ({
        type: "POST",
        url: baseurl + "emaildemo/send_support_user",
        cache: false,
        data: data,
        beforeSend: function()
        {
          $('#submit_email_demo').addClass('disabled');
          $('#submit_email_demo').removeClass('btn-primary');
          $('#notification').html('<div class="alert alert-info" role="alert"> Sending, please wait....</div>');
        },
        success: function(result)
        {
          var res = result.split("__emaildemosplit__");
          $('#submit_email_demo').removeClass('disabled');
          $('#submit_email_demo').addClass('btn-primary'); 
          if(res[1] == 'success'){
            $('#notification').html('<div class="alert alert-success" role="alert"> Message successfully sent to users</div>');
            $("#message").val('');
          } else{
            $('#notification').html('<div class="alert alert-danger" role="alert"> <strong>Error!</strong> Failed to send email, please try again.</div>');
          }     
        
        }        
      });  

  }
});


 function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
            // console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
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
