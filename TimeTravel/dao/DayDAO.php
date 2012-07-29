<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');

class DayDAO {

	public function getRandomDay($userid){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("SELECT * FROM user_day WHERE userid = :userid ORDER BY RAND() LIMIT 1");
			$stmt->bindParam(':userid', $userid);

			$result = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["id"];
					break;
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}

		return $result;
	}
	
	public function getRandomDayForStatusUpdate($userid){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("select s.dayid as id from user_day d, status_update s where d.userid=:userid and s.userid = d.userid ORDER BY RAND() LIMIT 1");
			$stmt->bindParam(':userid', $userid);
	
			$result = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["id"];
					break;
				}
			}
	
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}
	
		return $result;
	}
	
	public function getDateForDayId($userid, $dayid){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("SELECT * FROM user_day WHERE userid = :userid and id=:dayid");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':dayid', $dayid);
	
			$result = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["theDate"];
					break;
				}
			}
	
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}
	
		return $result;
	}
	
	public function getIdForDay($userid, $theDate){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("SELECT * FROM user_day WHERE userid = :userid and date(theDate) = :theDate");
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':theDate', $theDate);
		
			$result = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["id"];
					break;
				}
			}
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}
		
		return $result;
	}
	
	public function getEarliestDateOfMemory($userid){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("select min(theDate) as theDate from user_day where userid = :userid");
			$stmt->bindParam(':userid', $userid);
		
			$result = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["theDate"];
					break;
				}
			}
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}
		
		return $result;
	}
}