<?php

class Picture {

	public $id;
	public $description;
	public $location;
	public $timetaken;
	public $fileType;
	public $payload;
	public $filename;
	public $latitude;
	public $longitude;
	public $dayId;
	public $sharerUsername;

	public function __construct($id, $description, $location, $timetaken, $fileType, $payload, $filename)  {
		$this->id = $id;
		$this->description = $description;
		$this->location = $location;
		$this->timetaken = $timetaken;
		$this->fileType = $fileType;
		$this->payload = $payload;
		$this->filename = $filename;
	}
}

?>