<?php

class Communication {

	public $id;
	public $title;
	public $body;
	public $timestamp;
	public $from;
	public $recipient;
		
	public function __construct($id, $title, $body, $timestamp, $from, $recipient)  {
		$this->id = $id;
		$this->title = $title;
		$this->body = $body;
		$this->timestamp = $timestamp;
		$this->from = $from;
		$this->recipient = $recipient;
	}
}

?>