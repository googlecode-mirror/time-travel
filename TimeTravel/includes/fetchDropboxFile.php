<?php
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');
require_once(dirname(dirname(__FILE__)) .'/services/DropboxService.php');
require_once(dirname(dirname(__FILE__)) .'/dao/PictureDAO.php');

error_reporting(E_ERROR | E_PARSE);
session_start();

$filePath = "";
if (isset($_GET["filename"])){
	$filePath = $_GET["filename"];
}


$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
$dropbox = new \Dropbox\API($OAuth);

$service = new DropboxService();
$pictureDAO = new PictureDAO();

if (!$pictureDAO->isPictureForUserExistingByFile($_SESSION['userid'], Util::getFileNameFromPath($filePath))){
	$service->fetchFile($dropbox, $filePath, $_SESSION['username'], $_SESSION['userid']);
}

?>