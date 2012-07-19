<?php
error_reporting(E_ERROR | E_PARSE);

if(session_start()){
	if (isset($_SESSION['username'])){
		$userid = $_SESSION["userid"];
		error_log("session: ".$_SESSION['username']);
	} else {
		header( 'Location: /index.php' );
	}

} else {
	header( 'Location: /index.php' );
}
?>