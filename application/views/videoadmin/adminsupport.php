<?php include('includes/header.php');?>

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

 <div class="row"  >
        <div class="col-lg-12" >
            <div class="panel panel-default" style="margin-bottom: 0px;">
            <!-- Default panel contents -->
            <div class="panel-heading">Chat Support System </div>
            <!-- Table -->
<div class="row" style="margin-right: 0px;
margin-left: 0px;
height: 100%;">
              <div class="col-xs-6 col-md-3" style="background-color:#555;padding-left: 0px;padding-right: 0px;">
                <br>
                     <div id="search">

            <form role="form">
                                <div class="input-group">
    <span class="input-group-addon"> <i class="fa fa-search"></i></span>
    <input type="text" class="form-control search" id="search_msg" placeholder="Search Message" >
  </div>
            </form>  
          </div>

                <div id="list_scroll" style="position: relative; overflow: hidden; width: auto; height: 480px;">
                  <div class="sidebar-inner userlist_scroll">
                    <div id="chatlist_content"></div>
                  </div>
                </div>

         
            <div class="btn-group" role="group" style=" width: 100%;" >
  <button type="button" class="btn  btn-success " id="affiliate_button" style="font-size: 10px;width:50%;">Affiliate <span id="conversation_count_affiliate"></span></button>
  <button type="button" class="btn  btn-danger" id="public_button" style="font-size: 10px;width:50%;">Public <span id="conversation_count_group"></span></button>
</div>

   </div>

   <div class="col-xs-12 col-md-9" style="padding-left: 0px;padding-right: 0px;">
        <div id="chat_container"></div>
    </div>
            </div>
            </div>
        </div>
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
<script src="<?php echo assets_url('js/main.js'); ?>"></script>
<script src="<?php echo site_url('nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js'); ?>"></script>
<script src="<?php echo assets_url('js/chatroom.js'); ?>"></script>
<script src="<?php echo assets_url('js/moment.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/MagnificPopup/dist/jquery.magnific-popup.js'); ?>"></script>

<script src="<?php echo assets_url('js/jquery.slimscroll.min.js'); ?>"></script>
<script src="<?php echo assets_url('js/slimscroll.config.js'); ?>"></script>

    <script>
    $(document).ready(function() 
    {
        //show_chat_content('12');
      $("#affiliate_button").click(function(e){
          show_chat_content('17');
      });

      $("#public_button").click(function(e){
          show_chat_content('11');
      });    

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

$("#search_msg" ).keyup(function(e) {

    var check_msg = $("#search_msg").val();
    if(check_msg.length < 1){
      socket.emit( 'check_chatlist', true );
    }

    $(this)[0].onkeypress = function(e) {
          var key = e.which||e.keyCode;    
          var msg             = $("#search_msg").val();
  
          if(msg.length > 2)
          {
              $.ajax
              ({
                 type: "POST",
                 cache: false,
                  url:  baseurl + "livesupport/search_messages",
                data: ({"search_msg": msg}),
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
                    $("#conversation_count_top").html("<span class=\"fa-stack\"><i class=\"fa fa-circle fa-stack-2x danger\"></i><i class=\"fa fa-stack-1x fa-inverse\"><b style='font-size:0.8em'>" + res[0] + "</b></i></span>");
                    $("#conversation_count_sm").text("(" + res[0] + ")");
                  }else{
                    $("#conversation_count").text("");
                    $("#conversation_count_sm").text("");
                    $("#conversation_count_top").html("");
                  }                  
                 }
             });  
          }
    };
});

        </script>

<?php include('includes/footer.php');?>