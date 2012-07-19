<?php
require_once( dirname(__FILE__) .'/dao/PictureDAO.php');
require_once(dirname(__FILE__). '/viewbean/Picture.php');
require_once(dirname(__FILE__). '/upload.class.php');
date_default_timezone_set('Europe/Minsk');

error_reporting(E_ALL | E_STRICT);
$upload_handler = new UploadHandler();

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

switch ($_SERVER['REQUEST_METHOD']) {
	case 'OPTIONS':
		break;
	case 'HEAD':
	case 'GET':
		$upload_handler->get();
		break;
	case 'POST':
		if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
			$upload_handler->delete();
		} else {
			// $upload_handler->post();
		}
		break;
	case 'DELETE':
		$upload_handler->delete();
		break;
	default:
		header('HTTP/1.1 405 Method Not Allowed');
}
//phpinfo();

$filenames = glob("server/php/files/*.{jpg,gif,png,JPG,GIF,PNG}", GLOB_BRACE);


foreach ($filenames as $filepath) {
	set_time_limit(60);
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
				$rawTimeTaken = $exif_exif['DateTimeOriginal'];
				error_log("EXIF TIME TAKEN : ".$rawTimeTaken);
				$timetaken = date("Y-m-d H:i:s", strtotime($rawTimeTaken));
			} catch (Exception $e){
		
			}
		} else	if (isset($exif_ifd0['DateTime'])){
			//We get the date and time the picture was taken
			try {
				$rawTimeTaken = $exif_ifd0['DateTime'];
				error_log("IFDO TIME TAKEN : ".$rawTimeTaken);
				$timetaken = date("Y-m-d H:i:s", strtotime($rawTimeTaken));
			} catch (Exception $e){

			}
		}  else  {
			$rawTimeTaken = $exif_file['FileDateTime'];
			$timetaken = date("Y-m-d H:i:s", $rawTimeTaken);
			
		}

		error_log("TIME TAKEN : ".$timetaken);

		$fileType = $exif_file['MimeType'];

		
		$exif = read_exif_data($filepath, 'EXIF', 0);
		/*var_dump($exif);
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
			case ($currentFileSize > 3000000): $quality = 7;
			break;

			case ($currentFileSize > 2000000): $quality = 14;
			break;

			case ($currentFileSize > 1000000): $quality = 21;
			break;

			case ($currentFileSize > 500000): $quality = 50;
			break;
		}

		imagejpeg($img, $filepath, $quality);
		imagedestroy($img);


		$payload = file_get_contents($filepath);
		$picture = new Picture(0, "", "", $timetaken, $fileType, $payload, $filename);
		$picture->latitude = "";
		$picture->longitude = "";
		$dao = new PictureDAO();

		error_reporting(E_ERROR | E_PARSE);
		session_start();
		
		if (isset($_SESSION['name'])) {
			$userid = $_SESSION["userid"];
			$dayId = $dao->createDay($userid, $timetaken);
			$dao->savePicture($dayId, $picture);
		} else {
			error_log("COULD NOT SAVE PICTURES, NO USER IN SESSION!!!");
		}
		
		
		//unlink($filepath);

	} catch (Exception $e){
		error_log("Exception!!! --- ".$e);
		unlink($filepath);
	}

}


header("Location: index.php");
?>