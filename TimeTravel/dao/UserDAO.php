<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/StatusUpdate.php');

class UserDAO {
	
	public function getIdForSharedContentType($contentType){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select id from contenttype where description = :contenttype");
			$stmt->bindParam(':contenttype', $contentType);
		
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					return $row["id"];
				}
			}
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return false;
	}
	
	
	
	private function doesStatusUpdateExist($statusUpdateId){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select id from status_update where id=:id");
			$stmt->bindParam(':id', $statusUpdateId);
		
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					return true;
				}
			}
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return false;
	}
	
	public function getUserToken($userid){
		$fbToken = null;
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select fbToken from user where id=:id");
			$stmt->bindParam(':id', $userid);
		
			$fbToken = null;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$fbToken = $row['fbToken'];
				}
			}
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return $fbToken;
	}
	
	public function getStatusUpdatesLastUpdateDate($userid){
		$lastUpdateDate = null;
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select max(theDate) as theDate from status_update where userid=:userid");
			$stmt->bindParam(':userid', $userid);
		
			$statusUpdate = null;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$lastUpdateDate = $row['theDate'];
				}
			}
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return $lastUpdateDate;
	}
	
	public function saveFbToken($userid, $fbToken){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("update user set fbToken = :fbToken where id=:userid");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':fbToken', $fbToken);

			$stmt->execute();

	
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}
	
	}

	public function saveStatusUpdate($userid, $theDate, $message, $messageId, $dayId){
		error_log("in saveStatusUpdate: ".$message);
		if ($this->doesStatusUpdateExist($messageId)){
			return;
		}
		
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("insert into status_update (id, theDate, message, userid, dayid) values (:id, :theDate, :message, :userid, :dayid)");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':theDate', $theDate);
			$stmt->bindParam(':message', $message);
			$stmt->bindParam(':id', $messageId);
			$stmt->bindParam(':dayid', $dayId);
	
			$stmt->execute();
	
		} catch (PDOException $e) {
			error_log($e->getMessage());
			throw new Exception('018');
		}
	
	}
	
	public function retrieveRandomStatusUpdateForDay($userid, $dayId){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from status_update where userid=:userid and dayid=:dayid order by rand() limit 1");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':dayid', $dayId);
		
			$statusUpdate = null;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$statusUpdate = new StatusUpdate($row['id'], $row['theDate'], $row['message']);
				}
			}
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return $statusUpdate;
	}	
	
	public function retrieveAllStatusUpdatesForDay($userid, $dayId){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from status_update where userid=:userid and dayid=:dayid order by theDate");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':dayid', $dayId);
		
			$itemslist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$statusUpdate = new StatusUpdate($row['id'], $row['theDate'], $row['message']);
					array_push($itemslist, $statusUpdate);
				}
			}
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return $itemslist;
	}
}

?>