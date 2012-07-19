<?php
date_default_timezone_set('Europe/Minsk');

class FileUtil{

	public function saveFileToDB($filepath){
		 
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
			 
			 
			if (isset($exif_ifd0)){
				//We get the date and time the picture was taken
				try {
					$rawTimeTaken = $exif_ifd0['DateTime'];
					error_log("IFDO TIME TAKEN : ".$rawTimeTaken);
					$timetaken = date("Y-m-d H:i:s", strtotime($rawTimeTaken));
				} catch (Exception $e){
					error_log($e->getTraceAsString());
				}
			} else {
				$rawTimeTaken = $exif_file['FileDateTime'];
				$timetaken = date("Y-m-d H:i:s", $rawTimeTaken);
	
			}
			 
			error_log("TIME TAKEN : ".$timetaken);
			 
			$fileType = $exif_file['MimeType'];
			 
			/* $exif = exif_read_data($filepath, 'IFD0');
			 foreach ($exif as $key => $section) {
			foreach ($section as $name => $val) {
			error_log("$key.$name: $val");
			}
			} */
			 
			 
			 
			//Reduce file size
			$img = imagecreatefromjpeg($filepath);
			 
			$quality = 100;
			$currentFileSize = filesize($filepath);
			 
			switch ($currentFileSize){
				case ($currentFileSize > 3500000): $quality = 15;
				break;
	
				case ($currentFileSize > 2000000): $quality = 30;
				break;
	
				case ($currentFileSize > 1000000): $quality = 60;
				break;
	
				case ($currentFileSize > 500000): $quality = 70;
				break;
			}
			 
			imagejpeg($img, $filepath, $quality);
			imagedestroy($img);
			 
			 
			$payload = file_get_contents($filepath);
			/*$picture = new Picture(0, "", "", $timetaken, $fileType, $payload, $filename);
			$picture->latitude = "";
			 $picture->longitude = "";
			$dao = new PictureDAO();
			 
			 
			$dayId = $dao->createDay(1, $timetaken);
			$dao->savePicture($dayId, $picture);
			unlink($filepath); */
			 
		} catch (Exception $e){
			error_log("Exception!!! --- ".$e->getTraceAsString());
			//unlink($filepath);
		}
	}
}

?>