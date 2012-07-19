<?php

class User {

	public $id;
	public $username;
	public $name;
	public $surname;
	public $email;
	public $cellphone;
	public $gender;
	public $location;
	public $work;
	public $birthday;
	public $timezone;
	
	public function __construct($id, $username, $name, $surname, $email, $cellphone, $password, $gender, $location, $work, $birthday, $timezone)  {
		$this->id = $id;
		$this->username = $username;
		$this->name = $name;
		$this->surname = $surname;
		$this->email = $email;
		$this->cellphone = $cellphone;
		$this->gender = $gender;
		$this->location = $location;
		$this->work = $work;
		$this->birthday = $birthday;
		$this->timezone = $timezone;
	}
}

?>
