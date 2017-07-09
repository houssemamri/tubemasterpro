<?php if($o['msg']){ ?>
    <div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="alert alert-<?php echo $o['msg_type']; ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <p><?php echo $o['msg']; ?></p>
            </div>
        </div>
    </div>

    <!---SHOW PAYOUT TABLE -->
<?php 
}
if($o['show_live_support_user_table']){
?>
<div class="row" style="padding-top: 15px;" >
        <div class="col-lg-12">
            <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading">Live Chat with our support</div>
                <div id="chat_container"></div>
            </div>
        </div>
    </div>
    <script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
    <script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
    <script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
    <script>
    $(document).ready(function() 
    {
        show_chat_content('<?=$o['cr']['id'];?>');

        socket.on( 'user_connected', function( data ) { 
              if(data.is_connected == 1){
                var user_login_bv = $("#usrloginName").val();
                var user_login_id_bv = $("#user_login_id").val();

                console.log("Connected " + data.is_connected + " as " + user_login_bv);  
                socket.emit( 'save_online', {username: user_login_bv, user_id: user_login_id_bv } );
              }
            });

        socket.on("isTyping", function(data) {  
          $(".list_typing_anim_" + data.room_id).html("<img src='"+ baseurl +"/assets/images/isTypingRoomList.GIF'>");
        });

        socket.on("remove_typing_room_list", function(data) {  
          $(".list_typing_anim_" + data.room_id).html(""); 
        });
    });  
        </script>
<?php
}
?>
