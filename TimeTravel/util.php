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
}

?>