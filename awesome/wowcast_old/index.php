<?php
session_start();
//session_stop();

function session_stop () {
	// remove all session variables
	session_unset(); 
	
	// destroy the session 
	session_destroy(); 
}

require "vendor/twitteroauth/autoload.php";
require_once("database.php");

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key 			= 'aQhuFOE4PMyLe1TAy2QDZca6R';
$consumer_secret 		= 'hQnYYzhLNJ7oP84XYcb8bSOPKGoY5TAk3nAahxeONJlyCj1sel';
$access_token 			= '531108969-IjzwrJq9WwKBfQ9h6QYJ0DJspCpJsVsvOp59thpv';
$access_token_secret 	= 'RB4D8JE0mNOMEjGty1dN8fQXtzdIJ8hK04Tn6peOI6nYi';
$oauth_callback			= $baseurl.'callback.php';

//added by rene
$room_id = $_GET['id'];

$error_msg = '';

if (isset($_POST['twitter_login']) && !empty($_POST['twitter_login'])) {
	$login = $_POST['login'];
	
	if ( $login == 'aw' ) {
		$aw_name = $_POST['aw_uname'];
		$aw_pass = $_POST['aw_pass'];
		$aw_wsdl = new AW_WSDL();
		$aw_user = $aw_wsdl->aw_login_valid($aw_name,$aw_pass);
		
		/*
echo '<pre>';
		print_r($aw_user);
		echo '</pre>';die();
*/

		if ($aw_user[0] == 'valid') {
			$aw_model = new AW_Model();
			$data = array(
				"is_twitter"	=> 0,
				"twitter_id"	=> null,
				"twitter_name"	=> null,
				"name" 			=> $aw_user[1],
				"user_id" 		=> $aw_user[3],
				"name" 			=> $aw_user[1],
				"is_trainer"	=> $aw_user[5],
				"photo"			=> $aw_user[2]
			);
			
			$added_user = $aw_model->add_user($data);
	
			$_SESSION['aw_user']['id']    		= $aw_user[3];
			$_SESSION['aw_user']['name']  		= $aw_user[1];
			$_SESSION['aw_user']['pic']   		= $aw_user[2];
			$_SESSION['aw_user']['is_trainer']  = $aw_user[5];
			$_SESSION['aw_user']['db_id'] 		= $added_user['id'];

			if ( $added_user ) {
				header("Location: ".$baseurl."room.php");
			}
			
		}
		else {
			$error_msg = $aw_user[1];
		}
		
	}
	else if ( $login == 'twitter' ) {
		$connection 	= new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
		$request_token 	= $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));
		
		$_SESSION['oauth_token'] 		= $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		
		$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		
		header("Location: ".$url);
	}
}
else if ( $_SESSION['aw_user'] || $_SESSION['access_token'] ) {
	header("Location: ".$baseurl."room.php");
}

/* check session */
if($_SESSION['aw_room'] != "" or $room_id != ""){
	$aw_model = new AW_Model();
	$aw_room = $_SESSION['aw_room'];
	if($_SESSION['aw_room'] != ""){
	$sql = "select r.name,r.banner_url,r.banner_image,r.short_url,r.slug,r.schedule_time,r.is_sched,r.guest_id,r.active,
				(select SUBSTRING_INDEX(name,' ',1) from users where id= r.trainer_id LIMIT 1) as trainer_name,
				(select twitter_name from users where id= r.trainer_id LIMIT 1) as twitter_name
			from 
				rooms as r
			where slug = '$aw_room' and active != '2' LIMIT 1";
	}else{
	$sql = "select r.name,r.banner_url,r.banner_image,r.short_url,r.slug,r.schedule_time,r.is_sched,r.guest_id,r.active,
				(select SUBSTRING_INDEX(name,' ',1) from users where id= r.trainer_id LIMIT 1) as trainer_name,
				(select twitter_name from users where id= r.trainer_id LIMIT 1) as twitter_name
			from 
				rooms as r
			where id = '$room_id' and active != '2' LIMIT 1";	
	}
			$check_sched = $aw_model->conn->query($sql);
			if($check_sched->num_rows == 0){
				echo "";
			}else{
				$sch 						= $check_sched->fetch_assoc();
				if($sch['active'] == "1"){
					$title = "WATCH LIVE NOW! {$sch['name']}";
				}else{
					if($sch['is_sched'] == "1"){
						$schedule_time 	= date("M dS, Y   h.ia",strtotime($sch['schedule_time']));
						$title = "Watch LIVE on $schedule_time {$sch['name']}";
					}else{
						$title = "Upcoming Cast {$sch['name']}";
					}
				}
				$has_meta = true;
			}

}
/*
Then if live = WATCH LIVE NOW! <topic>
If scheduled = Watch LIVE on Thursday 12th December at 2.30pm <topic>
	
*/

?>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/jquery.qtip.min.css">
	<link rel="stylesheet" href="assets/css/app.css">
	<link rel="stylesheet" href="assets/css/wc.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	
	<?php
		if($has_meta){
	?>
		<title><?php echo $title; ?></title>
		<link rel="canonical" href="<?php echo $baseurl.$sch['slug'];?>">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:site" content="<?php echo $title; ?>">
		<meta name="twitter:title" content="<?php echo $title; ?>">
		<meta name="twitter:creator" content="<?php echo $sch['trainer_name'];?>">
		<meta name="twitter:description" content="Watch live conversations about topics that matter most to you">
		<meta name="twitter:image" content="http://www.tubemasterpro.com/wowcast/assets/img/wcicon.png">
		
		<meta property="og:url" content="http://www.tubemasterpro.com/wowcast/<?php echo $sch['slug'];?>">
		<meta property="og:site_name" content="<?php echo $title; ?>">
		<meta property="og:title" content="<?php echo $title; ?>">
		<meta property="og:description" content="Watch live conversations about topics that matter most to you" />
		<meta property="og:image" content="http://www.tubemasterpro.com/wowcast/assets/img/wcicon.png">
		<meta property="og:type" content="product">

	<?php
	}else{
	?>
		<title>WOW!Cast</title>
		<link rel="canonical" href="<?php echo $baseurl; ?>">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:site" content="WOW!Cast">
		<meta name="twitter:title" content="WOW!Cast">
		<meta name="twitter:creator" content="@australiawow">
		<meta name="twitter:description" content="Watch live conversations about topics that matter most to you">
		<meta name="twitter:image" content="http://www.tubemasterpro.com/wowcast/assets/img/wcicon.png">
		
		<meta property="og:url" content="http://www.tubemasterpro.com/wowcast/">
		<meta property="og:site_name" content="WOW!Cast">
		<meta property="og:title" content="WOWCast!">
		<meta property="og:description" content="Watch live conversations about topics that matter most to you" />
		<meta property="og:image" content="http://www.tubemasterpro.com/wowcast/assets/img/wcicon.png">
		<meta property="og:type" content="product">
	<?php
		}
	?>
</head> 

<body>

    <div style="z-index:1000;" id="modal"></div>
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
	</script>

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
	<script>
	var error_msg = "<?php echo $error_msg; ?>";
	var baseurl   = "<?php echo $baseurl; ?>";
	$(document).ready(function() {
		if (error_msg != '') {
			alert(error_msg);
		}
		
		var html = '<div id="modal-signup" class="fade-modal faded-out">' +
	    //'<div class="close"></div>' +
	    '<div class="full-modal">' +
	    '<div class="table-cell">' +
	    '<div class="container">' +
		'<div class="signup">' +
		'<form id="login-form" method="post" action="'+baseurl+'">' +
		'<input type="hidden" name="twitter_login" value="true" />' +
		'<h1> Sign in to join WOWCAST! </h1>' +
		'<div class="signin-logo" style="background-image:url(assets/img/wowcast-logo.png);"></div>' +
		'<div id="step-1">' +
		//'<button id="aw-login" type="submit" class="twitter-login"> Sign up With AW! ' +
		//'<span style=""></span> </button><br>' +
		'<button id="twitter-login" type="submit" class="twitter-login" name="login" value="twitter"> Sign up With Twitter ' +
		'<span style="background-image:url(assets/img/twitter_blue.png)!important;"></span> </button>' +
		'</div>' +
		'<div id="step-2" style="display:none;" class="create-form">' +
		'<input autocomplete="off" type="text" id="aw_uname" name="aw_uname" placeholder="AW! Username" class="topic-input" />' +
		'<input autocomplete="off" type="password" id="aw_pass" name="aw_pass" placeholder="AW! Password" class="topic-input" /><br>' +
		'<button id="aw-login-submit" type="submit" class="twitter-login btn disabled" name="login" value="aw"> Login </button>' +
		'<a href="#" id="login-back">Back</a>' +
		'</div>' +
		//'<a class="skip-modal"> Nah, I\'ll keep lurking </a>' +
		'</form>' +
		'</div>' +
		'</div>' +
		'</div>' +
	    '</div>' +
	    '<div class="overlay twitterColor"></div>' +
		'</div>';
		
		$('#modal').html(html);
    	$('#modal').show();
    	
    	$('#aw-login').on('click', function(e){
	    	e.preventDefault();
	    	$('#step-1').hide();
	    	$('#step-2').show();
    	});
    	
    	$('#login-back').on('click', function(e){
	    	e.preventDefault();
	    	$('#step-2').hide();
	    	$('#step-1').show();
    	});
    	
    	$('#login-form').on('submit', function(){
		    $('#aw-login-submit').addClass('disabled');
    	});
    	
    	$('#aw_uname').on('keyup', function(e){
	    	e.preventDefault();
	    	var email = $.trim($(this).val());
	    	if ( isValidEmail(email) && $('#aw_pass').val().length > 0 ) {
		    	$('#aw-login-submit').removeClass('disabled');
	    	}
	    	else {
		    	$('#aw-login-submit').addClass('disabled');
	    	}
    	});
    	
    	$('#aw_pass').on('keyup', function(e){
	    	e.preventDefault();
	    	var email = $.trim($('#aw_uname').val());
	    	var pass  = $.trim($(this).val());
	    	if ( isValidEmail(email) && pass.length > 0 ) {
		    	$('#aw-login-submit').removeClass('disabled');
	    	}
	    	else {
		    	$('#aw-login-submit').addClass('disabled');
	    	}
    	});
    	
    	/* ==========================================
		   FUNCTION FOR EMAIL ADDRESS VALIDATION
		============================================= */
		function isValidEmail(emailAddress) {
		
		    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		
		    return pattern.test(emailAddress);
		
		}
    	
    });
	</script>
        
</body>
</html>