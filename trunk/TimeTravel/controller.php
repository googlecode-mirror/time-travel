<?php
require_once 'conf.php';
require_once "services/securityServices.php";
require_once "services/BusinessLogic.php";
require_once "errorCodes.php";
require_once "dto/Action.php";
require_once "ErrorHandler.php";

//setCustomError();
$errMessage = "";
$errCode = 0;
try {
	error_reporting(E_ERROR | E_PARSE);
	session_start();

	$actionName = $_REQUEST["action"];
	$action = new Action($actionName);

	error_log("SERVICE REQUESTED ::::: ".$action->serviceClass);

	$class = new ReflectionClass($action->serviceClass);
	$classInstance = $class->newInstance();

	$params = $action->parameters;

	$parameters = array();

	//We check if the action if for prelogon then check if the user is logged in
	if ($action->postlogon == "true"){
		//We need to send the userId for every call.
		if (isset($_SESSION['userid'])){
			$parameters["userid"] = $_SESSION["userid"];
			error_log("userId: ". $parameters["userid"]);
		} else {
			throw new Exception("026");
		}
	}

	foreach ($params as $param){
		$required = $param->required;

		if (($required == "true") && (isset($_REQUEST[$param->name]))){
			$parameters[$param->name] = trim($_REQUEST[$param->name]);
		} else if (($required == "false") && (isset($_REQUEST[$param->name]))){
			$parameters[$param->name] = trim($_REQUEST[$param->name]);
		} else if (($required == "false") && (!isset($_REQUEST[$param->name]))){
			$parameters[$param->name] = "";
		}

		error_log("PARAM: ".$param->name." VALUE: ".$parameters[$param->name]);
	}


	$responder = new Responder;

	$response = $classInstance->$actionName($parameters);

	if (is_string($response)){
		echo $response;
	} else {
		echo $responder->constructResponse($response);
	}

	if (isset($_SESSION['mobile'])){
		session_destroy();
	}

} catch (Exception $e) {
	$responder = new Responder;
	$errorCodes = new ErrorCodes;
	$errMessage = $errorCodes->getErrorMessage($e->getMessage());
	error_log("Error Message: " . $errMessage);
	echo $responder->constructErrorResponse($errMessage);

}

function customError($errno, $errstr, $file, $line){
	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting
		error_log($errstr);
		return;
	}

	switch ($errno) {
		case E_USER_ERROR:
			error_log("Fatal error on line $errline in file $errfile $errstr");
			break;

		case E_USER_WARNING:
			error_log("Warning on line $errline in file $errfile $errstr");
			break;

		case E_USER_NOTICE:
			error_log("Notice on line $errline in file $errfile $errstr");
			break;

		default:
			error_log("Default error!!!");
			break;
	}

	/* Don't execute PHP internal error handler */
	return true;
}

function setCustomError(){
	set_error_handler("customError");
}

?>