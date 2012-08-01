<?php
require_once(dirname(__FILE__) .'/Logger.php');

/* $link = mysql_connect('db2.swh.mweb.net','m8182230','bxfufvx8');
	

if (!$link) {
	die('Could not connect: ' . mysql_error());
} else {
	echo "connected to server </br></br>";
}


$db_selected = mysql_select_db('cldbhost_m8182230', $link);
if (!$db_selected) {
	die ('Can\'t use db : ' . mysql_error());
} else {
	echo "connected to db";
}


$query = "select * from user";
$result=mysql_query($query);

$num=mysql_numrows($result);

$i=0;
while ($i < $num) {

	echo $i;

	$i++;
}

mysql_close();
//$response = file_get_contents("https://graph.facebook.com/me/statuses?access_token=".  "AAACc296XJbQBAFAHn8CHr05QivImaNQTIzXFos6MHLjc9vXX4XDivMgTsLbs3b5LJpO9yJ16YGKVkh4dDdkGrox2bmqlZB38hRnEslAZDZD");
$response = file_get_contents("http://16thnote.co.za");
 */

sleep(5);
$response = file_get_contents("https://graph.facebook.com/me/statuses?access_token=".  "AAACc296XJbQBAFAHn8CHr05QivImaNQTIzXFos6MHLjc9vXX4XDivMgTsLbs3b5LJpO9yJ16YGKVkh4dDdkGrox2bmqlZB38hRnEslAZDZD");
Logger::log("OUPUT : ".$response);
?>