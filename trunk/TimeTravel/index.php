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
 <?php	include_once ("includes/header.php"); 
 		include_once ("includes/application.specific.overlays.php");
 		date_default_timezone_set('Africa/Johannesburg');
 
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
	
 	if (isset($_SESSION['chosenDate'])){
	 	$chosenDate = $_SESSION['chosenDate'];
	 	$diplayDate = $_SESSION['diplayDate'];
 	} else {
 		$chosenDate = date('Y-m-d');
 		$diplayDate = date('Y-m-d G:i:s.u');
 	}
 
 ?>
 
 <script type="text/javascript">
	var params = {};
	params.chosenDate = "<?php echo$chosenDate?>";
	params.diplayDateInput = "<?php echo$diplayDate?>";
	params.mainContentType = "<?php echo$mainContentType?>";
	params.fbToken = "<?php echo$facebookAccessToken?>";
	params.fbCode = "<?php echo$facebookCode?>";
	params.username = "<?php echo $username?>";
 </script>
 
 <div id="loadingPic" style="display: none; position:absolute; left: 0px; top:0px; height:100%; width: 100%; text-align: center;
           background-image:url('images/translucent.gif'); z-index: 1000;">
  <img src="images/loading2.gif" style="margin: 12%"/>
</div>

<div id="globalContaier" style="width: 100%; position: absolute; top: 40px;" align="center">
		<div id="loggedInContentAread" style="width: 870px; display: <?php echo $loggedIn? "block" : "none" ?>;">
			<div id="addInfoDiv" style="float: left; text-align: right;">
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=pictures">PICTURES</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=facebook" >STATUS UPDATES</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=email" >EMAILS</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=sms" >SMS's</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=calls" >PHONE CALLS</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=calls" >GEO LOCATION</a></div>
				<div style="font-size: 0.9em; color: #999; font-weight: bold; margin-top: 12px; padding-right: 15px;"><a href="index.php?response=dropbox" >DROPBOX</a></div>
			</div>
			<div style="float: left; border-left:1px solid #E0E0E0; height: 650px; padding-left: 10px; width: 56%;">
				<br/>
				<div id="contentArea1" style="height: 700px; overflow: hidden; overflow-y: scroll; padding-right: 10px;"></div>
			</div>

			<div style="float: right; right: 0px; border-left:1px solid #E0E0E0; width: 28%;">
				<br/>
				<div id="contentArea2" align="left" style="padding-left: 5px;"></div>

			</div>
		</div>
		
		
		<div id="loggedInContentAread" style="width: 850px; display: <?php echo$loggedIn? "none" : "block" ?>;">
			<br/><br/><br/><br/>
			<div class="formlabel" height="400px" style="font-size: 1.2em">
				<p>The whole idea of taking pictures is to capture the moment so that you may reminisce about it years after it was taken. But, why do you have them all over the place? Think about the 
					most precious pictures taken when you were growing up...what happened to them? What about the ones you took on that vacation 5 years ago? 
					"I have them somewhere...", I hear you say. Besides the fact that you might lose them, when do you ever see them? When you stumble across them in your old laptop while 
					looking for your CV?</p>
					
					<p>Well, Timetravel gives you a place where you can upload all your precious memories into one portal.... If you don't have an account you might wanna signup to start travelling through time in your lifetime. Or, if you 
				already have an account, login above to experience some dejavu!</p>
			</div>
		</div>
		
	</div>

<?php 
	include("includes/user.management.overlays.php");
	
	//include("includes/footer.php");
?>
<div id="genericOverlay" style="display: none;"></div>
</body>
</html>