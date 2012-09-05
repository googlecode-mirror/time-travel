<?php

require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');

class DropboxService {


	public function fetchFolders($parameters, $dropbox){
		$accountInfo = $dropbox->metaData();
		$folderList = $accountInfo["body"]->{"contents"};
		return $folderList;
	}


	function readDirs($main, $dropbox){
		$mainDir = $dropbox->metaData($main);
		
		$subDirList = $mainDir['body']->{"contents"};
		
		$numOfSubDir = sizeof($subDirList);
		
		for ($i = 0; $i < $numOfSubDir; $i++) {
			$folder = $subDirList[$i];
			
			if ($folder->{'is_dir'}){
				$this->readDirs($folder->{'path'}, $dropbox);
			} else {
				echo 'file: '.$folder->{'path'}.'<br/>';
			}
			

		}
		
	}

}



?>