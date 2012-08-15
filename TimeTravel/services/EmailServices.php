<?php
require_once(dirname(dirname(__FILE__)) . '/dao/GmailDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
require_once(dirname(dirname(__FILE__)) . '/services/securityServices.php');
require_once(dirname(dirname(__FILE__)) .'/EmailUtil.php');
require_once(dirname(dirname(__FILE__)) .'/util.php');

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
		self::$gmailDAO->saveGmailSyncDetails($_SESSION["userid"], $folderlist, $parameters["type"], $parameters["syncStart"]);
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

		try {
			self::$gmailDAO->saveGmailDetails($userid, $username, $password);
		} catch (Exception $e){
		}

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
		try {
			Logger::log("in updateGmailContent...");
			$username = $parameters["username"];
			$userid = self::$securityServices->getUserByUsername($username)->id;

			//We get the sms's first
			Logger::log("userid: ".$userid);
			$foldersToUpdate = self::$gmailDAO->getfoldersToUpdate($userid, 'sms_gmail');
			error_log("foldersToUpdate: ". sizeof($foldersToUpdate));

			foreach ($foldersToUpdate as $folderName => $lastupdate){
				Logger::log("folder: ".$folderName);
				$this->fetchFolderContent($userid, $folderName, $lastupdate, 'sms_gmail');
			}

			//We get the emails
			Logger::log("userid: ".$userid);
			$foldersToUpdate = self::$gmailDAO->getfoldersToUpdate($userid, 'email_gmail');
			error_log("foldersToUpdate: ". sizeof($foldersToUpdate));

			foreach ($foldersToUpdate as $folderName => $lastupdate){
				Logger::log("folder: ".$folderName);
				$this->fetchFolderContent($userid, $folderName, $lastupdate, 'email_gmail');
			}

		} catch (Exception $e){
			Logger::log("ERROR -- updateGmailContent ".$e->getMessage());
		}
		Logger::log("EmailService -- Done updating GMAIL!");
	}

	public function fetchFolderContent($userid, $folderName, $lastupdate, $type){
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

		$tomorrow = date('d-M-Y', mktime(0,0,0,date("m", time()),date("d", time())+1,date("Y", time())));
		$startDate = date('d-M-Y', mktime(0,0,0,date("m", strtotime($lastupdate)),date("d", strtotime($lastupdate))-1,date("Y", strtotime($lastupdate))));

		while ($startDate != $tomorrow){
			try {
				set_time_limit(1800);
				Logger::log("fetching Gmail content for date: ".$startDate);
				$emails = imap_search($inbox,'ON '.$startDate);
				if($emails) {

					$output = '';

					rsort($emails);
					foreach($emails as $email_number) {
						set_time_limit(0);
						$overview = imap_fetch_overview($inbox,$email_number,0);
							
						if ($type == 'sms_gmail'){
							$message = quoted_printable_decode(imap_fetchbody($inbox,$email_number,1));
						} else {
							$dataTxt = EmailUtil::get_part($inbox, $email_number, "TEXT/PLAIN");

							// GET HTML BODY
							$dataHtml = EmailUtil::get_part($inbox, $email_number, "TEXT/HTML");

							if ($dataHtml != "") {
								$message = $dataHtml;
								$mailformat = "html";
							} else {
								$message = ereg_replace("\n","<br>",$dataTxt);
								$mailformat = "text";
							}

						}


						$messageDate = $overview[0]->date;
						$gmtTimezone = new DateTimeZone('Africa/Johannesburg');
						$myDateTime = new DateTime($messageDate, $gmtTimezone);

						$theDate = date('c', $myDateTime->format('U') + 1);
						$theDate = date('Y-m-d H:i:s', strtotime($theDate));
						$recipient = quoted_printable_decode($overview[0]->to);


						$subject = Util::ShortenString(quoted_printable_decode($overview[0]->subject), 250);
						$from = quoted_printable_decode($overview[0]->from);

						$dayid = self::$pictureDAO->createDay($userid, $theDate);

						//Logger::log($messageDate." --- ".$message);
						//Logger::log($message);
						try {
							self::$gmailDAO->saveCommunication($subject, $message, $theDate, $dayid, $from, $type, $recipient);
						} catch (Exception $e){
						}
					}

				}

			} catch (Exception $e){
				Logger::log($e->getMessage());
			}

			self::$gmailDAO->updateLastUpdate($userid, $type, date("Y-m-d", strtotime($startDate)));

			$tempDate = mktime(0,0,0,date("m", strtotime($startDate)),date("d", strtotime($startDate))+1,date("Y", strtotime($startDate)));
			$startDate = date("d-M-Y", $tempDate);
		}
		imap_close($inbox);
	}

}
?>