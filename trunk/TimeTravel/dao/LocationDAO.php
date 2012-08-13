<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/Location.php');

class LocationDAO {

	private static $con;

	private static $userDAO;
	private static $securityService;
	private static $dayDAO;

	function __construct() {
		self::$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
		self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		self::$userDAO = new UserDAO();
		self::$securityService = new SecurityService();
		self::$dayDAO = new DayDAO();
	}

	public function saveLocation($dayid, $timestamp, $longitude, $latitude){
		try {

			$stmt = self::$con->prepare("insert into location (dayid, theTimestamp, longitude, latitude) values (:dayid, :theTimestamp, :longitude, :latitude)");
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
		$itemslist = array();
		try {

			$today = self::$dayDAO->getDateForDayId($_SESSION["userid"], $dayId);
			$yesterdayDate = date('Y-m-d', mktime(0,0,0,date("m", strtotime($today)),date("d", strtotime($today))-1,date("Y", strtotime($today))));
			error_log("YESTERDAY: ".$yesterdayDate);
			$yesterdayId = self::$dayDAO->getIdForDay($_SESSION["userid"], $yesterdayDate);

			$stmt = self::$con->prepare("select max(theTimestamp) as theTimestamp, longitude, latitude from location where dayid=:dayid");
			$stmt->bindParam(':dayid', $yesterdayId);
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					if ($row["longitude"] != ""){
						$location = new Location($row["theTimestamp"], $row["longitude"], $row["latitude"]);
						array_push($itemslist, $location);
					}
				}
			}


			$stmt = self::$con->prepare("select * from location where dayid=:dayid order by theTimestamp");
			$stmt->bindParam(':dayid', $dayId);


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