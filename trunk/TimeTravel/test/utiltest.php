<?php
require_once(dirname(dirname(__FILE__)) .'/util.php');
require_once(dirname(dirname(__FILE__)) .'/services/DropboxService.php');
require_once(dirname(dirname(__FILE__)) .'/ImageResizer.php');


$service = new DropboxService();


error_reporting(E_ERROR | E_PARSE);
session_start();

$userid = $_SESSION['userid'];
$chosenDate = $_SESSION['chosenDate'];
$diplayDate = $_SESSION['diplayDate'];

$saveTo = (dirname(dirname(__FILE__))) . '/pictures/'. 'test1' .'/temp/' . '20120527_115207.jpg';
$thumbnail = (dirname(dirname(__FILE__))) . '/pictures/'. 'test1' .'/thumbnails/' . '20120527_115207.jpg';
$optimized = (dirname(dirname(__FILE__))) . '/pictures/'. 'test1' .'/optimized/' . '20120527_115207.jpg';
$main = (dirname(dirname(__FILE__))) . '/pictures/'. 'test1' .'/main/' . '20120527_115207.jpg';

$service->saveFileToDB($saveTo, '10');

$resizer = new ImageResizer($saveTo);

$resizer->resize(80, $thumbnail);
$resizer->resize(460, $optimized);
$resizer->resize(800, $main);

?>