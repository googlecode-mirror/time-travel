<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');

class DayDAO {
	
	/**
	 * Deletes a day if unused
	 * @param unknown_type $dayId
	 */
	public function deleteDayIfUnused($dayId){
		error_log("tryin to deleteDay..".$dayId);
		if ((!$this->isDayUsed($dayId, "picture") && (!$this->isDayUsed($dayId, "status_update") && (!$this->isDayUsed($dayId, "location")) && (!$this->isDayUsed($dayId, "communicationcontent"))))){
			$this->deleteDay($dayId);
		}

	}

	private function deleteDay($dayId){
		error_log("in deleteDay..");
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("delete from user_day where id=:id");
			$stmt->bindParam(':id', $dayId);
		
			$stmt->execute();
			error_log("done deleteDay..");
		
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('036');
		}
	}
	
	private function isDayUsed($dayId, $artifactTable){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from ". $artifactTable ." where dayid = :dayid limit 1");
			$stmt->bindParam(':dayid', $dayId);

			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					error_log("day is still used ".$dayId ." by ".$artifactTable);
					return true;
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}

		return false;
	}

	public function getRandomDay($userid, $options){
		if (isset($options)){
			error_log("I need to randomize:: ".$options);
		}
		
		$query = " ";
		if (strpos($options, "picture") > 0){
			$query .= " and id in (select dayid from picture)";
		}
		
		if (strpos($options, "sms") > 0){
			$query .= " and id in (select dayid from communicationcontent where communicationtype='sms')";
		}

		if (strpos($options, "email") > 0){
			$query .= " and id in (select dayid from communicationcontent where communicationtype='email')";
		}
		
		if (strpos($options, "s-update") > 0){
			$query .= " and id in (select dayid from status_update)";
		}
		
		if (strpos($options, "phone") > 0){
			$query .= " and id in (select dayid from communicationcontent where communicationtype='call')";
		}
		
		error_log("QUERY: ".$query);
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select id from user_day where userid=:userid ". $query. " order by rand() limit 1");
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
	
	public function getAllDayId(){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("select id from user_day order by id asc");

			$itemlist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$id = $row["id"];
					array_push($itemlist, $id);
				}
			}
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}
		
		return $itemlist;
	}
}