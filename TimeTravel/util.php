<?php
date_default_timezone_set('Europe/Minsk');

class Util{

	function ShortenString($text, $size) {
		// Change to the number of characters you want to display
		if (strlen($text) > $size){
			$text = substr($text,0, $size);
			$text = $text."...";
		}

		return $text;
	}


	function getPercentage($numerator, $denominator){
		if (($numerator == 0) || ($denominator == 0)){
			return 0;
		} else {
			return round(($numerator/$denominator)* 100);
		}
	}

	public function formatDate($date){
		return date("l, d F Y", strtotime($date));
	}

	public function hashString($inputString){
		return md5($inputString);
	}

	public static function getFriendlyDate($inputDate){
					
	}
	
	/**
	 * Used to extract the name of the person an sms (gmail)
	 */
	public static function getSourceName($string){
		$end = strpos($string, '<');
		$sender = trim(substr($string, 0, $end));
		if ($sender === ""){
			return "Me"	;
		} else {
			return trim(substr($string, 0, $end));
		}
		
		/* if (strpos($string, '"') === false){
			echo strpos($string, '"');
			$source = split(" ", $string);
			return $source[0];
		} else if (substr_count($string, '"') == 2) {
			$start = strpos($string, '"');
			$end = strrpos($string, '"')+1;
			return substr($string, $start, $end);
		} else {
			$from = split('"', $string);
			if(strpos($string, '"') == 0){
				return $from[1];
			} else {
				return $from[0];
			}
		} */
	}
}

?>