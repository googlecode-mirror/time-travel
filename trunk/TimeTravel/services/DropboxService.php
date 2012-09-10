<?php
require_once(dirname(dirname(__FILE__)) .'/util.php');
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/DropboxFile.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/Picture.php');
require_once(dirname(dirname(__FILE__)) .'/dao/PictureDAO.php');
date_default_timezone_set('Africa/Johannesburg');

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


	function readDirContents($main, $dropbox){
		$fileList = array();
		
		if ($main == "root"){
			$mainDir = $dropbox->metaData();
		} else {
			$mainDir = $dropbox->metaData($main);
		}
		
		$subDirList = $mainDir['body']->{"contents"};
		$numOfSubDir = sizeof($subDirList);

		for ($i = 0; $i < $numOfSubDir; $i++) {
			$folder = $subDirList[$i];
			$is_dir = false;
			$name = $folder->{'path'};

			if ($folder->{'is_dir'}){
				$is_dir = true;
			}
			
			array_push($fileList, new DropboxFile($is_dir, "", $name));
		}
		
		return $fileList;
	}
	
	
	function fetchFile($dropbox, $filename, $username, $userid){
		$temp = (dirname(dirname(__FILE__))) . '/pictures/'. $username .'/temp/' . Util::getFileNameFromPath($filename);

		$pic = $dropbox->getFile($filename);
		
		file_put_contents($temp, $pic['data']);
		$this->saveFileToDB($temp, $userid);
		
		unset($pic);
		
		$thumbnail = (dirname(dirname(__FILE__))) . '/pictures/'. $username .'/thumbnails/' . Util::getFileNameFromPath($filename);
		$optimized = (dirname(dirname(__FILE__))) . '/pictures/'. $username .'/optimized/' . Util::getFileNameFromPath($filename);
		$main = (dirname(dirname(__FILE__))) . '/pictures/'. $username .'/main/' . Util::getFileNameFromPath($filename);
		
		$resizer = new ImageResizer($temp);
		
		$resizer->resize(80, $thumbnail);
		$resizer->resize(460, $optimized);
		$resizer->resize(800, $main);
		unlink($temp);
		unset($resizer);
	}
	
	
	
	public function saveFileToDB($filepath, $userid){
		 
		ini_set('exif.encode_unicode', 'UTF-8');
		error_log($filepath);
		try {
			 
			try {
				$exif_ifd0 = read_exif_data($filepath ,'IFD0' ,0);
				$exif_exif = read_exif_data($filepath ,'EXIF' ,0);
				$exif_file = read_exif_data($filepath ,'FILE' ,0);
			} catch (Exception $e){
	
			}
	
			$timetaken = null;
			 
			$filename = $exif_file['FileName'];
	
			if (isset($exif_exif['DateTimeOriginal'])){
				//We get the date and time the picture was taken
				try {
					$exif_date = $exif_exif['DateTimeOriginal'];
					error_log("EXIF TIME TAKEN : ".$exif_date);
					$exif_timetaken = date("Y-m-d H:i:s", strtotime($exif_date));
				} catch (Exception $e){
					error_log($e->getMessage());
				}
			}
	
			if (isset($exif_ifd0['DateTime'])){
				//We get the date and time the picture was taken
				try {
					$ifd0_date = $exif_ifd0['DateTime'];
					error_log("IFDO TIME TAKEN : ".$ifd0_date);
					$ifd0_timetaken = date("Y-m-d H:i:s", strtotime($ifd0_date));
				} catch (Exception $e){
					error_log($e->getMessage());
				}
			}
	
			//We chose the earliest date of the 2
			if (isset($exif_date) && (isset($ifd0_date))){
				$rawTimeTaken = ($exif_date < $ifd0_date) ? $exif_date : $ifd0_date;
				error_log("FINAL TIME TAKEN : ".$rawTimeTaken);
				$timetaken = date("Y-m-d H:i:s", strtotime($rawTimeTaken));
			} else if (isset($exif_date)){
				$rawTimeTaken = $exif_date;
				error_log("FINAL TIME TAKEN : ".$rawTimeTaken);
				$timetaken = date("Y-m-d H:i:s", strtotime($rawTimeTaken));
			} else if (isset($ifd0_date)){
	
				$rawTimeTaken = $ifd0_date;
				error_log("FINAL TIME TAKEN : ".$rawTimeTaken);
				$timetaken = date("Y-m-d H:i:s", strtotime($rawTimeTaken));
			}else  {
				$rawTimeTaken = $exif_file['FileDateTime'];
				$timetaken = date("Y-m-d H:i:s", $rawTimeTaken);
	
			}
			 
			 
			error_log("TIME TAKEN : ".$timetaken);
			 
			$fileType = $exif_file['MimeType'];
		 
			 
			//$payload = file_get_contents($filepath);
			$picture = new Picture(0, "", "", $timetaken, $fileType, null, $filename);
			$picture->latitude = "";
			$picture->longitude = "";
			$dao = new PictureDAO();
			 

			$dayId = $dao->createDay($userid, $timetaken);
			$dao->savePicture($dayId, $picture);
			error_log("File saved successfully.");
			//unlink($filepath);
			 
		} catch (Exception $e3){
			print "Error!: " . $e3->getMessage() . "<br/>";
			//unlink($filepath);
		}
	}
}



?>