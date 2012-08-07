<?php

class Location {

	public $timestamp;
	public $longitude;
	public $latitude;

	public function __construct($timestamp, $longitude, $latitude)  {
		$this->timestamp = $timestamp;
		$this->longitude = $longitude;
		$this->latitude = $latitude;
	}
}

?>