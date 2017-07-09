<input type="hidden" value="<?=$o['rn']['id'];?>" name="chat_room_id" id="chat_room_id">
<input type="hidden" value="<?=$o['user']['id'];?>" name="user_id" id="user_id">
<input type="hidden" value="<?=$o['user']['first_name'];?>" name="user_name" id="user_name">
<input type="hidden" value="<?=$o['rn']['chat_id'];?>" name="user_chat_id" id="user_chat_id">
<input type="hidden" value="<?php echo site_url(); ?>" name="aj_baseurl" id="aj_baseurl">
<!-- Chat widget -->
<div class="page-heading animated fadeIn"  >
            <div class="box-info" style="padding-right: 0px;padding-top: 0px;">
              <small>Participants: <span class="room_name_participants_<?=$o['rn']['id'];?>"><?=$o['rn']['room_name_users'];?></span></small>
              <div class="progress progress-sm progress-striped"  style="display:none;">
                <div class="progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                <span class="sr-only">0% Complete</span>

                </div>
              </div>
              <p class="text-center cancel_button" style="display:none;"><button type="button" class="btn btn-warning btn-xs" id="cancel_upload">Cancel Upload</button></p>


              <!-- Chat widget inner -->
              <div class="chat-widget" style="background: #fff;">

   <div class="col-md-12" id="msgbox scroll-y">
    <div id="list_scroll" style="position: relative; overflow: hidden; width: auto; <?php if($o['is_admin'] == '1') { echo " height:470px"; } else { echo "height:390px"; } ?>">
        <div id="msgcontainer" class="slimscrollerChat"></div>
    </div>
    </div>  
    <div class="col-md-12" id="scrollnw" style="position:absolute;">
        <div id="scrollnewmsg" style="text-align:center;" ></div>
    </div>
    
              </div><!-- End div .chat-widget -->
            </div>
</div>
<div class="footer rows" style="height:100px;background-color:#e5e5e5;">
          <div class="col-md-12">

    <div class="row msg_content_container">
         <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-comment" aria-hidden="true"></span></span>
               <textarea type="text" class="form-control" name="message_here" id="message_here" placeholder="Message here!" style="height:60px;" tabindex="-2"></textarea>  
              <div class="input-group-btn dropup">
                <button type="button" name="post_msg" id="post_msg" class="btn btn-primary" style="height:60px;">Post Message</button>
                
              </div>
            </div>
  
   	<input type="checkbox" name="autosend" id="autosend" value="1" checked> <span style="color:black">Enter to send</span> &nbsp;&nbsp;&nbsp;
   	<i class="fa fa-paperclip btn-file" style="top: 3px;padding-right:10px;color:black" ><a href="#"><input type="file" name="afile" id="afile" accept=".pdf, .xls,.xlxs, .docx, .doc, .png, .jpeg, .jpg" style="width:10px; height:10px;"/> Add File</a></i> 
    &nbsp;&nbsp;&nbsp;<span id="notif" style="position:absolute;bottom:0em;display:none;left: 15em;"><span id="istyping"></span> <span id="istyping_text" style="color:black;"></span></span>
<!--
    <div class="col-md-12" id="notif" style="position:absolute;bottom:3em;display:none;">
        <div class="bubble messages animated fadeIn">
          <p class="personSay"><span id="istyping"></span> <span id="istyping_text"></span></p> 
        </div>
    </div>  
    -->
    </div> 
          </div><!-- End div .footer .rows -->
</div>
<script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
<script>

var typing = false;  
var timeout = undefined;

function timeoutFunction() {  
  typing = false;
  socket.emit("typing", false);
}




$("#notif").css({'display' : 'none'});
$("#message_here" ).keyup(function(e) {

	 var check_msg = $("#message_here").val();
   if(check_msg.length < 1){
        var check_room_id     = $("#chat_room_id").val();
        var check_user_id   = $("#user_id").val();
       socket.emit( 'remove_user_typing', {user_id: check_user_id, room_id: check_room_id} );
      typing = false;
   }

        $(this)[0].onkeypress = function(e) {
          var key = e.which||e.keyCode;    
          var msg         = $("#message_here").val();
          var room_id     = $("#chat_room_id").val();
          var user_name   = $("#user_name").val();
          var user_id   = $("#user_id").val();

          if (key !== 13) {
            if (typing === false  && $("#message_here").is(":focus")) {
                typing = true;
                socket.emit("isTyping", {user_name: user_name, room_id: room_id, user_id: user_id } );
              } else {
               clearTimeout(timeout);
                timeout = setTimeout(timeoutFunction, 5000);
              }
          }

            if(key == 13) {
              var autosend = $('#autosend').is(':checked');
              if(autosend){
                e.preventDefault(); 
                var room_id      = $("#chat_room_id").val();
                var sender_id      = $("#user_id").val();
                
                if(msg.length > 1)
                {
          		    submit_chat(msg,room_id,sender_id);
          		   $(this).val("");
                  socket.emit( 'remove_user_typing', {user_id: user_id, room_id: room_id} );
                  typing = false;
                // $(this).val().replace("\n", "");
         	      }
              }
            }
        };
});

socket.on("isTyping", function(data) {  
  var room_id     = $("#chat_room_id").val();
  var user_id     = $("#user_id").val();
  var aj_baseurl  = $("#aj_baseurl").val();
  
  if (data.user_name && (data.room_id == room_id) && (data.user_id != user_id)) {
    //check duplicate chat name
      $("#notif").css({'display' : 'block'});
      var dup_name = $("#chat_typing_" + data.user_id);

      if(dup_name.length == 0){
        $("#istyping").append("<small><img src='"+ aj_baseurl +"assets/images/chat/isTypingRoomList.gif'>&nbsp;<span class='useristyping' id='chat_typing_"+ data.user_id +"'><span class='text-muted'>" + data.user_name + "</span></small>");
      }

      $("#istyping_text").html(" <small>is typing...</small>");
      

      timeout = setTimeout(timeoutFunction, 5000);
  } else {
    $("#chat_typing_"+data.user_id+"").remove();

  }
});

socket.on("remove_user_typing", function(data) {  
  var room_id     = $("#chat_room_id").val();
  var user_id     = $("#user_id").val();
  if (data.room_id == room_id) {
    $("#chat_typing_"+data.user_id+"").remove();
    
    var numItems = $('.useristyping').length;
    //console.log("user typing: " + numItems);
    if(numItems < 1){

        $("#istyping_text").html("");
        $("#istyping").html("");
        $("#notif").css({'display' : 'none'});
      //remove is typing on room list
        socket.emit( 'remove_typing_room_list', {room_id: room_id} );
    }

  } 
});

$( "#message_here" ).attr('tabindex',-2).focus(function () { 
    refresh_chat_container();
});

/* ADD USERS */
/*
$("#add_users").click(function(e){
   var room_id      = $("#chat_room_id").val();
   var aj_baseurl   = $("#aj_baseurl").val();
  $.magnificPopup.open({
  items: {
    src: aj_baseurl + 'livesupport/add_room_user/' + room_id
  },
    type: 'iframe'
  })
});
*/
/* Update next space*/
/*
$("#autosend").click(function(e){
  var checkbox = $("#autosend").is(':checked') ? 1 : 0;

    $.ajax
    ({
        type: "POST",
        cache: false,
        url:  baseurl + "/chatroom/update_user_chat_nextspace",
         data: ({"is_nextspace": checkbox}),
        success: function(result)
        {

        }
    }); 
});
*/
/* UPDATE ROOM */
/*
$("#update_room_name").click(function(e){
   var room_id      = $("#chat_room_id").val();
   var aj_baseurl   = $("#aj_baseurl").val();
  $.magnificPopup.open({
  items: {
    src: aj_baseurl + '/chatroom/update_room_name/' + room_id
  },
    type: 'iframe'
  })
});
*/
$("#post_msg").click(function(e) {
   e.preventDefault();  
    var msg      = $("#message_here").val();
    var room_id      = $("#chat_room_id").val();
    var sender_id      = $("#user_id").val();
    if(msg.length > 1)
    { 
      msg.replace("\n", "<br>");
      submit_chat(msg,room_id,sender_id);
      $(':input[name="message_here"]').val(null);
      socket.emit( 'remove_user_typing', {user_id: sender_id, room_id: room_id} );
      typing = false;

    }    
 });
 setTimeout("openchatroom()",300);

 /* UPLOAD FILE*/
document.querySelector('#afile').addEventListener('change', function(e) {

  var room_id       = $("#chat_room_id").val();
  var cancel_upload = document.getElementById('cancel_upload');

  var file = this.files[0];

  var fd = new FormData();
  fd.append("afile", file);
  fd.append("room_id", room_id);

  var xhr = new XMLHttpRequest();
  xhr.open('POST', baseurl + 'livesupport/upload_chatfile', true);
 
  function detach() {
        // remove listeners after they become irrelevant
    cancel_upload.removeEventListener('click', canceling, false);
  }
 function canceling() {
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
      $(".progress-bar").text(percentComplete + "%");
      console.log(percentComplete + '% uploaded');
    }
  };


  xhr.onload = function() {
    if (this.status == 200) {
      setTimeout("disable_progress_disaply()", 1500);
      var resp = JSON.parse(this.response);
      console.log('Server got:', resp);

      //post to wall
      post_uploadedfile(resp.convo_id);
      //var image = document.createElement('img');
      //image.src = resp.dataUrl;
      //document.body.appendChild(image);
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

/* check users who's online */
setInterval(function(){

    var user_login = $("#usrloginName").val();
    socket.emit( 'get_online_users');
  },1000);

socket.on( 'get_online_users', function( data ) {
    //usernotif_1
      
      var keys = Object.keys(data.online_usernames);

      for (var i = 0; i < keys.length; i++) {

          var val = data.online_usernames[keys[i]];
          $("usernotification").html('<span class="glyphicon glyphicon-certificate" style="color:grey;font-size: 0.7em;"></span>');
          $(".usernotif_" + keys[i]).html('<span class="glyphicon glyphicon-certificate" style="color:green;font-size: 0.7em;"></span>');
      }
  });

socket.on('user_is_disconnected', function(data){
  console.log("disconnect: " + data);
  $(".usernotif_" + data).html('<span class="glyphicon glyphicon-certificate" style="color:grey;font-size: 0.7em;"></span>');
});
/*
socket.on('broadcast_remove_thread', function( data ){
  var aj_baseurl  = $("#aj_baseurl").val();
  var room_id      = $("#chat_room_id").val();
  if(room_id == data.room_id){
    $.ajax
    ({
        type: "POST",
        cache: false,
        url:  baseurl + "livesupport/disable_user_chatroom",
        success: function(result)
        {
          $("#holder").html(result);
        }
    });  
  }
});
*/


</script>