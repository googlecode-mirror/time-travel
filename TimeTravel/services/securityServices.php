<?php
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) . '/services/ResponseParser.php');
require_once(dirname(dirname(__FILE__)) .'/services/CommunicationServices.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/User.php');

class SecurityService  {

	public function getUserPassword($userid){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select password from user where id= :userid");
			$stmt->bindParam(':userid', $userid);

			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$password = $row["password"];
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('033');
		}
		return $password;
	}

	public function getUserByUsername($username){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user where username= :username");
			$stmt->bindParam(':username', $username);

			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$user = new User($row["id"], $row["username"], $row["name"], $row["surname"], $row["email"], $row["cellphone"], $row["password"], 
							$row["gender"], $row["location"], $row["work"], $row["birthday"], $row["timezone"]);
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('027');
		}

		return $user;
	}

	public function getUserByEmailAddress($emailaddress){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user where email= :email");
			$stmt->bindParam(':email', $emailaddress);

			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$user = new User($row["id"], $row["username"], $row["name"], $row["surname"], $row["email"], $row["cellphone"], $row["password"], 
							$row["gender"], $row["location"], $row["work"], $row["birthday"], $row["timezone"]);
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('027');
		}

		return $user;
	}

	public function isUserLoggedIn($parameters){
		if (isset($_SESSION['userid'])){
			$responseArray = array("loggedIn" => "true");
		} else {
			$responseArray = array("loggedIn" => "false");
		}

		$responder = new Responder;
		return $responder->constructResponseForKeyValue($responseArray);
	}

	public function createUser($username, $password, $name, $surname, $email){
		error_log("[SecurityService] creating user...");
		//$hashedPassword = hash("md5", $password);

		if ($this->isUsernameExist($username)){
			throw new Exception('022');
		}

		if ($this->isUserEmailExist($email)){
			throw new Exception('023');
		}

		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("insert into user (username, password, name, surname, email, created, active) values (:username, :password, :name, :surname, :email, NOW(), 1)");
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':password', $password);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':surname', $surname);
			$stmt->bindParam(':email', $email);

			$stmt->execute();
			
			//we create the pictures directories
			mkdir(dirname(dirname(__FILE__)) .'/pictures/'. $username);
			mkdir(dirname(dirname(__FILE__)) .'/pictures/'. $username.'/temp');
			mkdir(dirname(dirname(__FILE__)) .'/pictures/'. $username.'/main');
			mkdir(dirname(dirname(__FILE__)) .'/pictures/'. $username.'/thumbnails');
			
			
			error_log("[SecurityService] done creating user...");
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('019');
		}

		$responder = new Responder;
		return $responder->constructResponse(null);
	}

	private function getMaxIdFromTable($table){
		$maxId = 0;
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select max(id) as id from ".$table);

			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$maxId = $row["id"];
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return $maxId;
	}

	public function resetPassword($username){
		error_log("Resetting password for: ".$username);
		$generatedPass = $this->generatePassword();
		error_log("Generated pass for ". $username. " : ".$generatedPass);
		$hashedPass = md5($generatedPass, true);

		if (!($this->isUsernameExist($username))){
			throw new Exception('020');
		}

		try {

			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$stmt = $con->prepare("update user set password=:password where username=:username");
			$stmt->bindParam(':password', $hashedPass);
			$stmt->bindParam(':username', $username);

			if (!$stmt->execute()){
				throw new Exception('030');
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('030');
		}
		return $generatedPass;
	}

	public function updatePassword($userid, $password){
		error_log("updating password...");
		$hashedPass = md5($password, true);
		try {

			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("update user set password=:password where id=:userid");
			$stmt->bindParam(':password', $hashedPass);
			$stmt->bindParam(':userid', $userid);

			if (!$stmt->execute()){
				throw new Exception('032');
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('032');
		}
	}

	public function getUserEmailAddress($username){
		$emailaddres = "";
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select email from user where username= :username");
			$stmt->bindParam(':username', $username);

			$count = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$emailaddres = $row["email"];
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('004');
		}
		return $emailaddres;
	}

	private function generatePassword(){
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";

		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}

		return $pass;
	}

	private function isUsernameExist($username){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user where username= :username");
			$stmt->bindParam(':username', $username);

			$count = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$count++;
				}
			}

			error_log("USER: ".$username);

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('018');
		}

		if ($count > 0){
			return true;
		} else {
			return false;
		}
	}


	private function isUserEmailExist($emailaddress){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user where email=:email");
			$stmt->bindParam(':email', $emailaddress);

			$count = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$count++;
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
			throw new Exception('018');
		}

		if ($count > 0){
			return true;
		} else {
			return false;
		}
	}

	public function updateUserDetails($userid, $name, $surname, $username, $email, $gender, $cellphone, $location, $work, $birthday, $timezone){
		$user = $this->getUserById($userid);

		if (($this->isUsernameExist($username)) && ($user->username != $username)){
			throw new Exception('022');
		}
	
		$emailAddressUser = $this->getUserByEmailAddress($email);

		if (($this->isUserEmailExist($email)) && ($user->username != $emailAddressUser->username)){
			throw new Exception('035');
		}

		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "update user set username=:username, name=:name, surname=:surname, email=:email, cellphone=:cellphone, gender=:gender, location=:location, work=:work, birthday=:birthday, timezone=:timezone where id=:userid";
			error_log("SQL: ".$sql);
			$stmt = $con->prepare($sql);
			$stmt->bindParam(':userid', $userid);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':surname', $surname);
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':gender', $gender);
			$stmt->bindParam(':cellphone', $cellphone);
			$stmt->bindParam(':location', $location);
			$stmt->bindParam(':work', $work);
			$stmt->bindParam(':birthday', $birthday);
			$stmt->bindParam(':timezone', $timezone);

			$stmt->execute();

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('019');
		}
	}


	public function getUserById($userid){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user where id= :userid");
			$stmt->bindParam(':userid', $userid);

			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$user = new User($row["id"], $row["username"], $row["name"], $row["surname"], $row["email"], $row["cellphone"], $row["password"], 
							$row["gender"], $row["location"], $row["work"], $row["birthday"], $row["timezone"]);
				}
			}

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('027');
		}

		if (!isset($user)){
			throw new Exception('027');
		}

		return $user;
	}

	public function loginUser($username, $password){
		session_destroy();
		$result = false;
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("select * from user where username= :username and active is true");
			$stmt->bindParam(':username', $username);

			$returnUsername = "";
			$returnPassword = "";
			$userid = "";
			$active = 0;
			if ($stmt->execute()){
				while ($row = $stmt->fetch()){
					$returnUsername = $row["username"];
					$returnPassword = $row["password"];
					$userid = $row["id"];
					$active = $row["active"];
					$name = $row["name"];
					$fbToken = $row["fbToken"];
				}
			}

			error_log("retUSer: ".$returnUsername.", user:". $username);
			error_log("retPass: ".$returnPassword.", pass:". $password);
			if (($username == $returnUsername) && (md5($password, true) == $returnPassword )){

				if ($active == false){
					throw new Exception('024');
				}

				if (session_start()){
					$_SESSION["username"] = $username;
					$_SESSION["password"] = $password;
					$_SESSION["userid"] = $userid;
					$_SESSION["name"] = $name;
				}
				
				//return $_SESSION["name"];
				
				if (isset($fbToken)){
					$_SESSION["access_token"] = $fbToken;
				}
				
				$this->updateUserLoginDate($username);

				$result = true;

			} else {
				throw new Exception('002');
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			throw new Exception('001');
		}
		return $result;
	}

	private function updateUserLoginDate($username){
		try {
			$con = new PDO(GlobalConfig::db_pdo_connect_string, GlobalConfig::db_username, GlobalConfig::db_password);
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $con->prepare("update user set lastlogin=NOW() where username=:username");
			$stmt->bindParam(':username', $username);
			$stmt->execute();

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
		}
	}

	public function logoutUser($parameters){
		session_start();
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_SESSION['userid']);
		unset($_SESSION["name"]);
		unset($_SESSION['access_token']);

		session_destroy();

		$responder = new Responder;
		return $responder->constructResponse(null);
	}
	
	public function verifyUserSession($parameters){
		try {
			$reponseObject = $this->isUserLoggedIn($parameters);
		} catch (Exception $e) {
			throw new Exception('037');
		}
		
		foreach($reponseObject as $key => $value){
			if ($value != "true"){
				throw new Exception('037');
			}
		}
	}

}


?>