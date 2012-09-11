<?php
require_once('bootstrap.php');
require_once ('/services/DropboxService.php');


// Retrieve the account information
$accountInfo = $dropbox->metaData();

// Dump the output
//var_dump($accountInfo["body"]->{"contents"});


$folderList = $accountInfo["body"]->{"contents"};

$numOfFolders = sizeof($folderList);


for ($i = 0; $i < $numOfFolders; $i++) {
	echo $folderList[$i]->{'path'}."<br/>";
}




//var_dump($numOfFolders);