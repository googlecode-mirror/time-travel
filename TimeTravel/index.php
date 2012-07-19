<?php 
require_once "includes/libs.php";
require_once 'includes/user.session.initializer.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	
<title>Welcome to Time Travel</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

<link rel="stylesheet" type="text/css" media="screen" href="css/styles.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/navigation.css" />
<link rel="stylesheet" type="text/css" href="css/mbContainer.css" title="style"  media="screen"/>

<script type="text/javascript" src="plugins/mb/jquery.metadata.js"></script>

<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript" src="js/user.management.js"></script>
<script type="text/javascript" src="js/application.specific.util.js"></script>
<script type="text/javascript" src="js/validations.js"></script>
<script type="text/javascript" src="plugins/mb/mbContainer.js"></script>

<style type="text/css">
A:link {text-decoration: none}
A:visited {text-decoration: none}
A:active {text-decoration: none}
A:hover {text-decoration: underline;}
</style>

</head>
<body style="font-size: 75%" >
 <?php 	include_once ("includes/header.php"); 
 		include_once ("includes/application.specific.overlays.php");
 	?>
 <?php 
 
 	$facebookAccessToken = "";
 	$mainContentType = "";
 	$facebookCode = "";
 	
 	if (isset($_GET["response"])){
 		$mainContentType = $_GET["response"];
 	}
 
 	if (isset($_SESSION["access_token"])){
 		$facebookAccessToken = $_SESSION["access_token"];
 		error_log("fbToken is session ".$facebookAccessToken);
 		$facebookCode = "";
 	} else if (isset($_GET["code"])){
 		$facebookCode = $_GET["code"];
 	}

 	
 	$chosenDate = $_SESSION['chosenDate'];
 	$diplayDate = $_SESSION['diplayDate'];
 
 ?>
 <input id="chosenDate" type="hidden" value="<?=$chosenDate?>"/>
 <input id="diplayDateInput" type="hidden" value="<?=$diplayDate?>"/>
 <input id="mainContentType" type="hidden" value="<?=$mainContentType?>"/>
 <input id="fbToken" type="hidden" value="<?=$facebookAccessToken?>"/>
 <input id="fbCode" type="hidden" value="<?=$facebookCode?>"/>
 
 <div id="loadingPic" style="display: none; position:absolute; left: 0px; top:0px; height:100%; width: 100%; text-align: center;
           background-image:url('images/translucent.gif'); z-index: 1000;">
  <img src="images/loading2.gif" style="margin: 12%"/>
</div>

<div id="globalContaier" style="width: 100%; position: absolute; top: 40px;" align="center">
		<div id="loggedInContentAread" style="width: 850px; display: <?=$loggedIn? "block" : "none" ?>;">
			<div id="addInfoDiv" style="float: left; text-align: right;">
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=pictures">PICTURES</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=facebook" >STATUS UPDATES</a></div>
			</div>
			<div style="float: left; border-left:1px solid #E0E0E0; height: 600px; padding-left: 10px; width: 55%;">
				<br/>
				<div id="contentArea1"></div>
			</div>

			<div style="float: right; right: 0px; width: 30%; border-left:1px solid #E0E0E0;">
				<br/>
				<div id="contentArea2" align="left" style="padding-left: 15px;"></div>

			</div>
		</div>
		
		
		<div id="loggedInContentAread" style="width: 850px; display: <?=$loggedIn? "none" : "block" ?>;">
			<br/><br/><br/><br/>
			<div class="formlabel" height="400px">
				Welcome to Time Travel! If you don't have an account you might wanna signup to start travelling through time in your lifetime. Or, if you 
				already have an account, login above to experience some dejavu!
			</div>
		</div>
		
	</div>

<?php 
	include("includes/user.management.overlays.php");
	
	//include("includes/footer.php");
?>

</body>
</html>