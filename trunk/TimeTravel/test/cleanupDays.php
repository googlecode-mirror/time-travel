<?php
require_once(dirname(dirname(__FILE__)) .'/dao/DayDAO.php');

$dayDAO = new DayDAO();
$days = $dayDAO->getAllDayId();

foreach ($days as $dayId){
	echo "checking ".$dayId."... <br/>";
	$dayDAO->deleteDayIfUnused($dayId);
}



?>