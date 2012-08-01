<?php 
require_once(dirname(dirname(__FILE__)) .'/services/BusinessLogic.php');
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) .'/Logger.php');

class Forker {

	/**
	 * ACTIONS TO BE HANDLED BY FORKER
	 */
	public static $GET_STATUS_UPDATES = "getStatusUpdates";

	/**
	 * Handle your processing here. We will run the whatever method in class based on the action you have set in the request
	 * @param unknown_type $parameterMap
	 */
	static function handleProcessing($parameterMap){
		$action = "";
		if (isset($parameterMap['action'])){
			$action = $parameterMap['action'];
		} else {
			Logger::log("ACTION NOT SET!!!");
		}

		Logger::log("Action: ".$action);

		$busLogic = new BusinessLogic();
		if ($action == self::$GET_STATUS_UPDATES){
			$busLogic->getStatusUpdatesForUser($parameterMap['userid']);
		} else if ($action == "rotateImage"){
			$busLogic->doImageRotate($parameterMap);
		}
	}

	/**
	 * DO NOT TOUCH THIS. NOT BUSINESS LOGIC!!!
	 * @param unknown_type $params
	 */
	static function doPost($params)	{
		if (GlobalConfig::environment == "localhost"){
			Logger::log("handling procesing locally...");
			self::handleProcessing($params);
			return;
		}

		if (GlobalConfig::forker_url != null) {
			$url = GlobalConfig::forker_url;
		} else {
			Logger::log("You need to set the forker URL in the conf file.");
			return false;
		}

		foreach ($params as $key => &$val) {
			if (is_array($val)) $val = implode(',', $val);
			$post_params[] = $key.'='.urlencode($val);
		}
		$post_string = implode('&', $post_params);

		$parts=parse_url($url);

		$fp = fsockopen($parts['host'],
				isset($parts['port'])?$parts['port']:80,
				$errno, $errstr, 30);

		//pete_assert(($fp!=0), "Couldn't open a socket to ".$url." (".$errstr.")");

		$out = "POST ".$parts['path']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: ".strlen($post_string)."\r\n";
		$out.= "Connection: Close\r\n\r\n";
		if (isset($post_string)) $out.= $post_string;

		fwrite($fp, $out);
		fclose($fp);
	}

}

?>