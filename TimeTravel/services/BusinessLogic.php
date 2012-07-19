<?php

require_once(dirname(dirname(__FILE__)) . '/services/securityServices.php');
require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
require_once(dirname(dirname(__FILE__)) . '/errorCodes.php');
require_once(dirname(dirname(__FILE__)) . '/dto/Action.php');
date_default_timezone_set('Africa/Johannesburg');

class BusinessLogic{
	
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
			die();
			throw new Exception('032');
		}
		$responder = new Responder;
		return $responder->constructResponse(null);
	}
	
	public function saveTokenToSession($parameters){
		try{
			$accessToken = $parameters["access_token"];
			$_SESSION["access_token"] = $accessToken;
			
			$userDAO = new UserDAO();
			$userDAO->saveFbToken($_SESSION["userid"], $accessToken);
			
			$this->updateFbDetails($accessToken);
			$this->retrieveAndSaveAllStatusUpdatesFromFb($_SESSION["access_token"]);
			
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('032');
		}
		$responder = new Responder;
		return $responder->constructResponse(null);
	}
	
	private function retrieveAndSaveAllStatusUpdatesFromFb($accessToken){
		error_log("fetching statuses from fb...");
		$response = file_get_contents("https://graph.facebook.com/me/statuses?access_token=".  $accessToken);
		error_log("STATUS UPDATE: ".$response);
		$statuses = json_decode($response);
		$statusesArray = $statuses->{'data'};
		$userid = $_SESSION["userid"];
		
		$userDAO = new UserDAO();
		$pictureDAO = new PictureDAO();
		foreach ($statusesArray as $status){
			 $gmtTimezone = new DateTimeZone('Africa/Johannesburg');
			 $myDateTime = new DateTime($status->{'updated_time'}, $gmtTimezone);
			 	
			 $theDate = date('c', $myDateTime->format('U') + 1);
			 $theDate = date('Y-m-d H:i:s', strtotime($theDate));
			 error_log("DATE: ".$theDate);
			 $message = $status->{'message'};
			 $messageId = $status->{'id'};
			 	
			 $dayId = $pictureDAO->createDay($userid, $theDate);
			 $userDAO->saveStatusUpdate($userid, $theDate, $message, $messageId, $dayId);
		}

		$url = $statuses->paging->{'next'};
		while (isset($url)){
			set_time_limit(60);
			error_log("fetching statuses from fb...");
			$response = file_get_contents($url);
			error_log("STATUS UPDATE: ".$response);
			$statuses = json_decode($response);
			$statusesArray = $statuses->{'data'};
			$userDAO = new UserDAO();
			foreach ($statusesArray as $status){
				$gmtTimezone = new DateTimeZone('Africa/Johannesburg');
				$myDateTime = new DateTime($status->{'updated_time'}, $gmtTimezone);
					
				$theDate = date('c', $myDateTime->format('U') + 1);
				$theDate = date('Y-m-d H:i:s', strtotime($theDate));
				error_log("DATE: ".$theDate);
				$message = $status->{'message'};
				$messageId = $status->{'id'};
					
				$dayId = $pictureDAO->createDay($userid, $theDate);
				$userDAO->saveStatusUpdate($userid, $theDate, $message, $messageId, $dayId);
			}
			
			$url = null;
			if (isset($statuses->paging->{'next'})){
				$url = $statuses->paging->{'next'};
			}
		}
		
	}
	
	private function updateFbDetails($accessToken){
		$response = file_get_contents("https://graph.facebook.com/me?access_token=".  $accessToken);
		error_log("FB USER INFO: ".$response);
		$fbuser = json_decode(str_replace('\"','"',$response));
			
		$securityServices = new SecurityService;
		$user = $securityServices->getUserById($_SESSION["userid"]);
		
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
		
		$securityServices->updateUserDetails($user->id, $user->name, $user->surname, $user->username, $user->email, $gender, $user->cellphone, $location, $work, $birthday, $timezone);

	}
	
	public function updatePictureCaption($parameters){
		error_log("in updatePictureCaption");
		try{
			$pictureDAO = new PictureDAO();
			$pictureId = $parameters["pictureId"];
			$caption = $parameters["caption"];
			$timetaken = $parameters["dateandtime"];
			
			if (isset($timetaken)){
				error_log("timetaken is set");
				$dayid = $pictureDAO->createDay($_SESSION["userid"], $timetaken);
				$pictureDAO->updatePictureTimeTaken($pictureId, $dayid, $timetaken);
				
			} else if (isset($caption)){
				$pictureDAO->updatePictureCaption($pictureId, $caption);
			}
			
			
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('032');
		}
		$responder = new Responder;
		return $responder->constructResponse(null);
	}
	
	public function rotatePicture($parameters){
		try {
			
			$pictureDAO = new PictureDAO();
			$pictureId = $parameters["pictureId"];
			$picture = $pictureDAO->getPictureById($pictureId);
			
			$username = $_SESSION["username"];
			$rootDir = dirname(dirname(__FILE__)) .'/pictures/'. $username. '/main/';
			$filepath = $rootDir. $picture->filename;
			
			$image1 = @imagecreatefromjpeg($filepath);

			$direction = $parameters["direction"];
			$degrees = 180;
			if ($direction == "left"){
				$degrees = 90;
			} else if ($direction == "right"){
				$degrees = -90;
			}
			
			$image = @imagerotate($image1, $degrees, 0);

			imagejpeg($image, $filepath, 100);

			error_log("FILE ".$filepath);
			imagedestroy($image);

			/* $payload = file_get_contents($filepath);
			$picture->payload = $payload;

			$pictureDAO->editPicture($picture);
			unlink($filepath); */
		} catch (Exception $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('032');
		}
		$responder = new Responder;
		return $responder->constructResponse(null);
	}

	public function updatePassword($parameters){
		
		$securityServices = new SecurityService;
		$capturedCurrentPassword = $parameters["currentPassword"];
		$capturedNewPassword = $parameters["newPassword"];
		try {
			verifyUserSession($parameters);
			
			$dbCurrentPassword = $securityServices->getUserPassword($parameters["userid"]);

			if ((md5($capturedCurrentPassword, true)) != $dbCurrentPassword){
				throw new Exception("034");
			}

			$securityServices->updatePassword($parameters["userid"], $capturedNewPassword);

			try {
				$communicationServices = new CommunicationServices;
				$subject = "Kagogo Password Change";
				$body = "Hi, \n\n You have changed your password at Kagogo.co.co.za. \n\n Thank you for being part of our community.";
				$communicationServices->sendEmail($user->email, $subject, $body);
			} catch (Exception $e){
				error_log("Exception! Could not send email about updated details.  userid:".$userid);
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('032');
		}

		$responder = new Responder;
		return $responder->constructResponse("You password has been updated successfully.");
	}
	
	private function verifyUserSession($parameters){
		$securityServices = new SecurityService;
		$securityServices->verifyUserSession($parameters);
	}

	/**
	 * Gets user details but grabs userid from session, passed on by Controller
	 */
	public function getUserDetails($parameters){
		$securityServices = new SecurityService;
		try {
			$user = $securityServices->getUserById($parameters["userid"]);

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('001');
		}

		$responder = new Responder;
		return $responder->constructResponse($user);
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

		$securityServices = new SecurityService;
		try {
			$securityServices->updateUserDetails($userid, $name, $surname, $username, $email, $facebook, $cellphone);
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

		$responder = new Responder;
		return $responder->constructResponse("Your details have been updated successfully.");
	}


	public function doForgotPassword($parameters){
		$securityServices = new SecurityService;
		if (isset($parameters["identify_email"])){
			//is it an email?
			if (strpos($parameters["identify_email"], "@")){
				$user = $securityServices->getUserByEmailAddress($parameters["identify_email"]);
			} else {
				$user = $securityServices->getUserByUsername($parameters["identify_email"]);
			}
		} else {
			throw new Exception('028');
		}


		$responder = new Responder;
		if (!isset($user)){
			throw new Exception("031");
		} else {
			$username = $user->username;
			$generatedPass = $securityServices->resetPassword($username);

			$communicationServices = new CommunicationServices;
			$subject = "Kagogo password reset.";
			$body = "Hi, \n\n You have requested a password reset on Kagogo.co.za. Here are the details: \n\n username: ". $username . "\n password: ". $generatedPass . "\n";

			try {
				$communicationServices->sendEmail($user->email, $subject, $body);
				return $responder->constructResponse("We have sent you an email to your email address with the details to logon to Kagogog.co.za.");
			} catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function createUser($parameters){
		error_log("[BusinessLogic] creating user...");
		$securityServices = new SecurityService;
		try {
			$password = md5($parameters["password"], true);
			$user = $securityServices->createUser($parameters["username"], $password, $parameters["name"], $parameters["surname"], $parameters["email"]);
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('001');
		}

		$responder = new Responder;
		return $responder->constructResponse(null);
	}

	public function getUserById($parameters){
		$securityServices = new SecurityService;
		try {
			$user = $securityServices->getUserById($parameters["userid"]);

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('001');
		}

		$responder = new Responder;
		return $responder->constructResponse($user);
	}


	public function loginUser($parameters){

		$username = $parameters["username"];
		$password = $parameters["password"];
		$securityServices = new SecurityService;

		try {

			$securityServices->loginUser($username, $password);

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('001');
		}

		$responder = new Responder;
		return $responder->constructResponse(null);
	}
	
	
	private function verifyUserInSession($userid){
		
	}

}
?>