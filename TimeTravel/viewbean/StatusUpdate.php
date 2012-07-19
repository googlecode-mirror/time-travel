<?php

class StatusUpdate {

	public $id;
	public $theDate;
	public $message;

	public function __construct($id, $theDate, $message)  {
		$this->id = $id;
		$this->theDate = $theDate;
		$this->message = $message;
	}
}

?>