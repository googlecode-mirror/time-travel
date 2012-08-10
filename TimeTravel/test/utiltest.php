<?php
require_once(dirname(dirname(__FILE__)) .'/util.php');

$myString = 'Innocent Pereira <innocent.pereira@gmail.com>';

echo Util::getSourceName($myString);

?>