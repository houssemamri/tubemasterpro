<?php
session_start();
require "vendor/twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key 			= 'aQhuFOE4PMyLe1TAy2QDZca6R';
$consumer_secret 		= 'hQnYYzhLNJ7oP84XYcb8bSOPKGoY5TAk3nAahxeONJlyCj1sel';
$access_token_key 		= '531108969-IjzwrJq9WwKBfQ9h6QYJ0DJspCpJsVsvOp59thpv';
$access_token_secret 	= 'RB4D8JE0mNOMEjGty1dN8fQXtzdIJ8hK04Tn6peOI6nYi';

$request_token = [];
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
/*
echo '<pre>';
print_r($_SESSION['access_token']);
echo '</pre>';die();
*/
if (isset($_SESSION['access_token'])) {
	if (isset($_REQUEST['oauth_token'])) {
		header("Location: http://www.tubemasterpro.com/tokbox/trainer-chat.php?p=".$_SESSION['aw_position']);
	}
	$access_token = $_SESSION['access_token'];
	
	//- Search hashtag
	/*
$connection2  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
	$statuses = $connection2->get("search/tweets", array("q" => "#happiness", "result_type" => "recent", "count" => "5"));
*/

	//- Get User Deets
	$connection2  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
	$user = $connection2->get("users/show", array("screen_name" => $access_token['screen_name']));
	$_SESSION['access_token']['user'] = $user;
	/*
$rate_limit = $connection2->get("application/rate_limit_status", array("resources" => "users,search"));
	echo '<pre>';
	print_r($rate_limit);
	echo '</pre>';die();
*/
}
else {
	if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
	    // Abort! Something is wrong.
	    header("Location: http://www.tubemasterpro.com/tokbox/");
	}
	else if (!isset($_REQUEST['oauth_token']) && empty($_REQUEST['oauth_token'])) {
	    header("Location: http://www.tubemasterpro.com/tokbox/");
	}
	
	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
	$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
	$_SESSION['access_token'] = $access_token;
	header("Location: http://www.tubemasterpro.com/tokbox/trainer-chat.php?p=".$_SESSION['aw_position']);
}

if (isset($access_token)) {

?>
<html>
<head>
	<link rel="stylesheet" href="assets/css/jquery.qtip.min.css">
	<link rel="stylesheet" href="assets/css/app.css">
	
<script src="https://static.opentok.com/webrtc/v2.2/js/opentok.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<?php include("include-mao.php");?>
</head> 
<body>
<?php 
	$token 			= file_get_contents("http://www.tubemasterpro.com/tokbox/server-mao.php?p=generate_publisher");
    $user_id		= md5($_SESSION['access_token']['user_id']);
    $name 			= $_SESSION['access_token']['screen_name'];
    $profile_pic	= $_SESSION['access_token']['user']->profile_image_url;
    $video_window 	= "trainer";
    
    if ( $_SESSION['aw_position'] == 'trainer' ) {
	    $window1 = 'trainer';
	    $window2 = 'guest';
    }
    else {
	    $window1 = 'guest';
	    $window2 = 'trainer';
    }
    /*
if($p == "trainer_email")
    {
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server.php?p=generate_publisher_as_trainer");
        $name = "Trainer - Nathan";
        $video_window = "trainer";
    }
    if($p == "trainer_popup")
    {
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server.php?p=generate_publisher_as_trainer_popup");
        $name = "Trainer - Nathan";
        $video_window = "trainer";
    }
    if($p == "guest")
    {
        $token = file_get_contents("http://www.tubemasterpro.com/tokbox/server-mao.php?p=generate_publisher_as_guest");
        $name = "Guest - Rene";
        $video_window = "trainer";
    }
*/
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

		<div id="banner">
		</div>
        <div id="main">
        	<div class="container-fluid">
				<div id="loading-screen" class="hide-on-ending" style="display: none;">
					<div class="loading-wrapper">
						<img src="assets/img/aw-logo.png" width="144" height="144" alt="loading owl" class="upAndDown">
						<h1>Finding the stream</h1>
					</div>
				</div>
				<div class="room-container">
					<div id="stream-leftbar" class="sidebar topic">
						<div class="controls-sidebar">
							<div class="sidebar-header">
								<div id="home-button" class="logo">
									<!--
<a class="header-logo">
										<h1><span>AW!</span></h1>
									</a>
-->
									<h4><span>AW! Logo Here!</span></h4>
								</div>
							</div>
							<div class="stream-topic">
								<div id="topic-container" class="topic-container">
									<h1 id="topic-text">Title - @Author</h1>
								</div>
							<div class="stream-controls">
								<button id="tt-share" class="tweet"> Tell a little bird<span></span> </button>
							</div>
							<div style="border-bottom:none;" id="record-container" class="record-container">
					            <div class="viewer-record-state">
					                <p> Record status here</p>
					            </div>
							</div>
						</div>
						<div class="stream-content">
							<ul id="activity-feed" class="active">
								<!--
<li id="chat-beac0d0afe634fa49c79fbbbec7bf57d_528" class="activity-item" style="">
									<div class="card">
										<div id="tweet-embed-639280769932791808" class="tweet-container">
		
										</div>
									</div>
								</li>
								<li id="chat-beac0d0afe634fa49c79fbbbec7bf57d_527" class="activity-item" style="">
									<div class="card">
										<div id="tweet-embed-639280749821063168" class="tweet-container">
	
											<iframe id="twitter-widget-i1441266218362124799" scrolling="no" frameborder="0" allowtransparency="true" class="twitter-tweet twitter-tweet-rendered" allowfullscreen="true" data-tweet-id="639280749821063168" title="Twitter Tweet" style="position: static; visibility: visible; display: block; width: 100%; height: 194.672px; padding: 0px; border: none; max-width: 500px; min-width: 220px; margin-top: 10px; margin-bottom: 10px;"></iframe>
										</div>
									</div>
								</li>
-->
								<li id="chat-beac0d0afe634fa49c79fbbbec7bf57d_528" class="activity-item" style="">
									<div class="card">
										<h4>Twitter Feeds Here</h4>
										<div id="tweet-embed-639280769932791808" class="tweet-container">
											<iframe id="twitter-widget-i1441266218362124799" scrolling="no" frameborder="0" allowtransparency="true" class="twitter-tweet twitter-tweet-rendered" allowfullscreen="true" data-tweet-id="639280749821063168" title="Twitter Tweet" style="position: static; visibility: visible; display: block; width: 100%; height: 194.672px; padding: 0px; border: none; max-width: 500px; min-width: 220px; margin-top: 10px; margin-bottom: 10px;"></iframe>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div><!-- END STREAM LEFTBAR -->
				
				<div id="single-room" class="grid hide-on-ending">
					<div id="stream-started">
						<div class="user-list-container">
							<ul id="user-list">
						        <li class="viewers-count-container">
						            <p> <span id="total-viewers-count">0</span></p>
						            <p> <span id="viewer-count">0</span></p>
						            <div class="see-more-tip">See all viewers</div>
						        </li>
								<!--
<li class="viewer" id="viewer-44c1735c8b8f4071a3b16bccc22791b7" data-info="" data-hasqtip="44c1735c8b8f4071a3b16bccc22791b7">
									<img style="border-color:;" class="user-profile-image" src="https://img.blab.im/image/user/44c1735c8b8f4071a3b16bccc22791b7">
								</li>
-->
								<li class="viewer" id="viewer-df03999744c044f593f49faba5d18ad3" data-info="" data-hasqtip="df03999744c044f593f49faba5d18ad3">
									<img style="border-color:;" class="user-profile-image" src="assets/img/users/nathan.jpg">
								</li>
						        <li class="lurkers-count-container">
						            <p>
						                +<span id="lurker-count"></span>
						            </p>
						        </li>
						        <li class="overflow-active-container" style="display: none;">
						            <p>⋯</p>
						        </li>
							</ul>
						</div>
						<div class="stream-flex">
							<div class="stream-container" style="height: 608px; width: 608px;">
								<div id="host-vid" class="callers2 finished-animation">
									<div class="stream-cell-wrapper" id="caller-0-wrapper">
										<div class="stream-cell" id="caller-0">
											<div class="stream-caller-ui">
												<div class="caller-ui">
													<div class="ui-layer">


														<div class="caller-info-container">
															<div class="caller-info">
																<div class="image">
																	<img src="">
																</div>
												                <div class="handles">
												                    <a class="modal-link"></a>
												                    <p class="modal-link"></p>
												                </div>
																<div class="follow-button  ">
																	<span></span>
																</div>
															</div>
														</div>
												        <div class="caller-feels">
												            
												        </div>
												        <div class="hint-text-feels">
												            
												        </div>
												        <div class="mute-audio">
												            <span></span>
												        </div>
													</div>
												    <div class="background-layer">
												    </div>
												</div>
											</div>
											<div class="stream-caller" id="stream-caller-0">
												<div id="trainer" class="box" style="width:100%;height:100%;"></div>
											</div>
											<div class="stream-waiting" id="stream-waiting-0">
												<div class="pending-caller-cell">
													<!--     <h1 class='header-state'>  is calling in </h1> -->
													<div class="caller-avatar" style=""></div>
												</div>
											</div>
										</div>
									</div>
									<div class="stream-cell-wrapper" id="caller-1-wrapper">
										<div class="stream-cell" id="caller-1">
											<div class="stream-caller-ui">
												<div class="caller-ui">
													<div class="ui-layer">
														<div class="caller-info-container">
															<div class="caller-info">
																<div class="tt-image">
																	<img src="">
																</div>
												                <div class="handles">
												                    <a class="tt-title modal-link"></a>
												                    <p class="tt-uname modal-link"></p>
												                </div>
																<div class="follow-button  "> 
																	<span>  </span>
																</div>
															</div>
														</div>
												        <div class="caller-feels">
												            
												        </div>
												        <div class="hint-text-feels">
												            
												        </div>
												        <div class="mute-audio">
												            <span></span>
												        </div>
													</div>
												    <div class="background-layer">
												    </div>
												</div>
											</div>
											<div class="stream-caller" id="stream-caller-1">
												<div id="guest" class="box" style="width:100%;height:100%;"></div>
											</div>
											<div class="stream-waiting" id="stream-waiting-1">
												<div class="pending-caller-cell">
													<!--     <h1 class='header-state'>  is calling in </h1> -->
													<div class="caller-avatar" style=""></div>
												</div>
											</div>
										</div>
									</div>
									<!--
<div class="stream-cell-wrapper placeholder-wrapper">
										<div class="stream-cell" id="placeholder-cell" style="display: none;"></div>
									</div>
-->
								</div>
							</div>
						</div>
					</div>
					<div id="feel-container"></div>
					<!-- 
					<div id='controls-panel-button'>
					
					</div>
					<div id='controls-panel'>
					
					</div>
					-->
				</div>
			
				<div class="sidebar chat">
					<div class="sidebar-header">
						<div class="header-topic">
							<h3>Comments</h3>
						</div>
		                <div class="user-options userNavToggle">
		                    <img id="sidebar-profile-photo" src=""> 
		                </div>
					</div>
					<div class="chat-message-container">
						<div class="missed-messages-container">
		                    <div id="missed-messages-badge">
		                        <span id="#missed-text"> New Messages </span> <span class="arrow"></span> 
		                    </div>
						</div>
						<div class="chat-constrainer">
							<ul id="chat-messages-extended">
								<ul id="not-seen-mesages"></ul>
								
								
								<!--
<li data-user_id="b1b105b3b1ce4c88a2e2e02feeee5c0f" id="chat-beac0d0afe634fa49c79fbbbec7bf57d_538" data-type="message" class="message">
								    <div class="user-msg">
								        <div class="profile-image">
								            <span style="background-color:#E8A82C; background-image:url(assets/img/guest_badge_letter.svg);" class="caller-label avatar-badge"> </span>
								            <img class="modal-link" data-user_id="b1b105b3b1ce4c88a2e2e02feeee5c0f" src="assets/img/users/jubik.jpg">
								        </div>
								        <div class="msg-container">
								            <span class="time-stamp-chat">11:41 am</span>
								            <div class="chat-item-actions">
								                <a class="anchor-name modal-link" style="color:#E8A82C;" data-user_id="b1b105b3b1ce4c88a2e2e02feeee5c0f">
								                    @Jubibokibok
								                </a>
								                <div class="favorite-container faved " data-chat_id="beac0d0afe634fa49c79fbbbec7bf57d_538" data-user_id="b1b105b3b1ce4c88a2e2e02feeee5c0f">
								                    <span class="favorite-count">  1 </span>
								                    <span class="favorite-msg inline-icon"> </span>
								                </div>
								                <span class="reply-msg inline-icon" data-username="AceMuzic"> </span>
								            </div>
								
								            <p>Yo @ReneFandida</p>
								        </div>
								    </div>
								</li>
								<li data-user_id="0c2f99a01abc4ff08350c747348af696" id="chat-beac0d0afe634fa49c79fbbbec7bf57d_551" data-type="message" class="message">
								    <div class="user-msg">
								        <div class="profile-image">
								            <span style="background-color:#4884B8; background-image:url(assets/img/host_badge_letter.svg);" class="host-label avatar-badge"> </span>
								            <img class="modal-link" data-user_id="0c2f99a01abc4ff08350c747348af696" src="assets/img/users/rene.jpg">
								        </div>
								        <div class="msg-container">
								            <span class="time-stamp-chat"></span>
								            <div class="chat-item-actions">
								                <a class="anchor-name modal-link" style="color:#4884B8;" data-user_id="0c2f99a01abc4ff08350c747348af696">
								                    @ReneFandida
								                </a>
								                <div class="favorite-container  " data-chat_id="beac0d0afe634fa49c79fbbbec7bf57d_551" data-user_id="0c2f99a01abc4ff08350c747348af696">
								                    <span class="favorite-count"> </span>
								                    <span class="favorite-msg inline-icon"> </span>
								                </div>
								                <span class="reply-msg inline-icon" data-username="RobertCStern"> </span>
								            </div>
								
								            <p>Welcome guys!</p>
								        </div>
								    </div>
								</li>
-->
								<!--
<li id="chat-beac0d0afe634fa49c79fbbbec7bf57d_567" class="activity-item border chat">
									<div class="card">
									    <div class="profile-card">
									        <div data-user_id="2fbc26c42cda4a1e8f17369279e0a70b" class="card-avatar smaller profile-link">
									            <img src="https://cdn.blab.im/user_profile_images/2fbc26c42cda4a1e8f17369279e0a70b/04427a4f590343648fc75a0e9466cb40_original.jpg">
									        </div>	
									
									        <div class="card-info">
									            <h1> <span data-user_id="2fbc26c42cda4a1e8f17369279e0a70b" class="profile-link">Edwin </span><span class="hint-text">joined</span> </h1>
									        </div>
									    </div>
									
									</div>
								</li>
								<li id="chat-beac0d0afe634fa49c79fbbbec7bf57d_566" class="activity-item border chat">
									<div class="card">
									    <div class="profile-card">
									        <div data-user_id="c805a074da884118be47b93210bc69af" class="card-avatar smaller profile-link">
									            <img src="https://cdn.blab.im/user_profile_images/c805a074da884118be47b93210bc69af/9ff2a8a8c20c43ac9f3ce37e27721cb8_original.jpg">
									        </div>	
									
									        <div class="card-info">
									            <h1> <span data-user_id="c805a074da884118be47b93210bc69af" class="profile-link">Chris </span><span class="hint-text">joined</span> </h1>
									        </div>
									    </div>
									
									</div>
								</li>
-->
							</ul>
						</div>
					</div>
		            <div class="chat-input-container">
		                <div class="chat-wrapper">
		                    <div class="textarea-wrapper">
		                        <textarea maxlength="500" id="chat-input" rows="1" class="demo-default selectized" tabindex="-1" type="text" placeholder="Type Something Cool" style="height: 18px; overflow: hidden;"></textarea>
		                    </div>
		                    <div id="chat-ui-tip">
		                        <div class="left">
		                            <p id="char-count-chat">0/500</p>
		                        </div>
		                        <div class="right">
		                            <p> hit <span>enter</span> to send </p>
		                        </div>
		                    </div>
		                </div>
		            </div>
				</div>
			</div>
		    <ul class="popover nav-user-navigation-dropdown" style="right: 8px; display: none;">
		        <li class="new-blab-link"><a href="" class="create-channel">Start a new blab</a></li>
		        <li><a id="my-profile">Profile</a></li>
		        <li><a id="user-settings">Settings</a></li>
		        <li><a id="logout">Logout</a></li>
		    </ul>
		</div>
<script>
    var apiKey      = '<?php echo $apiKey;?>'; // Replace with your API key. See https://dashboard.tokbox.com/projects
    var sessionId   = '<?php echo $sessionId;?>'; // Replace with your own session ID.
    var token       = '<?php echo $token; ?>';
    var userName    = '<?php echo $name; ?>';
    var profilePic 	= '<?php echo $profile_pic; ?>';
    var userId		= '<?php echo $user_id; ?>';
    var chatBox;
	var sendBtn;
	var message;
	var session;
	var stream;
	var guest;

// get the APIKEY and TOKEN 
    $(document).ready(function() {
    	initializeSession();
    	console.log(token);
        /*

        //- Chat variables and buttons
        chatBox = $('#messageArea');
	    message = $('#chat-input');
	
	    sendBtn.on('click', function(){
	      console.log(message.val());
	      SendMessage(message.val());
	    });
*/
    });
    var connectionCount = 0;
    var connection;
    function initializeSession() {
    // Initialize Session Object
    session = OT.initSession(apiKey, sessionId);

    // Subscribe to a newly created stream

    session.on('streamCreated', function(event) {
    	var newCount = parseInt(connectionCount - 1);
    	var html = '<div class="stream-cell-wrapper" id="caller-'+newCount+'-wrapper">'+
		  				'<div class="stream-cell" id="caller-'+newCount+'">' +
		  					'<div class="stream-caller-ui">' +
		  						'<div data-user_id="'+userId+'" class="caller-ui" id="'+userId+'">' +
		  							'<div class="ui-layer">' +
		  								'<div class="caller-info-container">'+
		  									'<div class="caller-info">' +
		  										'<div class="image">' +
		  											'<img src="'+profilePic+'">' +
		  										'</div>' +
		  										'<div class="handles">' +
		  											'<a data-user_id="'+userId+'" class="modal-link"> '+userName+' </a>' +
		  											'<p data-user_id="'+userId+'" class="modal-link">@'+userName+'</p>' +
		  										'</div>' +
		  										'<div data-user_id="'+userId+'" class="follow-button  "> ' +
		  											'<span>  </span>' +
		  										'</div>' +
		  									'</div>' +
		  								'</div>' +
		  								'<div class="caller-feels">' +
		  									//'<p id="feel-count-'+userId+'" class="feel-counter">40</p>' +
		  									//'<div class="hands-icon"></div>' +
		  								'</div>' +
		  								'<div class="hint-text-feels">' +
		  									//'<p> Click to give props </p>' +
								        '</div>' +
								        '<div class="mute-audio">' +
								            '<span></span>' +
								        '</div>' +
									'</div>' +
									'<div class="background-layer">' +
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="stream-caller" id="stream-caller-'+newCount+'">' +
								'<div id="guest" class="box" style="width:100%;height:100%;"></div>' +
							'</div>' +
							'<div class="stream-waiting" id="stream-waiting-'+newCount+'">' +
								'<div class="pending-caller-cell">' +
									'<div class="caller-avatar" style="background-image:url('+profilePic+')"></div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>';
		//$('#host-vid').append(html);
		
        var subscriber = session.subscribe(event.stream, <?php echo $window2; ?>, {
            insertMode: 'append',
            width:      '100%',
            height:     '100%',
            name:       userName
        });
        
        guest = subscriber;
        
        console.log(subscriber);
        console.log(guest);
        
        SpeakerDetection(subscriber, function() {
          console.log('started talking');
        }, function() {
          console.log('stopped talking');
        });

    }); 
   
    session.on({
        connectionCreated: function (event) {
        	console.log(event);
          $('#viewer-count').text(connectionCount);
          connectionCount++;
          console.log(connectionCount + ' connections.');
        },
        connectionDestroyed: function (event) {
        	console.log(event);
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
			var html = '<div class="stream-cell-wrapper" id="caller-'+connectionCount+'-wrapper">'+
			  				'<div class="stream-cell" id="caller-'+connectionCount+'">' +
			  					'<div class="stream-caller-ui">' +
			  						'<div data-user_id="'+userId+'" class="caller-ui" id="'+userId+'">' +
			  							'<div class="ui-layer">' +
			  								'<div class="caller-info-container">'+
			  									'<div class="caller-info">' +
			  										'<div class="image">' +
			  											'<img src="'+profilePic+'">' +
			  										'</div>' +
			  										'<div class="handles">' +
			  											'<a data-user_id="'+userId+'" class="modal-link"> '+userName+' </a>' +
			  											'<p data-user_id="'+userId+'" class="modal-link">@'+userName+'</p>' +
			  										'</div>' +
			  										'<div data-user_id="'+userId+'" class="follow-button  "> ' +
			  											'<span>  </span>' +
			  										'</div>' +
			  									'</div>' +
			  								'</div>' +
			  								'<div class="caller-feels">' +
			  									//'<p id="feel-count-'+userId+'" class="feel-counter">40</p>' +
			  									//'<div class="hands-icon"></div>' +
			  								'</div>' +
			  								'<div class="hint-text-feels">' +
			  									//'<p> Click to give props </p>' +
									        '</div>' +
									        '<div class="mute-audio">' +
									            '<span></span>' +
									        '</div>' +
										'</div>' +
										'<div class="background-layer">' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="stream-caller" id="stream-caller-'+connectionCount+'">' +
									'<div id="<?php echo $window1; ?>" class="box" style="width:100%;height:100%;"></div>' +
								'</div>' +
								'<div class="stream-waiting" id="stream-waiting-'+connectionCount+'">' +
									'<div class="pending-caller-cell">' +
										'<div class="caller-avatar" style="background-image:url('+profilePic+')"></div>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>';
				//$('#host-vid').append(html);
				
				$('#sidebar-profile-photo').prop('src',profilePic);
				$('#caller-0 .tt-title').text(' '+userName);
				$('#caller-0 .tt-uname').text('@'+userName);
				$('#caller-0 .tt-image').html('<img src="'+profilePic+'">');
				$('#stream-waiting-0 .caller-avatar').css('background-image','url('+profilePic+')');
				
	        	var publisher = OT.initPublisher('<?php echo $window1;?>', {
	                insertMode: 'append',
	                width:      '100%',
	                height:     '100%',
	                name:       '<?php echo $name;?>',
	                frameRate: 	'7',
	                resolution:	'320x240'
	            });
	            session.publish(publisher);
	            
	            publisher.on('streamCreated', function (event) {
					stream = event;
					console.log(stream);
				});
			
        } else {
            console.log('There was an error connecting to the session:', error.code, error.message);
        }

    });

}   

</script>

<script>
		window.twttr = (function(d, s, id) {
		    var js, fjs = d.getElementsByTagName(s)[0],
		        t = window.twttr || {};
		    if (d.getElementById(id)) return t;
		    js = d.createElement(s);
		    js.id = id;
		    js.src = "https://platform.twitter.com/widgets.js";
		    fjs.parentNode.insertBefore(js, fjs);
		
		    t._e = [];
		    t.ready = function(f) {
		        t._e.push(f);
		    };
		
		    return t;
		}(document, "script", "twitter-wjs"));
		</script>
	</div>
    <div style="z-index:1000;" id="modal"></div>

        <!-- Intercom -->
        <script type="text/javascript" async="" src="https://widget.intercom.io/widget/kj3wej66"></script><script async="" src="//www.google-analytics.com/analytics.js"></script><script id="twitter-wjs" src="https://platform.twitter.com/widgets.js"></script><script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true; s.src='https://widget.intercom.io/widget/kj3wej66'; var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
        <!-- Twitter Widget -->
        <script>window.twttr = (function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
          if (d.getElementById(id)) return t;
          js = d.createElement(s);
          js.id = id;
          js.src = "https://platform.twitter.com/widgets.js";
          fjs.parentNode.insertBefore(js, fjs);
          t._e = [];
          t.ready = function(f) {
            t._e.push(f);
          };
          return t;
         }(document, "script", "twitter-wjs"));</script>

        <!-- Google analytics -->
        <!--
<script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-62611326-1', 'auto');
          ga('send', 'pageview');
        </script>
-->
        
            <script src="assets/js/aw.js?<?php echo md5(date()); ?>"></script>
</body>
</html>
<?php
	}
	else {
		header("Location: http://www.tubemasterpro.com/tokbox/");
	}
?>