<?php
error_reporting(E_ERROR | E_PARSE);

if(session_start()){
	if (!isset($_SESSION['userid'])){
		header( 'Location: /index.php' );
	}

} else {
	header( 'Location: /index.php' );
}
?>