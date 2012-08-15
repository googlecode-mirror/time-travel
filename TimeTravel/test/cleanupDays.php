<?php
require_once(dirname(dirname(__FILE__)) .'/dao/DayDao.php');

$dayDAO = new DayDAO();
$days = $dayDAO->getAllDayId();

foreach ($days as $dayId){
	$dayDAO->deleteDayIfUnused($dayId);
}



?>