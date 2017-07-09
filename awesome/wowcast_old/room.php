<?php
session_start();
require_once("database.php");
$aw_model = new AW_Model();

//- OPEN TOK
$loader = require __DIR__ . "/vendor/autoload.php";
$loader->addPsr4('OpenTok\\', __DIR__.'/OpenTok');

use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use OpenTok\Session;
use OpenTok\Role;

$apiKey 	= "45391582";
$apiSecret 	= "5aea02a33fac3eba6bafb85e75bcb0ce80c417c0";
$opentok	= new OpenTok($apiKey, $apiSecret);
//- END OPEN TOK
/*

$archive = $opentok->getArchive('0f2dc819-b387-45b8-8e76-c5583a43dd00');
echo '<pre>';
print_r($archive->status);
echo '</pre>';die();
*/

//- TWITTER API
require "vendor/twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key 			= 'aQhuFOE4PMyLe1TAy2QDZca6R';
$consumer_secret 		= 'hQnYYzhLNJ7oP84XYcb8bSOPKGoY5TAk3nAahxeONJlyCj1sel';
$access_token_key 		= '531108969-IjzwrJq9WwKBfQ9h6QYJ0DJspCpJsVsvOp59thpv';
$access_token_secret 	= 'RB4D8JE0mNOMEjGty1dN8fQXtzdIJ8hK04Tn6peOI6nYi';
$oauth_callback			= $baseurl.'callback.php';
$request_token = [];

//- END TWITTER API

$user;
$sessionId = '';

//- PRETTY URLs
#remove the directory path we don't want
$request 	= $_SERVER['REQUEST_URI'];
$params  	= split("/", $request);
$room_slug  = $params[3];
$room 		= '';//$_GET['room'];
$token 		= null;
$roomData 	= null;
$roomInfo 	= null;

/*
$curr = gmdate('Y-m-d H:m:s', 1446573720);
$d = new \DateTime($curr);
*/

/*
$date = date_create('2015-11-04 02:02:00 +0800');
$curr = date('Y-m-d H:m:s');
echo $curr;
$new  = date_create($curr.' +0800');
echo '<pre>';
print_r($date);
echo '</pre>';
echo '<pre>';
print_r($new);
echo '</pre>';
echo date_format($date,"l Y/m/d H:m:s");
die();
*/
/*
$d = new \DateTime('2015-11-04 02:02:00 +0800');
echo 'The date is ' . $d->format('Y-m-d H:i:s O') . '<br />';
//The date is 2010-10-10 10:10:00 -0600

$d->setTimezone(new \DateTimeZone('GMT'));
echo 'The new date is ' . $d->format('Y-m-d H:i:s O');
//The new date is 2010-10-10 16:10:10 +0000
die();
*/

/*
if ( $room == 'watchmemaomao' ) {
	$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
	$lists  = $connection3->get("lists/list", array('user_id' => 531108969));
	$listId = 0;
	foreach ($lists as $list) {
		if ($list->name == $post['twitter_list_name']) {
			$listId = $list->id;
			break;
		}
	}
	if (!$listId) {
		$lists = $connection3->post("lists/create", array('name' => $post['twitter_list_name']));
	}
	else {
		$data['twitter_list_id'] = $listId;
	}
	echo '<pre>';
	print_r($lists);
	echo '</pre>';
	die();
}
*/
		
		/*
$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
							
		//- LIST CRUD FOR TWITTER
		$lists = $connection3->get("lists/list", array("user_id"=>306097523));
		echo '<pre>';
		print_r($lists);
		echo '</pre>';die();
*/

//if ( isset($room) ) {
if ( $room_slug != 'room.php' ) {

	if ( !$roomData = $aw_model->check_room($room_slug) ) {
		header("Location: ".$baseurl."");
	}
	else {
		$room = $roomData['session_id'];
		$_SESSION['aw_room'] = $roomData['slug'];
		
		$roomInfo = array(
			'name' 		=> $roomData['name'],
			'trainer'	=> $aw_model->check_user_by_id($roomData['trainer_id'])['twitter_name'],
			'guest'		=> $aw_model->check_user_by_id($roomData['guest_id'])['twitter_name'],
		);
	}
}

if (isset($_SESSION['access_token'])) {

		
		/*
if ( $roomData['active'] == 0 && $room_slug != 'room.php' ) {
			if ( $roomData['trainer_id'] != $_SESSION['access_token']['id'] ) {
				header("Location: ".$baseurl."");die();
			}
		}
*/
		if ($roomData['active'] != 1 && $room_slug != 'room.php') {
			header("Location: ".$baseurl."");die();
		}
	
		$request_token['oauth_token'] = $_SESSION['oauth_token'];
		$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
		//- Get User Deets
		$connection2  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
		$ttuser = $connection2->get("users/show", array("screen_name" => $_SESSION['access_token']['screen_name']));
		$_SESSION['access_token']['user'] = $ttuser;
		
		$user = array(
			"id"	 => $_SESSION['access_token']['user_id'],
			"name"	 => $_SESSION['access_token']['screen_name'],
			"pic"	 => $_SESSION['access_token']['user']->profile_image_url,
			"db_id"  => $_SESSION['access_token']['id'],
			"twitter_data" => array(
				'id' 				=> $ttuser->id,
				'name'				=> $ttuser->name,
				'screen_name'		=> $ttuser->screen_name,
				'friends_count'		=> $ttuser->friends_count,
				'followers_count'	=> $ttuser->followers_count,
				'following'			=> $ttuser->following,
				'profile_image_url'	=> $ttuser->profile_image_url
			)
		);
		
	
	if ( isset($room) && !empty($room) ) {
		$room_arr = $aw_model->check_room($room_slug);
		$sessionId= $room_arr['session_id'];
		
		if ($roomData['trainer_id'] == $user['db_id']) {
			//header("Location: ".$baseurl."");die();
			if ( isset($room_arr['trainer_online']) && !empty($room_arr['trainer_online']) ) {
				//header("Location: ".$baseurl."");die();
			}
			else {
				$aw_model->update_trainer_online($room_arr['id'], 1);
			}
		}
		
		//- Search hashtag
		/*
	$connection2  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
		$statuses = $connection2->get("search/tweets", array("q" => "#happiness", "result_type" => "recent", "count" => "5"));
	*/
		/*
	echo '<pre>';
		print_r($user);
		echo '</pre>';die();
	*/
		
		/*
if ( is_null($room_arr['guest_id']) || empty($room_arr['guest_id']) ) {
			$user['type'] = "Guest";
			$data = array(
				"room"			=> $room,
				"guest_id" 	    => $user['db_id']
			);
			$update_result = $aw_model->update_room_guest($data);
			$token = $opentok->generateToken($sessionId, array(
			    'role'       => Role::PUBLISHER,
			    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
			    'data'       => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|0'
			));
		}
*/
		if ($user['db_id'] == $room_arr['trainer_id']) {
			$user['type'] = "Trainer";
			$banner_type = ( $room_arr['banner_type'] ) ? ( ( $room_arr['banner_type'] == 1 ) ? 'email' : 'popup' ) : 'none';
			
			$token = $opentok->generateToken($sessionId, array(
			    'role'       => Role::PUBLISHER,
			    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
			    'data'       => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|'.$banner_type.'|'.$room_arr['banner_url'].'|0|'.$room_arr['banner_image'].'|1|'.json_encode($user['twitter_data']).'|web'
			));
		}
		else {
			if ($user['db_id'] == $room_arr['guest_id']) {
				$user['type'] = "Guest";
				$data = array(
					"room"			=> $room,
					"guest_id" 	    => $user['db_id']
				);
				$aw_model->update_room_guest($data);
				$token = $opentok->generateToken($sessionId, array(
				    'role'       => Role::PUBLISHER,
				    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
				    'data'       => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|0|'.json_encode($user['twitter_data']).'|web'
				));
			}
			else {
				$user['type'] = "Client";
				$token = $opentok->generateToken($sessionId, array(
				    //'role'       => Role::SUBSCRIBER,
				    'role'       => Role::PUBLISHER,
				    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
				    'data'		 => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|1|'.json_encode($user['twitter_data']).'|web'
				));
			}	
		}
	}
	/*
$rate_limit = $connection2->get("application/rate_limit_status", array("resources" => "users,search"));
	echo '<pre>';
	print_r($rate_limit);
	echo '</pre>';die();
*/

}
else if (isset($_SESSION['aw_user'])) {
	if ( !isset($_SESSION['aw_user']['access_token']) ) {
		$connection3 	= new TwitterOAuth($consumer_key, $consumer_secret);
		$request_token 	= $connection3->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));
		
		$_SESSION['oauth_token'] 		= $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		
		$url = $connection3->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		
		header("Location: ".$url);
	}
	else {
		
		//$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
		//- MEMBERS CRUD FOR TWITTER LIST
		//$lists = $connection3->get("lists/members", array('list_id' => 222657501));
		//$lists = $connection3->post("lists/members/destroy", array('list_id' => 222657501, 'user_id' => 1710743930));
		//$lists = $connection3->post("lists/members/create", array('list_id' => 222657501, 'user_id' => 1710743930));
		
		//- LIST CRUD FOR TWITTER
		//$lists = $connection3->post("lists/destroy", array('list_id' => 222657501));
		//$date  = date('Ymd', time());
		//$lists = $connection3->post("lists/create", array('name' => 'WOWCast-'.$date));
		
	/*
echo '<pre>';
	print_r($_SESSION['aw_user']['access_token']['user']);
	echo '</pre>';die();
*/
		$user_type = ($_SESSION['aw_user']['db_id'] == $roomData['trainer_id']) ? 'Trainer' : 'Client';
		$user = array(
			"id"	 => $_SESSION['aw_user']['id'],
			"name"	 => $_SESSION['aw_user']['name'],
			"pic"	 => $_SESSION['aw_user']['pic'],
			"db_id"	 => $_SESSION['aw_user']['db_id'],
			"type"	 => $user_type,
			"twitter_data" => array(
				'id' 				=> $_SESSION['aw_user']['access_token']['user']->id,
				'name'				=> $_SESSION['aw_user']['access_token']['user']->name,
				'screen_name'		=> $_SESSION['aw_user']['access_token']['user']->screen_name,
				'friends_count'		=> $_SESSION['aw_user']['access_token']['user']->friends_count,
				'followers_count'	=> $_SESSION['aw_user']['access_token']['user']->followers_count,
				'following'			=> $_SESSION['aw_user']['access_token']['user']->following,
				'profile_image_url'	=> $_SESSION['aw_user']['access_token']['user']->profile_image_url
			)
		);
		
		/*
if ( $roomData['active'] == 0 && $room_slug != 'room.php' ) {
			if ( $roomData['trainer_id'] != $user['db_id'] ) {
				header("Location: ".$baseurl."");die();
			}
		}
*/
		if ($roomData['active'] != 1 && $room_slug != 'room.php') {
			header("Location: ".$baseurl."");die();
		}
		
		if ( isset($room) && !empty($room) ) {
			$room_arr = $aw_model->check_room($room_slug);
			$aw_model->deactivate_rooms($user['db_id']);
			$sessionId= $room_arr['session_id'];
	
			if ($user['db_id'] == $room_arr['trainer_id']) {
				$banner_type = ( $room_arr['banner_type'] ) ? ( ( $room_arr['banner_type'] == 1 ) ? 'email' : 'popup' ) : 'none';
				
				$token = $opentok->generateToken($sessionId, array(
				    'role'       => Role::PUBLISHER,
				    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
				    'data'       => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|'.$banner_type.'|'.$room_arr['banner_url'].'|0|'.$room_arr['banner_image'].'|1|'.json_encode($user['twitter_data']).'|web'
				));
			}
			else {
				//header("Location: ".$baseurl."");
				if ($user['db_id'] == $room_arr['guest_id']) {
					$user['type'] = "Guest";
					$data = array(
						"room"			=> $room,
						"guest_id" 	    => $user['db_id']
					);
					$aw_model->update_room_guest($data);
					$token = $opentok->generateToken($sessionId, array(
					    'role'       => Role::PUBLISHER,
					    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
					    'data'       => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|0|'.json_encode($user['twitter_data']).'|web'
					));
				}
				else {
					$user['type'] = "Client";
					$token = $opentok->generateToken($sessionId, array(
					    'role'       => Role::SUBSCRIBER,
					    'data'		 => ''.$user['type'].'|'.$user['name'].'|'.$user['pic'].'|'.$user['db_id'].'|1|'.json_encode($user['twitter_data']).'|web'
					));
				}
			}
		}
	}
}
else {

//http://goo.gl/FvXEIw
		$aw_model = new AW_Model();
	$sql = "select id
				from 
					rooms as r
				where slug = '$room_slug' and active != '2' LIMIT 1";

			$check_sched = $aw_model->conn->query($sql);
			if($check_sched->num_rows == 0){
				echo "";
			}else{
				$sch 						= $check_sched->fetch_assoc();	
				$roomdt = "?id={$sch['id']}";
			}
	header("Location: ".$baseurl."$roomdt");
}

?>
<html>
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# video: http://ogp.me/ns/video#">
    <meta charset="UTF-8">
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/font-awesome.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/bootstrap-social.css">
	<!--
<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/fullcalendar.min.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/fullcalendar.print.css">
-->
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/bootstrap/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="http://cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/app.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/style.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/fonts/wc-fonts.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/wc.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $baseurl; ?>assets/css/animate.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="<?php echo $baseurl; ?>assets/bootstrap/js/moment.js" type="text/javascript"></script>
	<!-- <script src="<?php echo $baseurl; ?>assets/js/fullcalendar.min.js" type="text/javascript"></script> -->
	<script src="<?php echo $baseurl; ?>assets/bootstrap/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<script src="https://static.opentok.com/webrtc/v2.2/js/opentok.min.js"></script>
	<script src="https://cdn.firebase.com/js/client/2.3.0/firebase.js"></script>
	<script src="<?php echo $baseurl; ?>assets/js/jquery.urlshortener.min.js" type="text/javascript"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
	<script src="<?php echo $baseurl; ?>assets/js/holder.min.js" type="text/javascript"></script>
	<script src="http://cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.js" type="text/javascript"></script>
    <base href="/wowcast/">
    
    <meta name="twitter:creator" content="@australiawow!">
    <meta name="twitter:description" content="Watch live conversations about topics that matter most to you">
    <meta name="twitter:image" content=""><meta property="og:type" content="product">

    
    <link rel="shortcut icon" href="<?php echo $baseurl; ?>assets/img/_icons/favicon.ico">
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $baseurl; ?>assets/img/_icons/57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $baseurl; ?>assets/img/_icons/60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $baseurl; ?>assets/img/_icons/72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $baseurl; ?>assets/img/_icons/76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $baseurl; ?>assets/img/_icons/114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $baseurl; ?>assets/img/_icons/120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $baseurl; ?>assets/img/_icons/144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $baseurl; ?>assets/img/_icons/152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $baseurl; ?>assets/img/_icons/180x180.png">
    <link rel="icon" type="image/png" href="<?php echo $baseurl; ?>assets/img/_icons/32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo $baseurl; ?>assets/img/_icons/16x16.png" sizes="16x16">
<!--
    <link rel="icon" type="image/png" href="/assets/favicon/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="/assets/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/assets/favicon/android-chrome-192x192.png" sizes="192x192">
    <link rel="manifest" href="/manifest.json?5">
    <meta name="msapplication-TileColor" content="#19103a">
    <meta name="msapplication-TileImage" content="/assets/favicon/mstile-144x144.png">
    <meta name="msapplication-config" content="/assets/favicon/browserconfig.xml">
    <meta name="theme-color" content="#4E4597">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="avgthreatlabs-verification" content="c27834922b131282c1a0fb7742a4bf9989fbf663">
    <link type="text/css" media="screen" rel="stylesheet" href="https://static.opentok.com/webrtc/v2.6.6/css/TB.min.css">
-->
    <title>WOWCAST! - Watch live conversations</title>
	<meta name="description" content="Welcome to WOWCAST!">
	
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@australiawow!">
	<meta name="twitter:title" content="Watch live conversations about topics that matter most to you">
	<meta name="twitter:creator" content="@australiawow!">
	<meta name="twitter:description" content="">
	<meta name="twitter:image" content="">
	
	<meta property="og:url" content="http://www.tubemasterpro.com/wowcast/">
	<meta property="og:site_name" content="WOWCast!">
	<meta property="og:title" content="<?php echo $roomData['name']; ?>">
	<meta property="og:image" content="http://www.tubemasterpro.com/wowcast/assets/img/wowcast-logo.png">
	<meta property="og:type" content="product">

	<style>
	    body, html {
	    /* background-color: gray; */
	    height: 100%;
	}
	#videos {
	    width: 920px;
	    height: 100%;
	    margin: auto;
	}
	.box {
		left: 450px;
		margin: 5px;
	    top: 0;
	    width: 420px;
	    height: 340px;
	    z-index: 10;
	    border: 3px solid white;
	    display: inline-block;
	}
	/*
	#subscriber {
	    left: 450px;
	    top: 0;
	    width: 420px;
	    height: 340px;
	    z-index: 10;
	    border: 3px solid white;
	    display: inline-block;
	}
	#publisher {
	    position: absolute;
	    width: 420px;
	    height: 340px;
	    top: 0px;
	    left: 10px;
	    z-index: 100;
	    border: 3px solid white;
	    border-radius: 3px;
	}
	*/
	</style>
	
	<?php
		if (isset($room) && !empty($room)) {
			if ($roomData['fb_pixel']) {
				$fb_pixel = stripslashes(htmlspecialchars_decode($roomData['fb_pixel']));
				echo $fb_pixel;
			}
		}
	?>
</head>
    <body id="body" cz-shortcut-listen="true">
    	<script>
		  window.fbAsyncInit = function() {
		    FB.init({
		      appId      : '757032001073585',
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
		   }(document, 'script', 'facebook-jssdk'));
		</script>
    
    
    
    	<input type="hidden" data-room='<?php echo json_encode($roomInfo); ?>' id="room-info" />
        <div id="banner">
		</div>
        <div id="main">
        <?php if (isset($room) && !empty($room)) : ?>
        	<div class="container-fluid">
				<div id="loading-screen" class="hide-on-ending" style="z-index:10000;">
					<div class="loading-wrapper">
						<img src="<?php echo $baseurl; ?>assets/img/wowcast-loader.png" alt="loading owl" class="upAndDown">
						<h1>Sit tight, Tiger ;)</h1>
					</div>
				</div><!-- #loading-screen -->
				<div class="room-container">
					<div id="stream-leftbar" class="sidebar topic left-sidebar">
						<div class="controls-sidebar">
							<div class="sidebar-header">
								<div id="home-button" class="logo">
									<a class="header-logo" href="#" style="background-size: 20% 80%;">
										<h1><span>WOWCAST!</span></h1>
									</a>
									<!-- <h4><span>AW! Logo Here!</span></h4> -->
								</div>
							</div><!-- .sidebar-header -->
							<div class="stream-topic">
								<div id="topic-container" class="topic-container">
									<h1 id="topic-text text-center"><?php echo $roomData['name']; ?></h1>
								</div>
								<div class="stream-controls">
									<div id="social-state" class="stream-buttons">
										<button id="tt-share" class="tweet"> Tweet This Cast!<span></span> </button>
										<button id="fb-share" class="share"> Share This Cast!<span></span> </button>
										<button id="email-share" class="send"> Email This Cast!<span></span> </button>
									</div>
									<?php if ( $user['db_id'] == $roomData['trainer_id'] ) : ?>
									<div id="record-state" class="stream-buttons">
											<?php if (!isset($roomData['archiveId']) && empty($roomData['archiveId'])) : ?>
											<button id="record-room" data-trainer_id="<?php echo $roomData['trainer_id']; ?>" class="btn disabled">
											<?php else: ?>
												Recorded
											<?php endif; ?>
											</button>
											<button id="end-room" data-trainer_id="<?php echo $roomData['trainer_id']; ?>" class="btn">
												End Room
											</button>
									</div>
									<?php endif; ?>
								</div>
								<!--
<div style="border-bottom:none;padding:0;" id="record-container" class="record-container stream-controls">
					            	<div class="viewer-record-state" style="padding:0;">
										<?php if ( $user['db_id'] == $roomData['trainer_id'] ) : ?>
											<button style="width:70%; padding: 0.7em 1.1em;" id="end-room" data-trainer_id="<?php echo $roomData['trainer_id']; ?>" class="btn">End Room</button>
										<?php endif; ?>
									</div>
								</div>
-->
							</div><!-- .stream-topic -->
							
							<div class="stream-content">
								<ul id="activity-feed" class="active">
									<a class="twitter-timeline" href="https://twitter.com/hashtag/wowcast"
										data-widget-id="662165965279358976">
										#wowcast Tweets
									</a>
								</ul>
							</div>
						</div><!-- .controls-sidebar -->
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
							</ul>
						</div><!-- .user-list-container -->
						<div class="stream-flex">
							<?php
								$callers = 'callers2';//($roomData['allow_guest_client'] == 0) ? 'callers2' : 'callers1';
								$top     = 'top:10%;';//($roomData['allow_guest_client'] == 0) ? 'top:10%;' : 'top:5%;';
							?>
							<div class="stream-container" style="height: 708px; width: 708px; <?php echo $top; ?>">
								<div id="host-vid" class="<?php echo $callers; ?> finished-animation animated bounceIn">
									<div class="stream-cell-wrapper" id="caller-0-wrapper" data-id="">
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
												                    <a class="tt-title modal-link"></a>
												                    <p class="tt-uname modal-link"></p>
												                </div>
																<!--
<div class="follow-button  ">
																	<span></span>
																</div>
-->
															</div>
														</div>
												        <div class="caller-feels" style="top:0.5em;">
												            <p class="feel-counter">0</p>
												            <div class="hands-icon"></div>
												        </div>
												        <div class="hint-text-feels" style="bottom:5;width:80%;pointer-events:all !important;">
												        	<?php if ($roomData['banner_image']) : ?>
												        	<?php
												        		if ( $roomData['banner_type'] == 2 ) {
													        		$banner_attr = 'href="#" data-banner_type="popup" data-url="'.$room_arr['banner_url'].'"';
												        		}
												        		else if ( $roomData['banner_type'] == 1 ) {
													        		$banner_attr = 'href="#" data-banner_type="email" data-mc_api="'.$room_arr['mc_api'].'" data-mc_api="'.$room_arr['mc_api'].'"';
												        		}
												        	?>
												        	
												        	<a id="banner-prompt" <?php echo $banner_attr; ?> class="thumbnail" style="margin:0;">
												        		<img id="banner-image" src="<?php echo $roomData['banner_image']; ?>" style="max-width: 100%; max-height: 50px;" />
												        	</a>
												        	<?php endif; ?>
												        </div>
												        <div class="mute-audio">
												            <span></span>
												        </div>
													</div>
												    <div class="background-layer">
												    </div>
												</div>
											</div>
											<div class="stream-caller" id="stream-caller-0" data-id="" >
												<div id="trainer" class="box" style="width:100%;height:100%;margin:0;"></div>
											</div>
											<div class="stream-waiting" id="stream-waiting-0">
												<div class="pending-caller-cell">
													<!--     <h1 class='header-state'>  is calling in </h1> -->
													<div class="caller-avatar" style=""></div>
												</div>
											</div>
										</div>
									</div>
									<div class="stream-cell-wrapper" id="caller-1-wrapper" data-id="" style="display:none;">
										<div class="stream-cell" id="caller-1">
											<div id="kick-guest" class="open-seat-wrapper" style="z-index:100000; display:none;">    
												<div class="open-seat-container">
													<button data-id="" class="caller-button call-out" style="padding: 1.3em 1.2em;"> Kick <span class="inline-icon"></span> </button>
												</div>
											</div>
											<div class="stream-caller-ui">
												<div class="caller-ui">
													<div class="ui-layer">
														<div class="caller-info-container">
															<div class="caller-info">
																<div class="image">
																	<img src="">
																</div>
												                <div class="handles">
												                    <a class="tt-title modal-link"></a>
												                    <p class="tt-uname modal-link"></p>
												                </div>
																<!--
<div class="follow-button  "> 
																	<span>  </span>
																</div>
-->
															</div>
														</div>
												        <div class="caller-feels" style="top:0.5em;">
												            <p class="feel-counter">0</p>
												            <div class="hands-icon"></div>
												        </div>
												        <div class="hint-text-feels">
												            <p> Click to give props </p>
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
												<div id="guest" class="box" style="width:100%;height:100%;margin:0;"></div>
											</div>
											<div class="stream-waiting" id="stream-waiting-1">
												<div class="pending-caller-cell">
													<h1 class='header-state' style="display:none;">  is calling in </h1>
													<div class="caller-avatar" style=""></div>
													<div id="incoming-guest" class="open-seat-wrapper is_calling animated bounceIn" style="z-index: 100000;">    
														<div id="incoming-client" class="open-seat-container" style="margin-bottom:5px; display:none;">
															<button id="cancel-call" data-id="" class="caller-button" style="padding: 0.5em 0.5em;background:red;"> Cancel <span class="inline-icon"></span> </button>
														</div>
														<div id="incoming-host" class="open-seat-container" style="margin-bottom:5px; display:none;">
															<button id="accept-incoming" data-id="" class="caller-button" style="padding: 0.5em 0.5em;background:#27ae60;"> Accept <span class="inline-icon"></span> </button>
													<button id="decline-incoming" data-id="" class="caller-button" style="padding: 0.5em 0.5em;background:red;"> Decline <span class="inline-icon"></span> </button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
						            
									<?php if ( $roomData['allow_guest_client'] == 1 ) : ?>
							            <div class="stream-cell-wrapper placeholder-wrapper" id="placeholder-wrapper-cell">
							                <div class="stream-cell open-state" id="placeholder-cell">
							                	<div class="open-seat-wrapper">    
													<div class="open-seat-container">
														<?php if ($user['db_id'] == $roomData['trainer_id']) : ?>
														<button id="lock-btn" class="caller-button lock-seat" style="background-color:red;"> Lock Seat <span data-lock="0" class="lock-icon inline-icon"></span> </button>
														<?php else: ?>
														<button class="caller-button call-in"> Join <span class="join-icon inline-icon"></span> </button>
														<?php endif; ?>
													</div>
												</div>
											</div>
							            </div>
									<?php else: ?>
										<?php if ($user['db_id'] == $roomData['trainer_id']) : ?>
											<div class="stream-cell-wrapper placeholder-wrapper" id="placeholder-wrapper-cell">
								                <div class="stream-cell open-state" id="placeholder-cell">
								                	<div class="open-seat-wrapper">    
														<div class="open-seat-container">
															<button id="lock-btn" class="caller-button lock-seat" style="background-color:#27ae60;"> Unlock Seat <span data-lock="1" class="unlock-icon inline-icon"></span> </button>
														</div>
													</div>
												</div>
								            </div>	
										<?php else: ?>
											<div class="stream-cell-wrapper placeholder-wrapper" id="placeholder-wrapper-cell">
								                <div class="locked-seat-cell locked-host-state" id="placeholder-cell">
								                	<div class="locked-seat-cell">
														<div class="locked-icon">
														    <span class="icon inline-icon" style="width:100%;"></span>
														</div>
													</div>
												</div>
								            </div>
								        <?php endif; ?>
						            <?php endif; ?>
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
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#chat-messages-extended" data-tab="messages" aria-controls="chat-messages-extended" role="tab" data-toggle="tab">All Messages</a>
							</li>
							<li role="presentation">
								<a href="#chat-mentions-extended" data-tab="mentions" aria-controls="chat-mentions-extended" role="tab" data-toggle="tab">Mentions <span id="mentions-count"></span></a>
							</li>
						</ul>
					</div><!-- .sidebar-header -->
					<div class="chat-message-container">
						<div class="missed-messages-container">
		                    <div id="missed-messages-badge">
		                        <span id="#missed-text"> New Messages </span> <span class="arrow"></span> 
		                    </div>
						</div>
						<!--
<div role="tabpanel" class="tab-pane active" id="messages-tab" class="chat-constrainer tab-content">
							<ul id="chat-messages-extended">
								<ul id="not-seen-mesages"></ul>
								
							</ul>
						</div>
						<div role="tabpanel" class="tab-pane" id="mentions-tab" class="chat-constrainer tab-content">
							<ul id="chat-mentions-extended">
								
							</ul>
						</div>
-->
						<div class="chat-constrainer tab-content">
							<ul role="tabpanel" class="tab-pane fade in active" id="chat-messages-extended">
								<ul id="not-seen-mesages"></ul>
								
							</ul>
							<ul role="tabpanel" class="tab-pane fade" id="chat-mentions-extended" style="display:none;">
								
							</ul>
						</div>
					</div><!-- .chat-message-container -->
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
		            </div><!-- .chat-input-container -->
				</div><!-- .sidebar.chat -->
			</div>
		    <ul class="popover nav-user-navigation-dropdown" style="right: 8px; display: none;">
		        <li class="new-aw-link"><a href="" class="create-channel" style="color:white;">Go Live NOW!</a></li>
		        <li><a id="my-profile">Profile</a></li>
		        <li><a id="user-settings">Settings</a></li>
		        <li><a id="logout">Logout</a></li>
		    </ul>
		    
		    
		    <?php else://elseif ( strtolower($user['type']) == 'trainer' ) : ?>
		    
		    <div id="index-header" style="z-index:100;"><header class="site-header">
			    <a class="header-logo" style="background-size:25% 100%;">
			      <h1><span>WOWCAST!</span></h1>
			    </a>
			    <nav class="nav-site">
			      <div>
			        <form class="search">
			          <div class="custom-search">
			            <input placeholder="Search for Cast name" id="search-query" type="text" name="search">
			            <input id="submit" type="submit" class="search-submit">
			            <div class="clear-search"></div>
			          </div>
			        </form>
			        <a class="close-phonemenu-drawer-button">
			          <svg width="15px" height="10px" viewBox="0 0 15 10" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
			            <g id="blab-feed-laptop-large" sketch:type="MSArtboardGroup" transform="translate(-20.000000, -84.000000)" fill="#9364EE">
			              <g id="filter-sidebar" sketch:type="MSLayerGroup" transform="translate(0.000000, 61.000000)">
			                <g id="close-tag-drawer" transform="translate(20.000000, 23.000000)" sketch:type="MSShapeGroup">
			                  <path d="M3,2.99066436 L3,6.99066436 L0,4.99066436 L3,2.99066436 Z M5,0 L15,0 L15,2 L5,2 L5,0 Z M5,4 L15,4 L15,6 L5,6 L5,4 Z M5,8 L15,8 L15,10 L5,10 L5,8 Z"></path>
			                </g>
			              </g>
			            </g>
			          </svg>
			        </a>
			      </div>
			    </nav>
			
			    <nav class="nav-user">
			      <ul>
			        <li class="nav-user-newaw">
			        	<?php //if ( strtolower($user['type']) == 'trainer' ) : ?>
							<a class="create-channel button aw-green-btn">Go Live NOW!</a>
			          	<?php //endif; ?>
			        </li>
			        <li class="nav-user-navigation">
			          <a class="userNavToggle">
			            <img src="<?php echo $user['twitter_data']['profile_image_url']; ?>" alt="User Navigations">
			 <!--            <p class="profile-name"></p> -->
			            <svg class="down-toggle-arrow" width="9px" height="6px" viewBox="0 0 9 6" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
			              <g id="triangle-container" sketch:type="MSArtboardGroup" transform="translate(-1401.000000, -28.000000)" fill="#475364">
			                <g id="triangle" transform="translate(1282.000000, 15.000000)" sketch:type="MSShapeGroup">
			                  <path d="M125.5925,13.973 L127.79,13.973 L123.395,18.4867838 L119,13.973 L121.1975,13.973 L123.395,16.2298919 L125.5925,13.973 Z" id="Triangle-5"></path>
			                </g>
			            </g>
			            </svg>
			          </a>
			          <ul class="popover nav-user-navigation-dropdown" style="display: none; left:auto;">
			          	
			        	<!--
<?php if ( strtolower($user['type']) == 'trainer' ) : ?>
			            <li class="new-blab-link"><a href="" class="create-channel aw-green-btn">Go Live NOW!</a></li>
			            <li><a id="my-profile">Profile</a></li>
			            <li><a id="user-settings">Settings</a></li>
			            <?php endif; ?>
-->
			            <li><a id="logout" href="<?php echo $baseurl.'logout.php'; ?>">Logout</a></li>
			          </ul>
			        </li>
			
			      </ul>
			    </nav>
			</header>
			</div>
	        
	        <div class="feed-container">
	        	<nav class="nav-wowcasts">
				    <ul id="tab-list">
				      <!--<li><a href="#all" id="all" class="selected"><span>All Blabs</span> <!--<span class="count">10</span></a></li>-->
				      <li><a href="/live" class="internal-link selected" id="live"><span>Live NOW!</span> <!--<span class="count">10</span>--></a></li>
				      <li><a href="/scheduled" class="internal-link" id="scheduled"><span>Scheduled</span> <!--<span class="count new">3</span>--></a></li>
				      <li><a href="/archive" class="internal-link" id="archive"><span>Archive</span> <!--<span class="count new">3</span>--></a></li>
				      <!--<li><a href="#explore"><span>Explore</span></a></li>-->
				    </ul>
				    <ul id="search-tab-list" class="no-height hidden">
				      <!--<li><a href="#all" id="all" class="selected"><span>All Blabs</span> <!--<span class="count">10</span></a></li>-->
				      <li><a class="internal-link selected" id="search-all"><span>All Search Results</span> <!--<span class="count">10</span>--></a></li>
				      <!--<li><a href="#explore"><span>Explore</span></a></li>-->
				    </ul>
				</nav>
				
				<div id="browse">
			        <div class="main-content">
				        <div class="feed">
					        <div id="loading-spinner" class="peeek-loading peeek-loading--index peeek-loading--dark spinner-hidden">
					          <ul>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					            <li></li>
					          </ul>
					        </div>
					        <section class="live">
						        <ul id="stream-list">
						        
						        </ul>
					        </section>
					        <section class="scheduled">
						        <ul id="scheduled-list">
						        
						        </ul>
					        </section>
					        <section class="archive">
						        <ul id="archive-list">
						        
						        </ul>
					        </section>
				        </div><!-- .feed -->
			        </div><!-- .main-content -->
		        </div><!-- .browse -->
				
	        </div><!-- .feed-container -->
	        
		    <?php endif; ?>
		</div><!-- END OF MAIN -->

    <div style="display:none;" id="modal">
        <div id="modal-create" class="modal-create">
		  <div class="close" data-dismiss="modal"></div>
		  <div class="full-modal">
		    <h1>Create a WOWCast!</h1>    
		    <form class="create-form" onkeypress="return event.keyCode != 13" method="post" enctype="multipart/form-data">
		      <ul id="create-form-wrapper">
		        <li class="create-topic active">
		          <p>What you talkin' about <?php echo explode(" ",$user['name'])[0]; ?>?</p>
		          <div class="topic-container">
		            <input class="topic-input" maxlength="80" id="rename-input" type="text" placeholder="Ex. How to drop a dress size in 28 days" width="100%" autocomplete="off">
		            <small><span id="room-char-count" class="character-count">0</span>/80</small>
		          </div>
		        </li>
		        <li id="aw-banner" class="create-category">
		          <div class="fileinput fileinput-new" data-provides="fileinput">
					  <div class="fileinput-new thumbnail">
					  	<svg xmlns="http://www.w3.org/2000/svg" width="300" height="50" viewBox="0 0 300 50" preserveAspectRatio="none"><defs><style type="text/css"><![CDATA[#holder_150f6396818 text { fill:#AAAAAA;font-weight:bold;font-family:Arial, Helvetica, Open Sans, sans-serif, monospace;font-size:15pt } ]]></style></defs><g id="holder_150f6396818"><rect width="300" height="50" fill="#EEEEEE"/><g><text x="65" y="31.6">300x50 (.jpg only!)</text></g></g></svg>
					    <!-- <img src="holder.js/300x50" alt="banner"> -->
					  </div>
					  <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 300px; max-height: 50px;"></div>
					  <div>
					    <span class="btn btn-default btn-file"><span class="fileinput-new">Upload a banner</span><span class="fileinput-exists">Change</span><input id="banner-input" type="file" name="banner" /></span>
					    <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput">Remove</a>
					  </div>
					</div>
		        </li>
		        <li id="aw-banner-type" class="create-category disabled">
		          <p>Choose a Banner tap type</p>
		          <ul>
		            <li>
		              <input id="category" type="radio" name="category" value="1">
		              <label id="label-type1" for="category" class="button ghost-button item-create-category" data-category="1">Email</label>
		            </li>
		            <li>
		              <input id="category" type="radio" name="category" value="2">
		              <label id="label-type2" for="category" class="button ghost-button item-create-category" data-category="2">Popup</label>
		            </li>
		          </ul>
		          <div id="banner-type-1" class="topic-container" style="display:none;">
		            <input class="topic-input" id="banner-mc-api" type="text" placeholder="Your MailChimp API Key" width="100%" autocomplete="off">
		            <input class="topic-input" id="banner-mc-list" type="text" placeholder="Your MailChimp List ID" width="100%" autocomplete="off">
		          </div>
		          <div id="banner-type-2" class="topic-container" style="display:none;">
		            <input class="topic-input" id="banner-url" type="text" placeholder="Ex. www.example.com" width="100%" autocomplete="off">
		          </div>
		        </li>
		        <li id="aw-guest-client" class="create-category">
		          <p>Lock Guest Seat?</p>
		          <ul>
		            <li>
		              <input id="allow-guest-client" type="radio" name="allow-guest-client" value="0">
		              <label id="label-guest-client1" for="allow-guest-client" class="button ghost-button item-guest-client" data-category="1">Yes</label>
		            </li>
		            <li>
		              <input id="allow-guest-client" type="radio" name="allow-guest-client" value="1">
		              <label id="label-guest-client2" for="allow-guest-client" class="button ghost-button item-guest-client" data-category="0">No</label>
		            </li>
		          </ul>
		        </li>
		        <li id="aw-twitter-list" class="create-category">
		          <p>Allow Twitter List</p>
		          <ul>
		            <li>
		              <input id="allow-twitter-list" type="radio" name="allow-twitter-list" value="1">
		              <label id="label-twitter-list1" for="allow-twitter-list" class="button ghost-button item-twitter-list" data-category="1">Choose</label>
		            </li>
		            <li>
		              <input id="allow-twitter-list" type="radio" name="allow-twitter-list" value="0">
		              <label id="label-twitter-list2" for="allow-twitter-list" class="button ghost-button item-twitter-list" data-category="0">Create</label>
		            </li>
		          </ul>
		          <div id="aw-twitter-list-dropdown" class="topic-container" style="display:none; margin-top:10px;">
			          	<?php
			          		if ( isset($_SESSION['aw_user']) ) {
								$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['aw_user']['access_token']['oauth_token'], $_SESSION['aw_user']['access_token']['oauth_token_secret']);
							}
							else {
								$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
							}
							//$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
							
							//- LIST CRUD FOR TWITTER
							$lists = $connection3->get("lists/list");
							if ( !empty($lists) && count($lists) > 0 ) {
								echo '<select name="twitter-lists" id="twitter-lists">';
								foreach ($lists as $list ){
									echo '<option value="'.$list->id.'">'.$list->name.'</option>';
								}
								echo '</select>';
							}
							else {
								echo 'NONE';
							}
							//$lists = $connection3->post("lists/destroy", array('list_id' => 222657501));
							//$date  = date('Ymd', time());
							//$lists = $connection3->post("lists/create", array('name' => 'WOWCast-'.$date));
			          	?>
		          </div>
		          
		          <div id="aw-twitter-list-create" class="topic-container" style="display:none; margin-top:10px;">
		          		<?php
		          			$date  = date('Ymd', time());
							$generated_tt_name = 'WOWCast-'.$date;
							echo '<input class="topic-input" id="twitter-list-new" type="text" maxlength="17" placeholder="Name of list 17 characters only" width="100%" autocomplete="off" value="'.$generated_tt_name.'">';
		          		?>
		          </div>
		        </li>
		        <li id="marketing-pixel" class="create-topic">
		          <p>Marketing Pixels</p>
		          <div class="row">
		            <div class="col-sm-12 text-center">
					  <div class="col-sm-4">
			            <a data-pixel="facebook" class="btn btn-social btn-block btn-facebook pixel-empty">
						    <span class="fa fa-facebook"></span> Facebook
						</a>
						<a data-pixel="facebook" id="remove-facebook-pixel" class="remove-pixel">Remove</a>
					  </div>
					  
					  <div class="col-sm-4">
			            <a data-pixel="twitter" class="btn btn-social btn-block btn-twitter pixel-empty">
						    <span class="fa fa-twitter"></span> Twitter
						</a>
						<a data-pixel="twitter" id="remove-twitter-pixel" class="remove-pixel">Remove</a>
					  </div>
					  
					  <div class="col-sm-4">
			            <a data-pixel="google" class="btn btn-social btn-block btn-google pixel-empty">
						    <span class="fa fa-google"></span> Google
						</a>
						<a data-pixel="google" id="remove-google-pixel" class="remove-pixel">Remove</a>
					  </div>
					</div>
		          </div>
		        </li>
		        <!--
<li id="fb-pixel" class="create-topic">
		          <p>Facebook Custom Audience Pixel</p>
		          <div class="topic-container">
		            <textarea class="form-control" rows="3"></textarea>
		          </div>
		        </li>
		        <li id="tt-pixel" class="create-topic">
		          <p>Twitter Custom Audience Pixel</p>
		          <div class="topic-container">
		            <textarea class="form-control" rows="3"></textarea>
		          </div>
		        </li>
		        <li id="ad-pixel" class="create-topic">
		          <p>Adwords Remamarketing Pixel</p>
		          <div class="topic-container">
		            <textarea class="form-control" rows="3"></textarea>
		          </div>
		        </li>
-->
		        <li class="create-time" id="create-time">
			          <p>When do you want to go live?</p>
			          <ul>
			            <li>
			              <input id="now-selector" type="radio" value="now" name="time">
			              <label id="item-create-now" class="button ghost-button item-create-time" data-time="now">Now</label>
			            </li>
			            <li>
			              <input id="scheduled-selector" type="radio" value="schedule" name="time">
			              <label id="item-create-schedule" class="button ghost-button item-create-time  item-create-schedule" data-time="schedule">In the future</label>
			            </li>
			          </ul>
			          <div class="scheduling-container hidden">
			          	<!-- <div id="calendar"></div> -->
			          	<input type="hidden" id="scheduled_date" value="" />
			            <div class="time-container">
			              <div class="time-fields">
			                <input id="hours" name="setTime" maxlength="2" pattern="[0-9]" value="02">
			                <span class="colon"> : </span>
			                <input id="minutes" name="S" maxlength="2" pattern="[0-9]" value="03">
			              </div>
			              <div class="am-pm-selector">
			                <div class="selector">
			                  <input id="am" type="radio" value="am" name="am-pm">
			                  <label for="am"> AM</label>
			                </div>
			                <div class="selector">
			                  <input id="pm" type="radio" value="pm" name="am-pm" checked="">
			                  <label for="pm"> PM </label>
			                </div>
			              </div>
			              <span>(Time is in your TimeZone)</span>
			              <ul class="days-container">
			                
			              </ul>
			            </div>
			          </div>
          		</li>
	        </ul>
	        <div class="form-submit-container">
	       
	          <button id="start-now-stream" onclick="return false;" class="button new-video-button disabled">Go Live NOW!</button>
	
	          <p class="error-message hidden"></p>
	        </div>
	      </form>
	    </div>
	  </div>
    </div>
    
    <div id="image-error-modal" class="modal fade" style="z-index:2147483647;">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title text-center">AHEM!</h4>
	      </div>
	      <div class="modal-body">
	        <p></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-success" data-dismiss="modal">Ok</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
	<div id="pixels-modal" data-pixel="" class="modal fade" style="z-index:2147483647;">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title text-center">Enter Pixel</h4>
	      </div>
	      <div class="modal-body">
	        <textarea id="facebook-pixel" class="form-control" rows="3" style="display:none;"></textarea>
	        <textarea id="twitter-pixel" class="form-control" rows="3" style="display:none;"></textarea>
	        <textarea id="google-pixel" class="form-control" rows="3" style="display:none;"></textarea>
	      </div>
	      <div class="modal-footer">
	        <button id="cancel-pixel" type="button" class="btn btn-default">Cancel</button>
	        <button id="save-pixel" type="button" class="btn btn-success disabled">Save</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
</div>
    
    <script>
    	var baseurl		= '<?php echo $baseurl; ?>';
	    var apiKey      = '<?php echo $apiKey;?>'; // Replace with your API key. See https://dashboard.tokbox.com/projects
	    var sessionId   = '<?php echo $sessionId;?>'; // Replace with your own session ID.
	    var userName    = '<?php echo $user['twitter_data']['screen_name']; ?>';
	    var profilePic 	= '<?php echo $user['pic']; ?>';
	    var userId		= '<?php echo $user['db_id']; ?>';
	    var userType	= '<?php echo $user['type']; ?>';
	    var room		= '<?php echo $room; ?>';
	    var token       = '<?php echo $token; ?>';
	    var roomName    = '<?php echo $roomData['name']; ?>';
	    var roomSlug    = '<?php echo $roomData['slug']; ?>';
	    var userData    = '<?php echo json_encode($user); ?>';
		var session;
		var roomShortUrl = '<?php echo $roomData['short_url']; ?>';
		var mentionsCount = 0;
		
		$('.nav-wowcasts ul li a.selected').parent('li').css({
			'background-color' : '#27ae60',
			'color' : 'white'
		});
	</script>
    <!-- Twitter Widget -->
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
		    
	    $('#fb-share').on('click', function(e){
	    	e.preventDefault();
			FB.ui(
			  {
			    method: 'share',
			    href: baseurl+roomSlug,
			  },
			  function(response) {
			  	console.log(response);
			    if (response && !response.error_code) {
			      //alert('Posting completed.');
			    } else {
			      //alert('Error while posting.');
			    }
			  }
			);
	    });
	</script>
	
	<script src="<?php echo $baseurl; ?>assets/js/selectize.min.js" type="text/javascript"></script>
	<script src="<?php echo $baseurl; ?>assets/js/room.js?<?php echo md5(date()); ?>"></script>
	<script src="<?php echo $baseurl; ?>assets/js/script.js?<?php echo md5(date()); ?>"></script>
	
	<?php
		if (isset($room) && !empty($room)) {
			if ($roomData['tt_pixel']) {
				$tt_pixel = stripslashes(htmlspecialchars_decode($roomData['tt_pixel']));
				echo $tt_pixel;
			}
			
			if ($roomData['ad_pixel']) {
				$ad_pixel = stripslashes(htmlspecialchars_decode($roomData['ad_pixel']));
				echo $ad_pixel;
			}
		}
	?>
</body>
</html>