<?php

/*    $fb = new Facebook\Facebook([
      'app_id' => '{1680609378892560}',
      'app_secret' => '{c4455b08945d17bca7ec46a383e8e9ef}',
      'default_graph_version' => 'v2.5',
      ]);

    $linkData = [
      'link' => 'http://www.tubemasterpro.com/awesome/wowcast/archived_details.php?p=check_video_archived_get&token=95112a2d4f3ea34791845d9e64c5a11a&recorded_id=4e49aa17-2e52-40e2-8107-6c84753de0e6',
      'message' => 'User provided message',
      ];

    try {
      // Returns a `Facebook\FacebookResponse` object
      $response = $fb->post('/me/feed', $linkData, '{}');
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    $graphNode = $response->getGraphNode();

    echo 'Posted with id: ' . $graphNode['id'];*/

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Theme Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme.css" rel="stylesheet">

    <link href="css/mq.css" rel="stylesheet">

    <link href="fonts/wc-fonts.css" rel="stylesheet">

    <link href="css/wc.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

  </head>

  <body role="document">

    <!-- <div class="container">
      <video width="400" controls>
        <source src="vids/Smooth Jazz Guitar Solo.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
      </video>
    </div> -->

    <button id="fb-post" class="btn btn-primary">Post</button>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>


    <script>

    $(document).ready(function() {
      $.ajaxSetup({ cache: true });
      $.getScript('//connect.facebook.net/en_US/sdk.js', function(){
        FB.init({
          appId: '1680609378892560',
          version: 'v2.4' // or v2.0, v2.1, v2.2, v2.3
        });
        //$('#loginbutton,#feedbutton').removeAttr('disabled');
        //FB.getLoginStatus(updateStatusCallback);
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            console.log('Logged in.');
          }
          else {
            console.log('Logging in.');
            FB.login();
          }
        });
      });

      $('#fb-post').on('click', function (){
/*        FB.login(function(){
          // Note: The call will only work if you accept the permission request
          FB.api('/me/feed', 'post', {message: 'Hello, world!', link: 'http://www.tubemasterpro.com/awesome/wowcast/archived_details.php?p=check_video_archived_get&token=95112a2d4f3ea34791845d9e64c5a11a&recorded_id=4e49aa17-2e52-40e2-8107-6c84753de0e6'});
        }, {scope: 'publish_actions'});*/

            publishWallPost();


      });

    });

    //facebook: post to wall
    function publishWallPost() {
    
		var d = new Date();
		var n = d.getTime();
        
          FB.ui({
              method: 'feed',
              display: 'iframe',
              name: 'WOWCast',
              caption: 'Caption Text',
              description: 'Your description text',
              link: 'https://wowcast.me/replay/84-test-012',//'http://www.tubemasterpro.com/awesome/fb/wowcast/test.php?t=' + n,
              picture: 'http://www.tubemasterpro.com/awesome/fb/wowcast/images/guy-smile.png'
            },
            function (response) {
              console.log('publishStory response: ', response);
            });
          return false;
    }

    /*window.fbAsyncInit = function() {
        FB.init({
              appId      : '1680609378892560',
              xfbml      : true,
              version    : 'v2.5'
            });
            };

            (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "//connect.facebook.net/en_US/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));*/
    </script>

  </body>
</html>
