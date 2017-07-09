<?php
session_start();
?>
<html>
<head>
<script src="https://static.opentok.com/webrtc/v2.2/js/opentok.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<?php include("include.php");?>
</head> 
<body>
<?php 
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server.php?p=generate_token_subscriber");
?>

<div id="videos">
</div>
<script>
    var apiKey      = '<?php echo $apiKey;?>'; // Replace with your API key. See https://dashboard.tokbox.com/projects
    var sessionId   = '<?php echo $sessionId;?>'; // Replace with your own session ID.
    var token       = '<?php echo $token; ?>';
// get the APIKEY and TOKEN 
$(document).ready(function() {
    initializeSession();
});

    function initializeSession() {
    // Initialize Session Object
    var session     = OT.initSession(apiKey, sessionId);
    var remoteVideo = document.getElementById('videos');
    // Subscribe to a newly created stream
    session.on('streamCreated', function(e) {

       for (var i = 0; i < e.streams.length; i++) {
        console.log("connected: " + e.streams[i].connection.connectionId );
          if (e.streams[i].connection.connectionId == session.connection.connectionId) {
             return;
          }
          var div = document.createElement('div');
          div.setAttribute('id', 'clientWindow_' + e.streams[i].connection.connectionId);
           div.setAttribute('class', 'box');
          remoteVideo.appendChild(div);
          session.subscribe(e.streams[i], div.id, {
            insertMode: 'append',
            width:      '100%',
            height:     '100%'
        });
       }
    }); 

    // Handler for sessionDisconnected event
    session.on('sessionDisconnected', function(event) {
        console.log('You were disconnected from the session.', event.reason);
    });

    session.on('connectionDestroyed', function(e) {
        //clientWindowed086bc7-0bcf-41c4-ab9f-e822d622c262
        console.log(e);
        console.log("Remove: " + e.connection.connectionId);
       // setTimeout("closeClientWindow('" + e.connection.connectionId + "')",500);
       //$("#clientWindow_" + e.connection.connectionId + "").css('display','none');
       $("#clientWindow_" + e.connection.connectionId + "").remove();
        
    }); 
    // Connect to the Session
    session.connect(token, function(error) {
        // If the connection is successful, initialize a publisher and publish to the session
        if (!error) {
            console.log('Connect as subscriber.... on window: <?php echo $video_window;?>'); 
        } else {
            console.log('There was an error connecting to the session:', error.code, error.message);
        }

    });

}   

  function closeClientWindow(client_window){
    console.log(client_window);
    
  }
</script>
</body>
</html>
