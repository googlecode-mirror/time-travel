<?php

class Logger{

	public static function log($output){
		file_put_contents((dirname(dirname(__FILE__)) ."/logs.txt"), date('Y-m-d G:i:s.u')." -- ".$output ."\n", FILE_APPEND | LOCK_EX);
	}
}

?>