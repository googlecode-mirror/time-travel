<?php

date_default_timezone_set('Africa/Johannesburg');

class GlobalConfig
{
	const db_host_name  = 'localhost';
	const db_username  = 'root';
	const db_password  = 'Musn12nat';
	const db_name = 'journal';
	const db_pdo_connect_string  = 'mysql:dbname=journal;host=localhost';
	const facebook_url = "http://localhost/index.php?response=facebook";
	const forker_url  = "http://www.sabside.com/services/handleFork.php";
	const environment  = 'localhost';
	const googel_api_key = "AIzaSyBKdklWP-P35blJE2JZb2iwfEI4WqcdmiA";
}
?>