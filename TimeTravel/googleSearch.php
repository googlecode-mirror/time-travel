<?php 
	
//include("includes/libs.php");  

$searchTerm = $_POST["searchTerm"];
$searchResult = $_POST["searchResult"];

$fileName = "c:\\temp\\research.csv";

$handle = fopen($fileName, "a") or die("can't open file");;

fwrite($handle, str_replace('\"', "", $searchTerm).",".  str_replace(',', "", $searchResult) . "\n");

fclose($handle);

?>
