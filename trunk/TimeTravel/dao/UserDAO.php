<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/StatusUpdate.php');

class UserDAO {
	
	
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
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("insert into status_update (id, theDate, message, userid, dayid) values (:id, :theDate, :message, :userid, :dayid)");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':theDate', $theDate);
			$stmt->bindParam(':message', $message);
			$stmt->bindParam(':id', $messageId);
			$stmt->bindParam(':dayid', $dayId);
	
			$stmt->execute();
	
	
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
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