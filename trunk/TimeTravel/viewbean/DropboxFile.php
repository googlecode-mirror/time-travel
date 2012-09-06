<?php

class DropboxFile {

	public $is_dir;
	public $last_update;
	public $name;

	public function __construct($is_dir, $last_update, $name)  {
		$this->is_dir = $is_dir;
		$this->last_update = $last_update;
		$this->name = $name;
	}
}

?>