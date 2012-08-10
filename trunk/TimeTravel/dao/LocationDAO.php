<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/Location.php');

class LocationDAO {
	
	public function saveLocation($dayid, $timestamp, $longitude, $latitude){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("insert into location (dayid, theTimestamp, longitude, latitude) values (:dayid, :theTimestamp, :longitude, :latitude)");
			$stmt->bindParam(':dayid', $dayid);
			$stmt->bindParam(':theTimestamp', $timestamp);
			$stmt->bindParam(':longitude', $longitude);
			$stmt->bindParam(':latitude', $latitude);
		
			$stmt->execute();
		
		
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
	}
	
	public function getLocationsForDay($dayId){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from location where dayid=:dayid order by theTimestamp");
			$stmt->bindParam(':dayid', $dayId);
		
			$itemslist = array();
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$location = new Location($row["theTimestamp"], $row["longitude"], $row["latitude"]);
					array_push($itemslist, $location);
				}
			}
		
		} catch (PDOException $e) {
			error_log("Error: ".$e->getMessage());
			throw new Exception('018');
		}
		
		return $itemslist;
	}
	
}