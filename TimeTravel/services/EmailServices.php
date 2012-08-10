<?php
require_once(dirname(dirname(__FILE__)) . '/dao/GmailDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
require_once(dirname(dirname(__FILE__)) . '/services/securityServices.php');
date_default_timezone_set('Africa/Johannesburg');
class EmailServices {

	private static $hostname = '{imap.gmail.com:993/imap/ssl}';
	private static $responder;
	private static $gmailDAO;
	private static $securityServices;
	private static $pictureDAO;
	const INBOX = "{mail.google.com}";

	function __construct() {
		self::$responder = new Responder;
		self::$gmailDAO = new GmailDAO();
		self::$securityServices = new SecurityService();
		self::$pictureDAO = new PictureDAO();
	}


	public function saveProviderFolders($parameters){
		$folders = split(",", $parameters["folders"]);
		$folderlist = array();
		foreach($folders as $folder){
			if (sizeof((trim($folder))) > 0 && (trim($folder) != "")){
				array_push($folderlist, $folder);
			}
		}
		self::$gmailDAO->saveGmailSyncDetails($_SESSION["userid"], $folderlist, $parameters["type"]);
		self::$responder->constructResponse(null);
	}

	private function getImapConnection($username, $password, $label){
		if ($inbox = imap_open(self::$hostname.$label ,$username, $password)){
			error_log("error: ".imap_last_error());
			return $inbox;
		} else {
			Logger::log("IMAP ERROR: ".imap_last_error());
			throw new Exception("");
		}
	}

	public function saveGmailDetails($parameters){
		$username = $parameters["gmailusername"];
		$password = $parameters["gmailpassword"];
		$userid = $_SESSION["userid"];

		try {
			$inbox = $this->getImapConnection($username, $password, "INBOX");
		} catch (Exception $e){
			error_log("Errror Caugt!!!");
			return self::$responder->constructErrorResponse("I could not connect to Gmail. Make sure your login details are correct.");
		}


		self::$gmailDAO->saveGmailDetails($userid, $username, $password);

		$folderList = array();
		$emailFolderList = imap_getmailboxes($inbox, self::INBOX, "*");
		error_log("list size: ".sizeof($emailFolderList));
		if (is_array($emailFolderList)) {
			$count = 0;
			foreach ($emailFolderList as $key => $val) {
				$temp = imap_utf7_decode($val->name);
				$folder = str_replace(self::INBOX, "", $temp);

				if (!strstr($folder, "[Gmail]")){
					$folderList["folder". ++$count] = $folder;
				}
			}
		} else {
			return self::$responder->constructErrorResponse("Could not get the list of folder from your account, please try again.");
		}


		imap_close($inbox);
		return self::$responder->constructResponseForKeyValue(array_reverse($folderList));

	}


	public function updateGmailContent($parameters){
		Logger::log("in updateGmailContent...");
		$username = $parameters["username"];
		$userid = self::$securityServices->getUserByUsername($username)->id;

		error_log("userid: ".$userid);
		$foldersToUpdate = self::$gmailDAO->getSMSfolders($userid);
		error_log("foldersToUpdate: ". sizeof($foldersToUpdate));

		foreach ($foldersToUpdate as $folderName => $lastupdate){
			Logger::log("folder: ".$folderName);
			$this->fetchFolderContent($userid, $folderName, $lastupdate);
		}
		
	}

	public function fetchFolderContent($userid, $folderName, $lastupdate){
		Logger::log("fetching folder: ".$folderName);
		$details = self::$gmailDAO->getGmailAccessDetailsForUser($userid);
		$username = "";
		$password = "";
		foreach ($details as $key => $value){
			if ($key == 'username'){
				$username = $value;
			} else if ($key == 'password'){
				$password = $value;
			}
		}
		$inbox = $this->getImapConnection($username, $password, $folderName);
		
		$today = date('d-M-Y', time());
		
		
		$startDate = date("d-M-Y", strtotime($lastupdate));
		//$startDate = date("d-M-Y", strtotime('2012-08-05'));
		while ($startDate != $today){
			set_time_limit(0);
			$emails = imap_search($inbox,'ON '.$startDate);
			if($emails) {

				$output = '';

				rsort($emails);
				foreach($emails as $email_number) {
					set_time_limit(0);
					$overview = imap_fetch_overview($inbox,$email_number,0);
					$message = quoted_printable_decode(imap_fetchbody($inbox,$email_number,1));


					$messageDate = $overview[0]->date;
					$gmtTimezone = new DateTimeZone('Africa/Johannesburg');
					$myDateTime = new DateTime($messageDate, $gmtTimezone);

					$theDate = date('c', $myDateTime->format('U') + 1);
					$theDate = date('Y-m-d H:i:s', strtotime($theDate));
					$recipient = quoted_printable_decode($overview[0]->to);


					$subject = quoted_printable_decode($overview[0]->subject);
					$from = quoted_printable_decode($overview[0]->from);

					$dayid = self::$pictureDAO->createDay($userid, $theDate);

					Logger::log($messageDate." --- ".$message);
					Logger::log($message);
					try {
						self::$gmailDAO->saveCommunication($subject, $message, $theDate, $dayid, $from, 'sms', $recipient);
					} catch (Exception $e){
					}
				}

			}
			
			self::$gmailDAO->updateLastUpdate($userid, 'sms_gmail', date("Y-m-d", strtotime($startDate)));
			
			$tempDate = mktime(0,0,0,date("m", strtotime($startDate)),date("d", strtotime($startDate))+1,date("Y", strtotime($startDate)));
			$startDate = date("d-M-Y", $tempDate);
		}
		imap_close($inbox);
	}

}
?>