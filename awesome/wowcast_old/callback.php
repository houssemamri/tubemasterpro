<?php
error_reporting(E_ALL);
session_start();
require "vendor/twitteroauth/autoload.php";
require_once("database.php");

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key 			= 'aQhuFOE4PMyLe1TAy2QDZca6R';
$consumer_secret 		= 'hQnYYzhLNJ7oP84XYcb8bSOPKGoY5TAk3nAahxeONJlyCj1sel';
$oauth_callback			= $baseurl.'callback.php';

$request_token = [];
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
    // Abort! Something is wrong.
    header("Location: ".$baseurl."");
}

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
/*
echo '<pre>';
print_r($access_token);
echo '</pre>';die();
*/
if (isset($access_token)) {
	$aw_model = new AW_Model();
	if (isset($_SESSION['aw_user'])) {
		$_SESSION['aw_user']['access_token'] = $access_token;
		
		//- Get User Deets
		$connection2  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$ttuser = $connection2->get("users/show", array("screen_name" => $_SESSION['aw_user']['access_token']['screen_name']));
		$_SESSION['aw_user']['access_token']['user'] = $ttuser;
		
		$data = array(
			"twitter_id"	=> $ttuser->id,
			"twitter_name"	=> $ttuser->screen_name,
			"id" 		    => $_SESSION['aw_user']['db_id']
		);
		
		if ( $aw_model->update_user_twitter($data) ) {
			header("Location: ".$baseurl."room.php");
		}
		
	}
	else {
		$_SESSION['access_token'] = $access_token;
		//- Create user if not found in database
		$data = array(
			"is_twitter"	=> 1,
			"twitter_id"	=> $access_token["user_id"],
			"twitter_name" 	=> $access_token["screen_name"],
			"user_id" 		=> $access_token["user_id"],
			"name" 			=> $access_token["screen_name"],
			"is_trainer"	=> 0,
			"photo"			=> ""
		);
		
		$added_user = $aw_model->add_user($data);
		
		if ( $added_user ) {
			$_SESSION['access_token']['id'] = $added_user;
	    	header("Location: ".$baseurl.$_SESSION['aw_room']);
		}
		else {
			header("Location: ".$baseurl."");
		}
	}
}
else {
	echo 'Authentication Error!';
}