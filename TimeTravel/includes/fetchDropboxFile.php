<?php
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');
require_once(dirname(dirname(__FILE__)) .'/services/DropboxService.php');

$filePath = "";
if (isset($_GET["filename"])){
	$filePath = $_GET["filename"];
}


$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
$dropbox = new \Dropbox\API($OAuth);

$service = new DropboxService();
$service->fetchFile($dropbox, $filePath, $_SESSION['username']);
?>