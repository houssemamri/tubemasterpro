<style>
  .userlist_scroll{
      overflow-x: hidden; 
      overflow-y: scroll; 
      width: auto; 
      height: 480px;
  }
.userlist_scroll::-webkit-scrollbar {
    width: 3px;
    height: 13px;
}

.userlist_scroll::-webkit-scrollbar-button:vertical {
    background-color: red;
    border: 1px dashed blue;
}
</style>
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
            <div class="panel-heading">Chat Support</div>
            <!-- Table -->
            <div class="row" style="margin-right: 0px;
margin-left: 0px;
height: 100%;">
              <div class="col-xs-6 col-md-3" style="background-color:#555;padding-right: 0px;">
                <div id="list_scroll" >
                  <div class="sidebar-inner userlist_scroll">
                    <div id="chatlist_content" ></div>
                  </div>
                </div>
              </div>
             <div class="col-xs-12 col-md-9">
               
               <div id="chat_container"></div>
             </div>
            </div>
            </div>
        </div>
  </div>

<script src="<?php echo assets_url('js/jquery.min.js'); ?>"></script>
<script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
<script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
<script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>
<script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/slimscroll.config.js'); ?>"></script> 
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
            $("#conversation_count_top").html("<span class=\"fa-stack\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b>" + res[0] + "</b></i></span>");
            $("#conversation_count_sm").text("(" + res[0] + ")");
          }else{
            $("#conversation_count").text("");
            $("#conversation_count_sm").text("");
            $("#conversation_count_top").html("");
          }
         // 
        }        
      });   
}
setTimeout("show_chatlist()",1000);

        </script>
<?php
}
?>