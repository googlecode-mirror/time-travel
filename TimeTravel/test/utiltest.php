<?php
require_once(dirname(dirname(__FILE__)) .'/util.php');
require_once(dirname(dirname(__FILE__)) .'/services/DropboxService.php');
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');

$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
$dropbox = new \Dropbox\API($OAuth);

$service = new DropboxService();

$parameters = array();

$folderList = $service->fetchFolders($parameters, $dropbox);
$numOfFolders = sizeof($folderList);

$service->readDirs('/Software', $dropbox);

/* for ($i = 0; $i < $numOfFolders; $i++) {
	$folder = $folderList[$i]->{'path'};
	$service->readDirs($folder, $dropbox);
} */

?>