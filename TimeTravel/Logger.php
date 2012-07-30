<?php

class Logger{

	public static function log($output){
		file_put_contents(dirname(__FILE__). "/logs/logs.txt", date('Y-m-d G:i:s.u')." -- ".$output ."\n", FILE_APPEND | LOCK_EX);
	}
}

?>