<?php

require_once(dirname(dirname(__FILE__)) .'/services/Forker.php');
require_once(dirname(dirname(__FILE__)) .'/Logger.php');

Logger::log("actioning request...");


Forker::handleProcessing($_REQUEST);

/* $actionGetStatus = Forker::$GET_STATUS_UPDATES;

$action = "";
if (isset($_REQUEST['action'])){
	$action = $_REQUEST['action'];
} else {
	Logger::log("ACTION NOT SET!!!");
	exit();
}

Logger::log("Action: ".$action);

if ($action == $actionGetStatus){
	$busLogic = new BusinessLogic();
	$busLogic->getStatusUpdatesForUser($_REQUEST['userid']);
} */
 
echo "DONE!"
?>