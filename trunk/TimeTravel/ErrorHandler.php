<?php
class ErrorHandler {
	static function customError($errno, $errstr, $file, $line){
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting
			return;
		}

		switch ($errno) {
			case E_USER_ERROR:
				echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
				break;

			case E_USER_WARNING:
				echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
				break;

			case E_USER_NOTICE:
				echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
				break;

			default:
				echo "Unknown error type: [$errno] $errstr<br />\n";
				break;
		}

		/* Don't execute PHP internal error handler */
		return true;
	}

	public static function set(){
		set_error_handler("customError");
	}
}
?>