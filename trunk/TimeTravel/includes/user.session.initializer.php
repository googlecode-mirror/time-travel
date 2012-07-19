<?php

	error_reporting(E_ERROR | E_PARSE);
	session_start();
	$userid = "";
	$loggedIn = false;
	$name = null;

	if (isset($_SESSION["userid"])) {
		$loggedIn = true;
		$userid = $_SESSION["userid"];
		$name = $_SESSION['name'];
		$username = $_SESSION['username'];
		error_log("user logged in :".$name);
		//echo "user logged.";
	} else{
		error_log("no user logged.");
		//echo "no user logged.";
	}

?>