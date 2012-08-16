<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/Communication.php');

class GmailDAO {

	private static $con;

	function __construct() {
		self::$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
		self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}


	public function saveGmailDetails($userid, $gmailUsername, $gmailPassword){
		error_log("in saveGmailDetails..");
		try {
			self::$con->beginTransaction();

			//We save the access details main entry
			$stmt1 = self::$con->prepare("insert into accessdetails (accesstype, userid) values ('gmail', :userid)");
			$stmt1->bindParam(':userid', $userid);
			$stmt1->execute();

			//we get the access details id
			$stmt2 = self::$con->prepare("select id from accessdetails where accesstype='gmail' and userid=:userid");
			$stmt2->bindParam(':userid', $userid);
			$accessDetailsId = -1;
			if ($stmt2->execute()){
				while ($row = $stmt2->fetch()){
					$accessDetailsId = $row["id"];
					break;
				}
			} else {
				throw new Exception('040');
			}

			//save the username
			$stmt3 = self::$con->prepare("insert into accessdetailsentries values (:accessDetailsId, 'username', :gmailUsername)");
			$stmt3->bindParam(':accessDetailsId', $accessDetailsId);
			$stmt3->bindParam(':gmailUsername', $gmailUsername);
			$stmt3->execute();


			//save the password
			$stmt4 = self::$con->prepare("insert into accessdetailsentries values (:accessDetailsId, 'password', :gmailPassword)");
			$stmt4->bindParam(':accessDetailsId', $accessDetailsId);
			$stmt4->bindParam(':gmailPassword', $gmailPassword);
			$stmt4->execute();

			self::$con->commit();
		} catch (Exception $e) {
			self::$con->rollBack();
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
	}


	public function saveGmailSyncDetails($userid, $folderlist, $type, $syncStart){
		error_log("in saveGmailSyncDetails..");
		try {

			self::$con->beginTransaction();

			$stmt1 = self::$con->prepare("insert into contentupdate (type, lastupdate, active, userid) values (:type, :lastupdate, true, :userid)");
			$stmt1->bindParam(':userid', $userid);
			$stmt1->bindParam(':type', $type);
			$stmt1->bindParam(':lastupdate', $syncStart);
			$stmt1->execute();
			$contentUpdateId = self::$con->lastInsertId();

			foreach ($folderlist as $folder){
				$stmt2 = self::$con->prepare("insert into contentupdatedetails (contentupdateid, contentkey) values (:contentupdateid, :contentkey)");
				$stmt2->bindParam(':contentupdateid', $contentUpdateId);
				$stmt2->bindParam(':contentkey', $folder);
				$stmt2->execute();
			}

			self::$con->commit();
		} catch (Exception $e) {
			self::$con->rollBack();
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
	}

	public function getfoldersToUpdate($userid, $type){
		try {
			$stmt = self::$con->prepare("select d.contentkey as folder, c.lastupdate from contentupdatedetails d, contentupdate c where c.id=d.contentupdateid and c.userid=:userid and c.type=:type");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':type', $type);
			$itemlist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$itemlist[$row["folder"]] = $row["lastupdate"];
				}
			} else {
				throw new Exception('040');
			}
		} catch (Exception $e) {
			self::$con->rollBack();
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
		return $itemlist;
	}

	public function saveCommunication($title, $body, $timestamp, $dayid, $from, $type, $recipient){
		Logger::log("in saveCommunication...");
		try {
			$stmt = self::$con->prepare("insert into communicationcontent (title, body, theTimestamp, dayid, source, communicationtype, recipient) values (:title, :body, :theTimestamp, :dayid, :from, :type, :recipient)");
			$stmt->bindParam(':title', $title);
			$stmt->bindParam(':body', $body);
			$stmt->bindParam(':theTimestamp', $timestamp);
			$stmt->bindParam(':dayid', $dayid);
			$stmt->bindParam(':from', $from);
			$stmt->bindParam(':type', $type);
			$stmt->bindParam(':recipient', $recipient);

			$stmt->execute();
		} catch (Exception $e) {
			Logger::log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
	}

	public function updateLastUpdate($userid, $type, $lastupdate){
		Logger::log("in updateLastUpdate :".$lastupdate." type: ".$type);
		try {
			$stmt = self::$con->prepare("update contentupdate set lastupdate=:lastupdate where userid=:userid and type=:type");
			$stmt->bindParam(':lastupdate', $lastupdate);
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':type', $type);

			$stmt->execute();
		} catch (Exception $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
	}

	public function hasUserSetupContentUpdate($userid, $type){
		$result = false;
		try {
			$stmt = self::$con->prepare("select id from contentupdate where userid=:userid and type like '". $type ."_%'");
			$stmt->bindParam(':userid', $userid);
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = true;
					break;
				}
			} else {
				throw new Exception('040');
			}
		} catch (Exception $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
		return $result;
	}

	public function getCommunicationContentForDay($dayid, $type){
		try {
			$stmt = self::$con->prepare("select * from communicationcontent where dayid=:dayid and communicationtype=:type order by theTimestamp desc");
			$stmt->bindParam(':dayid', $dayid);
			$stmt->bindParam(':type', $type);
			$itemlist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$communication = new Communication($row["id"], $row["title"], $row["body"], $row["theTimestamp"], $row["source"], $row["recipient"]);
					array_push($itemlist, $communication);
				}
			} else {
				throw new Exception('040');
			}
		} catch (Exception $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
		return $itemlist;
	}

	public function getRandomCommunicationContentForDay($dayid, $type){
		try {
			$stmt = self::$con->prepare("select * from communicationcontent where dayid=:dayid and communicationtype=:type order by rand() limit 1");
			$stmt->bindParam(':dayid', $dayid);
			$stmt->bindParam(':type', $type);
			$communication = null;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$communication = new Communication($row["id"], $row["title"], $row["body"], $row["theTimestamp"], $row["source"]);
				}
			} else {
				throw new Exception('040');
			}
		} catch (Exception $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
		return $communication;
	}


	public function getGmailAccessDetailsForUser($userid){
		try {
			$stmt = self::$con->prepare("select e.key, e.value from accessdetails a,accessdetailsentries e where a.id=e.accessdetailsid and a.userid=11;");
			$stmt->bindParam(':dayid', $dayid);
			$stmt->bindParam(':type', $type);
			$itemlist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$itemlist[$row["key"]] = $row["value"];
				}
			} else {
				throw new Exception('040');
			}
		} catch (Exception $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
		return $itemlist;

	}


	public function getCommunicationById($commId){
		try {
			$stmt = self::$con->prepare("select * from communicationcontent where id=:id");
			$stmt->bindParam(':id', $commId);
			$communication = null;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$communication = new Communication($row["id"], $row["title"], $row["body"], $row["theTimestamp"], $row["source"], $row["recipient"]);
				}
			} else {
				throw new Exception('040');
			}
		} catch (Exception $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('040');
		}
		return $communication;
	}
}
?>