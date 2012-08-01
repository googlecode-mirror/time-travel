 <?php
 	require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');
 	require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
 	require_once(dirname(dirname(__FILE__)) .'/conf.php');
	date_default_timezone_set('Africa/Johannesburg');
 	
	$facebook_url =  GlobalConfig::facebook_url;

	error_reporting(E_ERROR | E_PARSE);
 	session_start();
 	
 	$hasFbToken = false;
 	$userid = $_SESSION['userid'];
	
 	if (isset($_SESSION['access_token'])){
 		$hasFbToken = true;
 	}
 
 ?>
<div id="fb-root"></div>
<script>
	$(document).ready(function(){

		var appsecret = "c164368cbf655e75496e0451b33d3da8";
		
		$("button").button();

		$("#getStatusBtn").click(function(){
			//var url = "https://www.facebook.com/dialog/oauth?client_id=172468146218420&redirect_uri=http://localhost/index.php?response=facebook&scope=email,user_birthday,status_update,publish_stream&response_type=token";
			var url = "https://www.facebook.com/dialog/oauth?client_id=172468146218420&redirect_uri=<?php echo$facebook_url?>&scope=user_birthday,status_update,read_stream,offline_access&state=authenticated";
			window.location = url;
		});

		if ((params.fbCode != "") && (params.fbToken == "")){
			var url = "https://graph.facebook.com/oauth/access_token?client_id=172468146218420&redirect_uri=<?php echo$facebook_url?>&client_secret="+ appsecret+"&code="+params.fbCode;
			$.post(url, null, function(resultData) {
		 		splitString = resultData.split("=");
		 		params.fbToken = splitString[1];
		 		saveTokenToSession(splitString[1]);
		 		$("#fbRegisterDiv").hide();
		 		$("#fbLoginDiv").hide();
		 	});
		}

		//getStatuses();
		showLoading(false);
	});
 
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '172468146218420', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    /* All the events registered */
    FB.Event.subscribe('auth.login', function(response) {
        // do something with response
        login();
    });
    FB.Event.subscribe('auth.logout', function(response) {
        // do something with response
        logout();
    });

    FB.getLoginStatus(function(response) {
        if (response.session) {
            // logged in and connected user, someone you know
            login();
        }
    });
    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
   }(document));


  function loginFBUser(){
	  FB.login(function(response) {
		   if (response.authResponse) {
		     console.log('Welcome!  Fetching your information.... ');
		     FB.api('/me', function(response) {
		       console.log('Good to see you, ' + response.name + '.');
		     });
		   } else {
		     console.log('User cancelled login or did not fully authorize.');
		   }
		 });
  }

	function saveTokenToSession(token){
		showLoading(true);
		var url = "controller.php";
		var parms = new Object();
		parms["action"] = "saveTokenToSession";
		parms["access_token"] = token;

		$.post(url, parms, function(resultData) {
			resultData = parseResult(resultData);
			var errorCode = $(resultData).find("code").text();
			if (errorCode == 0) {
				showLoading(false);
				alert("Your status updates are here now. Click on Random to see them.");
				window.reload();
			} if (errorCode == 2) {
				saveTokenToSession(token);
			} else {
				showLoading(false);
				showMessage($(resultData).find("errMessage").text(), "error");
			}
		});
	}


	function loginToFacebook(){
		showLoading(true);
		var url = "https://www.facebook.com/dialog/oauth?client_id=172468146218420&redirect_uri=<?php echo$facebook_url?>&scope=user_birthday,status_update,read_stream,offline_access&state=authenticated";
		window.location = url;
	}
  
</script>
<div id="fbStatus"></div>
<input id="fbUsername" type="hidden" value="sabside"/>
<!-- <button id="getStatusBtn">GEt Status</button> 
<button id="doQuery" onclick="fqlQuery();">doQuery</button>
<button  onclick="TokenToSession();">Get Statuses</button> -->

	<?php if (!$hasFbToken) {?>

	<div id="fbRegisterDiv" class="formlabel">
		You have not set up your Facebook account here. If you do, we can show you your status updates on the chosen date so that you may accurately reconstruct the day.
	</div>
	
	<br/><br/>
	<div id="fbLoginDiv" class="formlabel">
		Please login into your Facebook account below so that we can retrive your statuses.
			<br/><br/>
			
			<div><!-- <fb:login-button autologoutlink="true" perms="user_birthday,status_update,read_stream,offline_access"></fb:login-button> -->
			
			<fb:login-button size="medium"
                 onlogin="loginToFacebook();">
			  Connect with Facebook
			</fb:login-button>
						
		</div>
	</div>
	<?php }?>

	
	<?php
			$chosenDate = $_SESSION['chosenDate'];
			$diplayDate = $_SESSION['diplayDate'];
	
			$dayDAO = new DayDAO();
			$userDAO = new UserDAO();
			
			if (isset($_GET["dateText"])){
				$theDate = $_GET["dateText"];
				$dayToDisplay = $dayDAO->getIdForDay($userid, $theDate);
			} else {
				$dayToDisplay = $dayDAO->getRandomDayForStatusUpdate($userid);
			}
			
			$unformattedDate = $dayDAO->getDateForDayId($userid, $dayToDisplay);
			
			$chosenDate = date("Y-m-d", strtotime($unformattedDate));
			$diplayDate = date("Y F j l", strtotime($chosenDate));
			
			$_SESSION['chosenDate'] = $chosenDate;
			$_SESSION['diplayDate'] = $diplayDate;
			
			error_log($userid." : status update : ". $chosenDate);
		
			
			//Facebook Statues
			
			$statusUpdates = $userDAO->retrieveAllStatusUpdatesForDay($userid, $dayToDisplay);

			foreach ($statusUpdates as $statusUpdate){
				error_log("status update : ". $statusUpdate->message);
	?>
			
			<script type="text/javascript">
				params.chosenDate = "<?php echo$chosenDate?>";
				params.diplayDateInput = "<?php echo$diplayDate?>";
			</script>
			
			<div class="formlabel">
			 <div style="font-size: 1.1em; text-align: left;">"<?php echo $statusUpdate->message ?>"</div><br/>
			 <div style="font-size: 0.8em; text-align: right;"><?php echo date("g:i a", strtotime($statusUpdate->theDate)) ?></div>
			</div>
			
		<?php }?>

