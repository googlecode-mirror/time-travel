<?php
require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
require_once(dirname(dirname(__FILE__)) .'/conf.php');
require_once(dirname(dirname(__FILE__)) . '/dao/GmailDAO.php');
date_default_timezone_set('Africa/Johannesburg');


error_reporting(E_ERROR | E_PARSE);
session_start();

$userid = $_SESSION['userid'];
$chosenDate = $_SESSION['chosenDate'];
$diplayDate = $_SESSION['diplayDate'];

$dayDAO = new DayDAO();
	
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

?>
<script type="text/javascript">
	$(document).ready(function(){
		$("button").button();
	});


	function saveGmailDetails(){
		if (!validateInputs($("#gmailSignUpForm"))) {
			return false;
		}
		showLoading(true);
		var url = "controller.php";
		var parms = new Object();
		parms["action"] = "saveGmailDetails";
		parms["gmailusername"] = $("#gmailUsernameinput").val();
		parms["gmailpassword"] = $("#gmailPasswordinput").val();

		$.post(url, parms, function(resultData) {
			resultData = parseResult(resultData);
			var errorCode = $(resultData).find("code").text();
			if (errorCode == 0) {
				displayEmailFolders(resultData);
			} else {
				alert($(resultData).find("errMessage").text());
			}
			showLoading(false);
		});
	}

	function displayEmailFolders(data){
		$("#gmailFolderList").filter(".folder").remove();
		$(data).find("list").children().each(function(){
			$('<tr class="folder"><td class="ui-widget-content"><input type="checkbox"/></td><td class="formlabel ui-widget-content">'+ $(this).html() +'</td></tr>').insertAfter($("#gmailFolderList-head"));
		})
		
		$("#gmailFolderList").show();
		$("#gmailFolderList").dialog( {
			bgiframe : true,
			autoOpen : false,
			width : 600,
			height : 600,
			position: ['center'],
			modal : true,
			resizable : false,
			title : "Choose folders to synchronize",
			buttons : {
				Submit : function() {
					$(this).dialog("destroy");
					showLoading(true);
					submitFolderList();
				},
				Cancel : function() {
					$(this).dialog("destroy");
				}
			}
		});

		$("#gmailFolderList").dialog("open");
	}

	function submitFolderList(){
		var folderList = "";
		$("#gmailFolderList").find("input[type='checkbox']:checked").each(function(){
			folderList += $(this).parent().next().html() +",";
			//alert($(this).parent().next().html());
		});
		console.log("FOLDERS: "+folderList);
		var url = "controller.php";
		var parms = new Object();
		parms["action"] = "saveProviderFolders";
		parms["type"] = 'call_gmail';
		parms["folders"] = folderList;
		parms["syncStart"] = $("#syncStartYear").val()+"-"+$("#syncStartMonth").val()+"-01";

		$.post(url, parms, function(resultData) {
			resultData = parseResult(resultData);
			var errorCode = $(resultData).find("code").text();
			if (errorCode == 0) {
				alert("Thank you! We are now pulling your calls from your provider. They will be available shortly.");
			} else {
				alert($(resultData).find("errMessage").text());
			}
			showLoading(false);
		});
	}
</script>

<br/><br/>

<?php 
			$gmailDAO = new GmailDAO();
			$userSubscribedForCalls = $gmailDAO->hasUserSetupContentUpdate($userid, 'call');
			error_log("SUB: ".$userSubscribedForSms);
			if (!$userSubscribedForCalls){
				
				
	?>
<div>
	<img src="/images/gmail.png" width="80"></img>
</div>
<br/><br/>
<div id="gmailRegisterDiv" class="formlabel">You have not set up your CALL account provider yet. If you do, we can show you your emails, SMS, and even calls on
	the chosen date so that you may accurately reconstruct the day.</div>

<br />
<br />
	<div id="gmailSignUpForm" style="display: block;">
	<table border="0" width="100%" cellspacing="0" cellpadding="1">
		<tr><td colspan="2" style="color: #0E385F; font-size: 15px;">Please enter your GMail details below.</td></tr>
		<tr>
			<td colspan="2"><hr width="100%" color="#0E385F" size="1px"></hr></td>
		</tr>
		<tr>
			<td class="formlabel">Gmail Username:</td>
			<td><input id="gmailUsernameinput" nicename="Username" class="forminput" validation="email" style="font-size: 16px;" type="text" maxlength="20"></input></td>
		</tr>
		<tr>
			<td class="formlabel">Gmail Password:</td>
			<td><input id="gmailPasswordinput" nicename="Password" class="forminput" validation="password" style="font-size: 16px;" type="password" maxlength="20"></input></td>
		</tr>
		<tr>
			<td></td>
			<td><button onclick="saveGmailDetails();">Hook me up</button></td>
		</tr>
	</table>

	</div>
	<div>
<?php }	 else { 
	
	$callList = $gmailDAO->getCommunicationContentForDay($dayToDisplay, "call");
	$callList = array_reverse($smsList);
	foreach ($callList as $call){
 ?>
 	<div style="position: relative; left: 0px; float: left;"><?php echo $call->from?></div>
 	<div style="position: relative; left: 0px; float: right;"><?php echo date("H:m:s", strtotime($call->timestamp))?></div>
 	<hr width="100%" size="1">
 	<p class="formlabel" style="text-align: left; background-color: #FAF5F5;"><?php echo $call->body?></p>
 
 <?php }
 
}?>
 	</div>
</div>