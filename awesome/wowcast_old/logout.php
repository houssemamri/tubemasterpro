<?php
session_start();
require_once("database.php");

function session_stop () {
	// remove all session variables
	session_unset(); 
	
	// destroy the session 
	session_destroy(); 
}

session_stop();
header("Location: ".$baseurl);

?>