<?php
require_once(dirname(dirname(__FILE__)) . '/services/securityServices.php');
require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/LocationDAO.php');
require_once(dirname(dirname(__FILE__)) . '/errorCodes.php');
require_once(dirname(dirname(__FILE__)) . '/dto/Action.php');
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/Logger.php');
require_once(dirname(dirname(__FILE__)) .'/services/Forker.php');
date_default_timezone_set('Africa/Johannesburg');

class BusinessLogic{
	private static $responder;
	private static $locationDAO;
	private static $userDAO;
	private static $pictureDAO;
	private static $dayDAO;
	private static $securityServices;

	function __construct() {
		//session_start();
		self::$responder = new Responder;
		self::$locationDAO = new LocationDAO();
		self::$userDAO = new UserDAO();
		self::$pictureDAO = new PictureDAO();
		self::$dayDAO = new DayDAO();
		self::$securityServices = new SecurityService();
	}
	

	public function sharePicturesToOtherUser($parameters){
		$pictureArray = split(",", $parameters["picturelist"]);
		$shareToId = $parameters["shareToId"];
		foreach ($pictureArray as $pictureId){
			if (trim($pictureId) == "") continue;
			$picture = self::$pictureDAO->getPictureById($pictureId);
			try {
				self::$pictureDAO->createDay($shareToId, $picture->timetaken);
				self::$pictureDAO->sharePictureToOtherUser($_SESSION["userid"], $shareToId, $pictureId);
			} catch (Exception $e){}
		}
		//TODO We send the shareTo User an email with a link
	}
	
	
	public function recordMyLocation($parameters){
		$user = self::$securityServices->getUserByUsername($parameters["username"]);
		
		$timestamp = $parameters["timestamp"];
		$timestamp = date("Y-m-d H:i:s", ($timestamp/1000));
		$dayId = self::$pictureDAO->createDay($user->id, $timestamp);
		
		self::$locationDAO->saveLocation($dayId, $timestamp, $parameters["longitude"], $parameters["latitude"]);
	}
	

	/*Moves the pictures for user from the temp folder to the main*/
	public function moveUserPictures($parameters){
		try{
			if (isset($_SESSION['username'])){
				$username = $_SESSION["username"];
				error_log("username: ". $username);
				$rootDir = dirname(dirname(__FILE__)) .'/pictures/'. $username;

				$filenames = glob($rootDir ."/temp/*.{jpg,gif,png,JPG,GIF,PNG}", GLOB_BRACE);

					
				foreach ($filenames as $filepath) {
					set_time_limit(20);
					$dest = $rootDir .'/main/'.basename($filepath);
					if (!copy($filepath, $dest)){
						throw new Exception("026");
					}

					//we delete the temp
					unlink($filepath);

					//we delete the thumbnail
					unlink($rootDir.'/thumbnails/'.basename($filepath));
				}


			} else {
				throw new Exception("026");
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('032');
		}
		$responder = new Responder;
		return $responder->constructResponse(null);
	}

	public function saveTokenToSession($parameters){
		try {
			$accessToken = $parameters["access_token"];
			$_SESSION["access_token"] = $accessToken;

			if (!isset($_SESSION["nextState"])){
				$_SESSION["nextState"] = $state = "saveFbToken";
			} else {
				$state = $_SESSION["nextState"];
			}

			Logger::log("STATE: ".$state);

			switch ($state){
				case "saveFbToken": self::$userDAO->saveFbToken($_SESSION["userid"], $accessToken);
				$_SESSION["nextState"] = "updateFbDetails";
				return self::$responder->constructLoopResponse("Facebook token saved successfully.");

				case "updateFbDetails": $this->updateFbDetails($accessToken);
				$_SESSION["nextState"] = "retrieveAndSaveAllStatusUpdatesFromFb";
				return self::$responder->constructLoopResponse("User details saved successfully.");

				case "retrieveAndSaveAllStatusUpdatesFromFb": $done = $this->retrieveAndSaveAllStatusUpdatesFromFb($accessToken);
					
				if ($done){
					unset($_SESSION["nextState"]);
					unset($_SESSION["fbUrl"]);
					return self::$responder->constructResponse(null);
				} else{
					return self::$responder->constructLoopResponse("Status updates saved successfully.");
				}
			}

		} catch (Exception $e){
			unset($_SESSION["nextState"]);
			unset($_SESSION["fbUrl"]);
			throw new Exception($e->getMessage());
		}

		return self::$responder->constructResponse(null);
	}


	private function retrieveAndSaveAllStatusUpdatesFromFb($accessToken){
		error_log("in retrieveAndSaveAllStatusUpdatesFromFb...");

		set_time_limit(0);
		error_log("fetching statuses from fb...");
			
			
		if (!isset($_SESSION["fbUrl"])){
			$_SESSION["fbUrl"] = $url = "https://graph.facebook.com/me/statuses?access_token=".  $accessToken;
		} else {
			$url = $_SESSION["fbUrl"];
		}
			
			
		$response = file_get_contents($url);
			
		$statuses = json_decode($response);
		$statusesArray = $statuses->{'data'};
		$userid = $_SESSION["userid"];

		$this->saveFbStatusUpdates($statusesArray, $userid);

		/* 		$userDAO = new UserDAO();
		 $pictureDAO = new PictureDAO();
		error_log("Reading the Status Array...");
		$count = 0;
		foreach ($statusesArray as $status){
		set_time_limit(20);

		$gmtTimezone = new DateTimeZone('Africa/Johannesburg');
		$myDateTime = new DateTime($status->{'updated_time'}, $gmtTimezone);

		$theDate = date('c', $myDateTime->format('U') + 1);
		$theDate = date('Y-m-d H:i:s', strtotime($theDate));

		$message = $status->{'message'};
		$messageId = $status->{'id'};

		$dayId = $pictureDAO->createDay($userid, $theDate);
		$userDAO->saveStatusUpdate($userid, $theDate, $message, $messageId, $dayId);
		} */


		if (isset($statuses->paging->{'next'})){
			$_SESSION["fbUrl"] = $statuses->paging->{'next'};
			return false;
		} else {
			return true;
		}

	}

	private function saveFbStatusUpdates($statusesArray, $userid){
		Logger::log("saving ffb statuses...");
		$count = 0;
		foreach ($statusesArray as $status){
			set_time_limit(20);

			$gmtTimezone = new DateTimeZone('Africa/Johannesburg');
			$myDateTime = new DateTime($status->{'updated_time'}, $gmtTimezone);

			$theDate = date('c', $myDateTime->format('U') + 1);
			$theDate = date('Y-m-d H:i:s', strtotime($theDate));

			$message = $status->{'message'};
			$messageId = $status->{'id'};

			$dayId = self::$pictureDAO->createDay($userid, $theDate);
			self::$userDAO->saveStatusUpdate($userid, $theDate, $message, $messageId, $dayId);
		}
	}

	private function updateFbDetails($accessToken){
		$response = file_get_contents("https://graph.facebook.com/me?access_token=".  $accessToken);
		error_log("FB USER INFO: ".$response);
		$fbuser = json_decode(str_replace('\"','"',$response));
			
		self::$securityServices = new SecurityService;
		$user = self::$securityServices->getUserById($_SESSION["userid"]);

		error_log("userid: ". $_SESSION["userid"]);

		$birthday = null;
		if (isset($fbuser->{'birthday'})){
			$birthday = $fbuser->{'birthday'};
			$birthday = date("m/d/Y", strtotime($birthday));
			$birthday = date("Y-m-d", strtotime ($birthday));
		}

		$gender = null;
		if (isset($fbuser->{'gender'})){
			$gender = $fbuser->{'gender'};
		}

		$location = null;
		if (isset($fbuser->{'location'}->{'name'})){
			$location = $fbuser->{'location'}->{'name'};
		}

		$work = null;
		if (isset($fbuser->{'work'}[0]->{'employer'}->{'name'})){
			$work = $fbuser->{'work'}[0]->{'employer'}->{'name'};
		}


		$timezone = null;
		if (isset($fbuser->{'timezone'})){
			$timezone = $fbuser->{'timezone'};
		}


		error_log("birthday : ". $birthday);

		self::$securityServices->updateUserDetails($user->id, $user->name, $user->surname, $user->username, $user->email, $gender, $user->cellphone, $location, $work, $birthday, $timezone);

	}

	public function updatePictureCaption($parameters){

		error_log("in updatePictureCaption");

		$pictureId = $parameters["pictureId"];
		$caption = $parameters["caption"];
		$timetaken = $parameters["dateandtime"];
			
		if (isset($timetaken) && ($timetaken != "")){
			error_log("timetaken is set: ".$timetaken);
			$dayid = self::$pictureDAO->createDay($_SESSION["userid"], $timetaken);

			$picture = self::$pictureDAO->getPictureById($pictureId);

			self::$pictureDAO->updatePictureTimeTaken($pictureId, $dayid, $timetaken);

			//we need to delete the day that the picture fell in if it's not used anymore
			
			self::$dayDAO->deleteDayIfUnused($picture->dayId);

		} else if (isset($caption)){
			self::$pictureDAO->updatePictureCaption($pictureId, $caption);
		}
			
		return self::$responder->constructResponse(null);
	}

	public function rotatePicture($parameters){
		$parameters['action'] = 'rotateImage';
		$parameters["username"] =  $_SESSION["username"];;
		
		$parameters["version"] = 'optimized';
		$this->doImageRotate($parameters);
		
		$parameters["version"] = 'thumbnails';
		Forker::doPost($parameters);
		
		$parameters["version"] = 'main';
		Forker::doPost($parameters);
		return self::$responder->constructResponse(null);
	}

	public function doImageRotate($parameters){
		set_time_limit(0);
		$pictureId = $parameters["pictureId"];
		$picture = self::$pictureDAO->getPictureById($pictureId);

		Logger::log("username: ".$parameters["username"]);

		$username = $parameters["username"];

		$version = $parameters["version"];
		$rootDir = dirname(dirname(__FILE__)) .'/pictures/'. $username. '/'. $version .'/';
		$filepath = $rootDir. $picture->filename;

		Logger::log("filepath: ".$filepath);

		$image1 = @imagecreatefromjpeg($filepath);

		Logger::log("created image");

		$direction = $parameters["direction"];
		$degrees = 180;
		if ($direction == "left"){
			$degrees = 90;
		} else if ($direction == "right"){
			$degrees = -90;
		}
			
		Logger::log("before imagerotate");

		try {
			$image = @imagerotate($image1, $degrees, 0);
		} catch (Exception $e) {
			Logger::log($e->getMessage());
			throw new Exception();
		}


		Logger::log("after image rotate");

		imagejpeg($image, $filepath, 100);

		error_log("FILE ".$filepath);
		imagedestroy($image);
		Logger::log("done image");
	}

	public function updatePassword($parameters){

		$capturedCurrentPassword = $parameters["currentPassword"];
		$capturedNewPassword = $parameters["newPassword"];

		verifyUserSession($parameters);
			
		$dbCurrentPassword = self::$securityServices->getUserPassword($parameters["userid"]);

		if ((md5($capturedCurrentPassword, true)) != $dbCurrentPassword){
			throw new Exception("034");
		}

		self::$securityServices->updatePassword($parameters["userid"], $capturedNewPassword);

		try {
			$communicationServices = new CommunicationServices;
			$subject = "TimeTravel Password Change";
			$body = "Hi, \n\n You have changed your password at Sabside.com. \n\n Thank you for being part of our community.";
			$communicationServices->sendEmail($user->email, $subject, $body);
		} catch (Exception $e){
			error_log("Exception! Could not send email about updated details.  userid:".$userid);
		}


		return self::$responder->constructResponse("You password has been updated successfully.");
	}

	private function verifyUserSession($parameters){
		self::$securityServices->verifyUserSession($parameters);
	}

	/**
	 * Gets user details but grabs userid from session, passed on by Controller
	 */
	public function getUserDetails($parameters){
		$user = self::$securityServices->getUserById($parameters["userid"]);

		return self::$responder->constructResponse($user);
	}

	public function updateUser($parameters){
		$userid= $parameters["userid"];
		$name = $parameters["name"];
		$surname = $parameters["surname"];
		$username = $parameters["username"];
		$password = $parameters["password"];
		$email = $parameters["email"];
		$facebook = $parameters["facebook"];
		$cellphone = $parameters["cellphone"];

		try {
			self::$securityServices->updateUserDetails($userid, $name, $surname, $username, $email, $facebook, $cellphone);
		} catch (Exception $e){
			throw new Exception($e->getMessage());
		}

		try {
			$communicationServices = new CommunicationServices;
			$subject = "Kagogo User Details";
			$body = "Hi, \n\n You have updated your details at Kagogo.co.co.za. \n\n Thank you for being part of our community.";
			$communicationServices->sendEmail($user->email, $subject, $body);
		} catch (Exception $e){
			error_log("Exception! Could not send email about updated details.  userid:".$userid);
		}

		return self::$responder->constructResponse("Your details have been updated successfully.");
	}


	public function doForgotPassword($parameters){
		if (isset($parameters["identify_email"])){
			//is it an email?
			if (strpos($parameters["identify_email"], "@")){
				$user = self::$securityServices->getUserByEmailAddress($parameters["identify_email"]);
			} else {
				$user = self::$securityServices->getUserByUsername($parameters["identify_email"]);
			}
		} else {
			throw new Exception('028');
		}


		if (!isset($user)){
			throw new Exception("031");
		} else {
			$username = $user->username;
			$generatedPass = self::$securityServices->resetPassword($username);

			$communicationServices = new CommunicationServices;
			$subject = "Kagogo password reset.";
			$body = "Hi, \n\n You have requested a password reset on Kagogo.co.za. Here are the details: \n\n username: ". $username . "\n password: ". $generatedPass . "\n";

			try {
				$communicationServices->sendEmail($user->email, $subject, $body);
				return self::$responder->constructResponse("We have sent you an email to your email address with the details to logon to Kagogog.co.za.");
			} catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function createUser($parameters){
		error_log("[BusinessLogic] creating user...");
		try {
			$password = md5($parameters["password"], true);
			$user = self::$securityServices->createUser($parameters["username"], $password, $parameters["name"], $parameters["surname"], $parameters["email"]);
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('001');
		}

		return self::$responder->constructResponse(null);
	}

	public function getUserById($parameters){
		try {
			$user = self::$securityServices->getUserById($parameters["userid"]);

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('001');
		}

		return self::$responder->constructResponse($user);
	}

	public function getStatusUpdatesForUser($userid){
		Logger::log("getting status updates for useid: ".$userid);
		$lastUpdateDate = self::$userDAO->getStatusUpdatesLastUpdateDate($userid);

		if ($lastUpdateDate != null){
			$lastUpdateDate = substr($lastUpdateDate, 0, 10);
			$accessToken = self::$userDAO->getUserToken($userid);
				
			set_time_limit(0);
			Logger::log("fetching statuses from fb...");


			$url = "https://graph.facebook.com/me/statuses?since=".$lastUpdateDate."&until=tomorrow&&access_token=".  $accessToken;
			Logger::log("URL: ".$url);
				
			while  ($url != null){
				
				if (isset($statuses->paging->{'next'})){
					$url = $statuses->paging->{'next'};
				}
				$response = file_get_contents($url);
					
				$statuses = json_decode($response);


				$statusesArray = $statuses->{'data'};

				$this->saveFbStatusUpdates($statusesArray, $userid);
				$url = $statuses->paging->{'next'};
				Logger::log("URL: ".$url);
			}
				
		}
		
	}

	public function loginUser($parameters){

		$username = $parameters["username"];
		$password = $parameters["password"];

		$response = self::$securityServices->loginUser($username, $password);

		$parameters["action"] = Forker::$GET_STATUS_UPDATES;
		$parameters["userid"] = $_SESSION['userid'];
		Forker::doPost($parameters);

		return self::$responder->constructResponse(null);
	}


	private function verifyUserInSession($userid){

	}

}
?>