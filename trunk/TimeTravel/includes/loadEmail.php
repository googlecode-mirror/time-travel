<?php
require_once(dirname(dirname(__FILE__)) . '/dao/GmailDAO.php');

if (isset($_GET["emailid"])){
	$commId = $_GET["emailid"];

	$gmailDAO = new GmailDAO();
	$email = $gmailDAO->getCommunicationById($commId);
	echo $email->body;
}
?>
