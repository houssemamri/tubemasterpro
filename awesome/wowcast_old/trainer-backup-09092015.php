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
    if($p == "trainer")
    {
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server.php?p=generate_publisher");
        $name = "Trainer - Nathan";
        $video_window = "trainer";
    }

    if($p == "guest")
    {
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server.php?p=generate_publisher");
        $name = "Guest - Rene";
        $video_window = "trainer";
    }
/*
    if($p == "client")
    {
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server.php?p=generate_token_subscriber");
        $name = "Guest - Rene";
        $video_window = "subscriber";
        $is_client = 1;
    }
*/
?>

<div id="videos">
        <div id="trainer" class="box"></div>
        <div id="guest"  class="box"></div>
</div>
<script>
    var apiKey      = '<?php echo $apiKey;?>'; // Replace with your API key. See https://dashboard.tokbox.com/projects
    var sessionId   = '<?php echo $sessionId;?>'; // Replace with your own session ID.
    var token       = '<?php echo $token; ?>';

// get the APIKEY and TOKEN 
    $(document).ready(function() {
        initializeSession();
    });
    var connectionCount = 0;
    function initializeSession() {
    // Initialize Session Object
    var session = OT.initSession(apiKey, sessionId);

    // Subscribe to a newly created stream

    session.on('streamCreated', function(event) {
        var subscriber = session.subscribe(event.stream, 'guest', {
            insertMode: 'append',
            width:      '100%',
            height:     '100%'
        });

        SpeakerDetection(subscriber, function() {
          console.log('started talking');
        }, function() {
          console.log('stopped talking');
        });

    }); 
   
    session.on({
        connectionCreated: function (event) {
          connectionCount++;
          console.log(connectionCount + ' connections.');
        },
        connectionDestroyed: function (event) {
          connectionCount--;
          console.log(connectionCount + ' connections.');
        },
        sessionDisconnected: function sessionDisconnectHandler(event) {
          // The event is defined by the SessionDisconnectEvent class
          console.log('Disconnected from the session.');
          document.getElementById('disconnectBtn').style.display = 'none';
          if (event.reason == 'networkDisconnected') {
            alert('Your network connection terminated.')
          }
        }
      });     


    var SpeakerDetection = function(subscriber, startTalking, stopTalking) {
      var activity = null;
      subscriber.on('audioLevelUpdated', function(event) {
        var now = Date.now();
        if (event.audioLevel > 0.2) {
          if (!activity) {
            activity = {timestamp: now, talking: false};
          } else if (activity.talking) {
            activity.timestamp = now;
          } else if (now- activity.timestamp > 1000) {
            // detected audio activity for more than 1s
            // for the first time.
            activity.talking = true;
            if (typeof(startTalking) === 'function') {
              startTalking();
            }
          }
        } else if (activity && now - activity.timestamp > 3000) {
          // detected low audio activity for more than 3s
          if (activity.talking) {
            if (typeof(stopTalking) === 'function') {
              stopTalking();
            }
          }
          activity = null;
        }
      });
    };

    // Handler for sessionDisconnected event
    session.on('sessionDisconnected', function(event) {
        console.log('You were disconnected from the session.', event.reason);
    });

    // Connect to the Session
    session.connect(token, function(error) {
        // If the connection is successful, initialize a publisher and publish to the session
        if (!error) {

            var publisher = OT.initPublisher('<?php echo $video_window;?>', {
                insertMode: 'append',
                width:      '100%',
                height:     '100%',
                name:       '<?php echo $name;?>'
            });
            session.publish(publisher); 
        } else {
            console.log('There was an error connecting to the session:', error.code, error.message);
        }

    });
}   

</script>
</body>
</html>