<?php

	//error_reporting(E_ERROR | E_PARSE);
	if (session_start()){
		
	}

	if (isset($_SESSION["name"])) {
		$loggedIn = true;
		$userid = $_SESSION["userid"];
		$name = $_SESSION['name'];
		$username = $_SESSION['username'];
		error_log("user logged in :".$name);
		//echo "USERID: ".$_SESSION["name"]. " length ". SID;
	} else{
		//error_log("no user logged.");
		//echo "no user logged.";
	}

	
?>