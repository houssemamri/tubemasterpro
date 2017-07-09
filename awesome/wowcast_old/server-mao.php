<?php
session_start();
error_reporting(E_ALL);
$p = $_GET['p'];

$loader = require __DIR__ . "/vendor/autoload.php";
$loader->addPsr4('OpenTok\\', __DIR__.'/OpenTok');


use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use OpenTok\Session;
use OpenTok\Role;

$apiKey = "45328482";
$apiSecret = "ac0959659492bd35358e17ad85d7d398dc4e1c5e";
$session_generated = "2_MX40NTMyODQ4Mn5-MTQ0MjgwMTM0OTAxOH4zUjlPdDRrKy9YenRsazhZOFFMNTRaMU9-fg";

$token_publisher  = "T1==cGFydG5lcl9pZD00NTMyODQ4MiZzaWc9MjYzZmJmMjc3N2NkMGI4MmY4ZjhjMzM1YjhhYmQ4NzRmZjA0NDliODpzZXNzaW9uX2lkPTFfTVg0ME5UTXlPRFE0TW41LU1UUTBNVEUzTlRneU1ETTJNMzVVVW1wSmVsVklUVVo1Y1VkcVNHUjJTSFl2ZW5SeGFVMS1mZyZjcmVhdGVfdGltZT0xNDQxMTc1ODQ3JnJvbGU9cHVibGlzaGVyJm5vbmNlPTE0NDExNzU4NDcuNzIxNTk3MDIzNTI4JmV4cGlyZV90aW1lPTE0NDE3ODA2NDcmY29ubmVjdGlvbl9kYXRhPW5hbWUlM0ROYXRoYW4=";

$opentok = new OpenTok($apiKey, $apiSecret);

if($p == "generate_session"){
// Create a session that attempts to use peer-to-peer streaming:
$session = $opentok->createSession();

// A session that uses the OpenTok Media Router:
$session = $opentok->createSession(array( 'mediaMode' => MediaMode::ROUTED ));

// Store this sessionId in the database for later use
$sessionId = $session->getSessionId();
echo "session: $sessionId";
}


if($p == "generate_token_publisher"){

	// Generate a Token from just a sessionId (fetched from a database)
	//$token = $opentok->generateToken($session_generated);
	// Generate a Token by calling the method on the Session (returned from createSession)
	// Set some options in a token
	$token = $opentok->generateToken($session_generated, array(
	    'role'       => Role::PUBLISHER,
	    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
	    'data'       => 'name=Nathan'
	));
	echo $token;
}

if($p == "generate_token_subscriber"){

	// Generate a Token from just a sessionId (fetched from a database)
	//$token = $opentok->generateToken($session_generated);
	// Generate a Token by calling the method on the Session (returned from createSession)
	// Set some options in a token
	$token = $opentok->generateToken($session_generated, array(
	    'role'       => Role::SUBSCRIBER
	));
	echo $token;
}

if($p == "generate_token_subscriber_mobile"){

	// Generate a Token from just a sessionId (fetched from a database)
	//$token = $opentok->generateToken($session_generated);
	// Generate a Token by calling the method on the Session (returned from createSession)
	// Set some options in a token
	$generated_name = "user" . rand(0000,9999);
	$token = $opentok->generateToken($session_generated, array(
	    'role'       => Role::SUBSCRIBER,
	    'data'		=>"Client|$generated_name|http://www.tubemasterpro.com/tokbox/assets/img/users/nathan.jpg"
	));
	$return['name'] = $generated_name;
	$return['token'] = $token;
	$return['room_id'] = $session_generated;
	echo json_encode($return);
}

//date = "Trainer | Trainer Name | POPUP or Email | URL PAGE | Trainer / Guest ID | Banner Url Link
if($p == "generate_publisher_as_trainer"){
		$token = $opentok->generateToken($session_generated, array(
	    'role'       => Role::PUBLISHER,
	    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
	    'data'       => 'Trainer|Nathan|email|http://www.google.com|0|http://www.tubemasterpro.com/tokbox/assets/app/banner01.jpg'
	));
	echo $token;
}
if($p == "generate_publisher_as_trainer_popup"){
		$token = $opentok->generateToken($session_generated, array(
	    'role'       => Role::PUBLISHER,
	    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
	    'data'       => 'Trainer|Nathan - Trainer|popup|http://www.google.com|0|http://www.tubemasterpro.com/tokbox/assets/app/banner02.jpg'
	));
	echo $token;
}

if($p == "generate_publisher_as_guest"){
		$token = $opentok->generateToken($session_generated, array(
	    'role'       => Role::PUBLISHER,
	    'expireTime' => time()+(7 * 24 * 60 * 60), // in one week
	    'data'       => 'Guest|Nathan - Guest|mail|http://www.google.com|1|'
	));
	echo $token;
}

if($p == "generate_publisher"){
	$token = $opentok->generateToken($session_generated);
	echo $token;
}


if($p == "submit_email"){


	$api_key = "ab5107262a4cb263b8aad742e2ff4d30-us5";
	$list_id = "3d1f460589";
	$email 	= htmlentities($_POST['email']);
	
	require('assets/chimp/Mailchimp.php');
	$Mailchimp = new Mailchimp( $api_key );
	$Mailchimp_Lists = new Mailchimp_Lists( $Mailchimp );
	
	if($email != ""){
	$subscriber = $Mailchimp_Lists->subscribe( $list_id, array( 'email' => $email ) );
		if($subscriber != "" )
		{
			if ( ! empty( $subscriber['leid'] ) ) {
				echo "success";
			}
			else
			{
			    echo "fail";
				}
		}else{
			echo "fail";
		}
	}else{
		echo "fail";
	}
}
?>