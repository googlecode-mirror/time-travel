<?php require_once dirname(__FILE__) . '/classes.php'; ?>
<?php 
$commonServicesInst = new CommonServices();
date_default_timezone_set('Europe/Minsk');
$module = $_REQUEST["mod"];
$filename = 'navigators/'.$module.'.php'; 
$moduleFile = $module.'.php';
$pageContext = array();
$pageContext["parm2"] = "parm2";
if (file_exists($filename)) { 
	include  $filename;
	$navName = $module . 'Navigator';
	$class = new ReflectionClass($navName);
	$classInstance = $class->newInstance();
	$classInstance->perform($_REQUEST,&$pageContext);
} else { 
 
}

include  $moduleFile; 
?>