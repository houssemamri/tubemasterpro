<?php
require "vendor/twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key 			= 'aQhuFOE4PMyLe1TAy2QDZca6R';
$consumer_secret 		= 'hQnYYzhLNJ7oP84XYcb8bSOPKGoY5TAk3nAahxeONJlyCj1sel';
$access_token_key 		= '531108969-IjzwrJq9WwKBfQ9h6QYJ0DJspCpJsVsvOp59thpv';
$access_token_secret 	= 'RB4D8JE0mNOMEjGty1dN8fQXtzdIJ8hK04Tn6peOI6nYi';

$connection3  = new TwitterOAuth($consumer_key, $consumer_secret, $access_token_key, $access_token_secret);
$lists = $connection3->get("lists/list", array('user_id' => 531108969));
echo '<pre>';
print_r($_SESSION['oauth_token']);
echo '</pre>';
//$lists = $connection3->post("lists/create", array('name' => $post['twitter_list_name']));
?>