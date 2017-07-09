var baseurl 	= $("#baseurl").val();
var get_time  = new Date().getTime();
//var socket    = io.connect( 'http://localhost:8080' );
var socket = io.connect( 'http://203.201.129.3:8888' );

var userList = [];
//var audioElement = document.createElement('audio');
//audioElement.setAttribute('src', baseurl +'/assets/sounds/notif.mp3');


// on connection to server, ask for user's name with an anonymous callback
/*
  socket.on('connect', function(){
    // call the server-side function 'adduser' and send one parameter (value of prompt)
    socket.emit('adduser', prompt("What's your name?"));
  });
*/


/* show online users*/
function show_online_contact(user_login,user_login_id){
    $.ajax
        ({
        type: "POST",
        url: baseurl + "/livesupport/show_user_online",
        cache: false,
        beforeSend: function()
        {
        },
        success: function(result)
        {
          $("#chatlist_content").html(result);
          socket.emit( 'save_online', {username: user_login, user_id: user_login_id } );
        }        
    });    
}


socket.on('update_usersList', function (users){
    $('#user').empty();
    var keys = Object.keys(users);
    if(keys.length > 1){
      var total_length = keys.length -1;
      $('#contact_count').text("(" + total_length +")");
       $('#contact_count_sm').text("(" + total_length +")");
    }
    
    for (var i = 0; i < keys.length; i++) {
      var val = users[keys[i]];
      $('#user').append("<li> <span class='glyphicon glyphicon-certificate' style='color:green;font-size: 0.7em;padding-right:2px'></span>" + val + "</li>"); 
    }

}); 

/* open room id*/

function show_chat_content(room_id){

	$.ajax
		({

		type: "POST",
		url: baseurl + "livesupport/show_chat_content",
		data: ({"room_id" : room_id}),
		cache: false,
		beforeSend: function()
		{
			$("#chat_container").html("<div class=\"alert alert-info\" role=\"alert\">Loading room, please wait...</div>");
		},
		success: function(result)
		{
			$("#chat_container").html(result);

		}	
	});
}

/* OPEN CHATROOM*/
function openchatroom(){

   var room_id  = $("#chat_room_id").val();

    $.ajax
    ({
       type: "POST",
       cache: false,
       url: baseurl + "livesupport/ret_chat",
      data: ({"room_id":room_id}),
      success: function(result)
      {  

        if(result == "not_allow_to_view"){
          $('#msgcontainer').html("<div class=\"alert alert-danger\" role=\"alert\">you are not allowed to view this room</div>"); 
        }else{
          $('#msgcontainer').html(result);

          var newmsg_top = parseInt($('ul#chat-items li:last').offset().top);
          $('#chat-container').scrollTop(newmsg_top);
    	   //setTimeout("openchatroom_refresh()",1500);

            /*check_last_message*/
            //$(".msg_is_read").html('');
            //$(".seen_by").html('');
            var last_convo_id = parseInt($('ul#chat-items li:last').attr('data-id'));
            var user_post_id = parseInt($('ul#chat-items li:last').attr('data-user-id'));
            socket.emit( 'check_last_message', {convo_id: last_convo_id, room_id: room_id, user_post: user_post_id} );
            socket.emit( 'check_chatlist', true );

          }
       }
   });
          


}

function submit_chat(msg,room_id,sender_id){
       var now = moment().format('X',get_time);
        $.ajax
        ({
           type: "POST",
           cache: false,
            url:  baseurl + "livesupport/postchat",
          data: ({"room_id": room_id, "msg": msg, "date_sent": now}),
          beforeSend: function()
          {     
          },      
           success: function(result)
           {  
              socket.emit( 'message', {user_id: sender_id, room: room_id, message: result, date_sent : now} );
              scrollToBottom();
           }
       });  
}

// POST UPLOADED FILE TO WALL
function post_uploadedfile(convo_id)
{
  var room_id = $("#chat_room_id").val();
  var sender_id = $("#user_id").val();
  var now = moment().format();
  socket.emit( 'message', {user_id: sender_id, room: room_id, message: convo_id, date_sent : now} );
}

function popup_video(link,type){
  $.magnificPopup.open({
    items: {
      src: link
    },
    type: '' + type +''
  });
}

function popup_seen(id){
  $.magnificPopup.open({
  items: {
    src: baseurl + 'livesupport/show_popup_seen/' +id
  },
    type: 'iframe'
  });
}

function popup_invite_user(id){
  $.magnificPopup.open({
  items: {
    src: baseurl + 'livesupport/show_popup_seen/' +id
  },
    type: 'iframe'
  });
}

function show_user_info(convo_id,user_id){
  $.magnificPopup.open({
     items: {
        src: baseurl + 'livesupport/show_user_info/' +user_id,
        type: 'iframe'
      }
       

  });
}

//MESSAGE
  socket.on( 'message', function( data ) {
    var room_id = $("#chat_room_id").val();
    var user_chat_id = $("#user_chat_id").val();
     var user_id = $("#user_id").val();


     if(room_id == data.room){
        
         $.ajax
        ({
            type: "POST",
            cache: false,
            url:  baseurl + "livesupport/show_post_msg",
            data: ({"room_id": room_id, "msg_id": data.message, "date_sent":data.date_sent, "user_chat_id": user_chat_id}),      
            success: function(result)
            {  
              //console.log(data.user_id + "==" + user_id)
              if(data.user_id != user_id){
               //  audioElement.play();
              }
               $( "#chat-items" ).append(result);
                var messageTimeSent = $(".timesent");
                messageTimeSent.last().text(moment().fromNow());
                $("#chatlist_" + user_chat_id).val(0);
                //$(".msg_is_read").html('');
                 //$(".seen_by").html('');
                $(".sending_" + data.message).html('<span class="glyphicon glyphicon-ok" style="color:#FF7E00">');
                socket.emit( 'message_read', {convo_id: data.message, user_id: data.user_id} );
                socket.emit( 'check_chatlist', true );

              if(data.user_id != user_id){
                if(($(".panel-body").scrollTop() + 500 ) > $(".panel-body")[0].scrollHeight){
                    scrollToBottom();
                 }else{
                    ShowScrollToBottomMsg();
                }
              }

            }
        });
      }
  });

//Read Message
  socket.on( 'message_read', function( data ) {
    var room_id = $("#chat_room_id").val();
    var user_id = $("#user_id").val();
     if(user_id == data.user_id){disable_progress_disaply

         $.ajax
        ({
            type: "POST",
            cache: false,
            url:  baseurl + "/livesupport/message_read",
            data: ({"convo_id": data.convo_id}),
            success: function(result)
            { 
               if(result == 'read'){
                 var newmsg_top = parseInt($('ul#chat-items li:last').attr('data-id'));

                 //ata.convo_id  = latest convo id
                //remove all check
                // $(".msg_is_read").html('');
                //$(".msg_is_read").html('<span class="glyphicon glyphicon-ok" style="color:green">');
                //$(".sending_" + data.convo_id).html('<span class="glyphicon glyphicon-ok" style="color:green">');

               }else{
                  $(".sending_" + data.convo_id).html('<span class="glyphicon glyphicon-ok" style="color:#FF7E00">');
               }

            }
        });
      }
  });

// on connection to server get the id of person's room
socket.on('check_last_message', function( data ){
    var room_id = $("#chat_room_id").val();
    var user_id = $("#user_id").val();

    if((room_id == data.room_id) && (user_id == data.user_post)){
         $.ajax
        ({
            type: "POST",
            cache: false,
            url:  baseurl + "livesupport/message_read_seen",
            data: ({"convo_id": data.convo_id}),
            success: function(result)
            {

              var res = result.split("|");
               if(res[0] == 'read'){
                var newmsg_top = parseInt($('ul#chat-items li:last').attr('data-id'));

                 //ata.convo_id  = latest convo id
                //remove all check
                $(".msg_is_read").html('<span class="glyphicon glyphicon-ok" style="color:green">');
                $(".sending_" + data.convo_id).html('<span class="glyphicon glyphicon-ok" style="color:green">');
                $(".seen_by_" + data.convo_id).html(res[1]);
               }else{
                  $(".sending_" + data.convo_id).html('<span class="glyphicon glyphicon-ok" style="color:#FF7E00">');
               }
            }
        });
    }
});

// on connection to server get the id of person's room
socket.on('update_chatroom_name', function( data ){
    var room_id = $("#chat_room_id").val();
    var user_id = $("#user_id").val();

    if(room_id == data.room_id){
      if(data.room_name == ""){
        if(user_id == data.user_id){
          $(".room_name_header_" + data.room_id).text("Set Room Name");
        }else{
          $(".room_name_header_" + data.room_id).text("Welcome");
        }
      }else{
        $(".room_name_header_" + data.room_id).text(data.room_name);
      }
      socket.emit( 'check_chatlist', true );
    }
});

/* check additional room participants */
socket.on('update_room_participants', function( data ){
    var room_id = $("#chat_room_id").val();
    var user_id = $("#user_id").val();

    if(room_id == data.room_id){
        $.ajax
        ({
            type: "POST",
            cache: false,
            url:  baseurl + "/livesupport/update_room_participants",
            data: ({"room_id": data.room_id}),
            success: function(result)
            {
              $(".room_name_participants_" + data.room_id).text(result);
            }
        });       
      socket.emit( 'check_chatlist', true );
    }
});

setInterval(function(){
   
    var messageTimeSent = $(".timesent");
    messageTimeSent.each(function(){
      var each = moment($(this).data('time') * 1000);
      $(this).text(each.fromNow());
    });

  },5000);

function scrollToBottom(){
    var scrollHeight = $(".panel-body")[0].scrollHeight;
    $(".panel-body").animate({ scrollTop: scrollHeight },1000);
}
/*
setInterval(function(){

    //check last message every 10 seconds
    
    var room_id = $("#chat_room_id").val();
    var last_convo_id = parseInt($('ul#chat-items li:last').attr('data-id'));
    var user_post_id = parseInt($('ul#chat-items li:last').attr('data-user-id'));
    socket.emit( 'check_last_message', {convo_id: last_convo_id, room_id: room_id, user_post: user_post_id} );
  },5000);
*/

$( "#chat_container" ).attr('tabindex',-1).focus(function () {
   refresh_chat_container();
});

function ShowScrollToBottomMsg(){
  console.log("Scroll to bottom");
  $( "#scrollnewmsg" ).show( "slow", function() {
   $("#scrollnewmsg").html("<span class=\"show_scroll_link\"><a href=\"#\" onclick=\"scroll_to_bottom(); return false;\"><span class=\"fa fa-arrow-circle-down fa-2 \" style=\"color:#5bc0de; font-size: 14px;\"></span>&nbsp;New message below..</a></span>");

  });
}

function scroll_to_bottom(){
 $( "#scrollnewmsg" ).hide( "slow", function() {
    $(".show_scroll_link").remove();
   scrollToBottom();
  });
}
function refresh_chat_container(){
  var room_id = $("#chat_room_id").val();
  var user_id = $("#user_id").val();
  var last_convo_id = parseInt($('ul#chat-items li:last').attr('data-id'));
  var user_post_id = parseInt($('ul#chat-items li:last').attr('data-user-id'));
  //console.log("refresh chat container - sending: last_convo_id:" + last_convo_id +" + room_id:" + room_id + " user_post_id" + user_post_id);
    socket.emit( 'check_last_message', {convo_id: last_convo_id, room_id: room_id, user_post: user_post_id} ); 
}

function delete_convo(convo_id){
  if(confirm("Are you sure you want to delete this convo?")){
        $.ajax
        ({
            type: "POST",
            cache: false,
            url:  baseurl + "/livesupport/delete_row_convo",
            data: ({"convo_id": convo_id}),
            success: function(result)
            {
              $( ".convo_row_" + convo_id).remove();
              socket.emit( 'remove_convo_row', {convo_id: convo_id} );
            }
        });       
      
  }
}
socket.on('remove_convo_row', function( data ){
    $( ".convo_row_" + data.convo_id).remove();    
    socket.emit( 'check_chatlist', true );

});



function delete_thread(room_id){
  if(confirm("Are you sure you want to delete this thread?")){
        $.ajax
        ({
            type: "POST",
            cache: false,
            url:  baseurl + "livesupport/delete_thread",
            data: ({"room_id": room_id}),
            success: function(result)
            {
              if(result == 'success'){
                socket.emit( 'broadcast_remove_thread', {room_id: room_id} );

              }
            }
        });
  }
}
socket.on('broadcast_remove_thread', function( data ){
    $( ".room_list_" + data.room_id ).fadeOut( "slow", function() {
    });
});