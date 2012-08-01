<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/Picture.php');
require_once(dirname(dirname(__FILE__)) .'/Logger.php');

class PictureDAO {

	public function updatePictureTimeTaken($pictureId, $dayid, $timetaken){
		error_log("in updatePictureTimeTaken ".$timetaken." new dayid: ".$dayid);
		try {
			if (($pictureId == null) || ($pictureId == "")){
				throw new Exception('004');
			}
				
			//$picture = $this->getPictureById($pictureId);
				
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("update picture set dayid=:dayid, timetaken=:timetaken where id = :id");
			$stmt->bindParam(':id', $pictureId);
			$stmt->bindParam(':dayid', $dayid);
			$stmt->bindParam(':timetaken', $timetaken);
	
			$stmt->execute();
	
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('004');
		}
	}
	
	
	public function updatePictureCaption($pictureId, $caption){
		error_log("in updatePictureCaption ".$caption);
		try {
			if (($pictureId == null) || ($pictureId == "")){
				throw new Exception('004');
			}
			
			$picture = $this->getPictureById($pictureId);
			
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("update picture set description=:description, timetaken=:timetaken where id = :id");
			$stmt->bindParam(':id', $pictureId);
			$stmt->bindParam(':description', $caption);
			$stmt->bindParam(':timetaken', $picture->timetaken);

			$stmt->execute();

		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('004');
		}
	}

	
	public function getPictureById($pictureId){
		$picture = null;
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from picture where id = :id");
			$stmt->bindParam(':id', $pictureId);
		
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$picture = new Picture($row["id"], $row["description"], $row["location"], $row["timetaken"], $row["fileType"], null, $row["filename"]);
					$picture->dayId = $row["dayid"];
				}
			}
		
		
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('004');
		}
		return $picture;
	}
	
	
	public function getAllPicturesForDay($dayId){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from picture where dayid = :dayid order by timetaken asc");
			$stmt->bindParam(':dayid', $dayId);
	
			$itemslist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$picture = new Picture($row["id"], $row["description"], $row["location"], $row["timetaken"], $row["fileType"], $row["payload"], $row["filename"]);
					array_push($itemslist, $picture);
				}
			}
	
	
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('004');
		}
		return $itemslist;
	}
	
	public function savePicture($dayId, $picture){
		Logger::log("in savePicture...");
		$id = $this->isPictureForUserExisting($dayId, $picture);
		
		if ($id != -1){
			Logger::log("picture already exists!");
			return;
		}

		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("insert into picture (description, dayid, location, timetaken, latitude, longitude, fileType, filename) values (:description, :dayId, :location, :timetaken, :latitude, :longitude, :fileType, :filename)");
			$stmt->bindParam(':description', $picture->description);
			$stmt->bindParam(':dayId', $dayId);
			$stmt->bindParam(':location', $picture->location);
			$stmt->bindParam(':timetaken', $picture->timetaken);
			$stmt->bindParam(':latitude', $picture->latitude);
			$stmt->bindParam(':longitude', $picture->longitude);
			$stmt->bindParam(':fileType', $picture->fileType);
			$stmt->bindParam(':filename', $picture->filename);
			//$stmt->bindParam(':payload', $picture->payload, PDO::PARAM_LOB);

			
			$stmt->execute();
			error_log("[PictureDAO] done savePicture..");
			
		} catch (PDOException $e) {
			Logger::log($e->getMessage());
			error_log("Error: ".$e->getMessage());
			throw new Exception('036');
		}
	
	}
	
	
	public function editPicture($picture){
		error_log("in editPicture...");
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("update picture set description=:description, dayid=:dayId, location=:location, timetaken=:timetaken, latitude=:latitude, longitude=:longitude, fileType=:fileType, filename=:filename, payload=:payload where id=:id");
			$stmt->bindParam(':id', $picture->id);
			$stmt->bindParam(':description', $picture->description);
			$stmt->bindParam(':dayId', $picture->dayId);
			$stmt->bindParam(':location', $picture->location);
			$stmt->bindParam(':timetaken', $picture->timetaken);
			$stmt->bindParam(':latitude', $picture->latitude);
			$stmt->bindParam(':longitude', $picture->longitude);
			$stmt->bindParam(':fileType', $picture->fileType);
			$stmt->bindParam(':filename', $picture->filename);
			$stmt->bindParam(':payload', $picture->payload, PDO::PARAM_LOB);
	
				
			$stmt->execute();
			error_log("[PictureDAO] done editPicture..");
				
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('036');
		}
	
	}
	
	public function createDay($userid, $date){
		error_log("in createDay...");
		
		$dayId = $this->isDayForUserExisting($userid, $date);
		if ($dayId != -1){
			Logger::log("day exists -- > ". $dayId);
			return $dayId;
		}
		
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
			$stmt = $con->prepare("insert into user_day (theDate, userid) values (:theDate, :userid)");
			$stmt->bindParam(':theDate', $date);
			$stmt->bindParam(':userid', $userid);

				
			try {
				$con->beginTransaction();
				$stmt->execute();
				$dayId = $con->lastInsertId();
				$con->commit();
				Logger::log($dayId);

			} catch(PDOExecption $e) {
				$con->rollback();
				error_log("Error: ".$e->getMessage());
				Logger::log("ERROR: ".$e->getMessage());
				throw new Exception('005');
			}
				
			Logger::log("[PictureDAO] done creating day..");
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			Logger::log("ERROR: ".$e->getMessage());
			throw new Exception('005');
		}
	
		return $dayId;
	}
	
	
	public function getNextAutoIncrementValueForTable($tableName){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbName = GlobalConfig::db_name;
			$stmt = $con->prepare("SELECT Auto_increment as id FROM information_schema.tables WHERE table_name=:tableName and table_schema=:db_name");
			$stmt->bindParam(':tableName', $tableName);
			$stmt->bindParam(':db_name', $dbName);
	
			$result = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["id"];
					break;
				}
			}
	
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('018');
		}
	
		return $result;
	}
	
	public function isPictureForUserExisting($dayId, $picture){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from picture where dayid=:dayid and filename = :filename");
			$stmt->bindParam(':dayid', $dayId);
			$stmt->bindParam(':filename', $picture->filename);
	
			$result = -1;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["id"];
					break;
				}
			}
	
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('018');
		}
	
		return $result;
	}
	
	public function isDayForUserExisting($userid, $date){
		$date = date("Y-m-d", strtotime($date));
		error_log("TIME TAKEN 2 : ".$date);
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user_day where userid=:userid and date(theDate) = :theDate");
			$stmt->bindParam(':theDate', $date);
			$stmt->bindParam(':userid', $userid);
		
			$result = -1;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$result = $row["id"];
					break;
				}
			}
		
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('018');
		}
		
		return $result;
	}
}

?>