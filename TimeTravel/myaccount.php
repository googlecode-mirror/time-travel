<?php 
	include("httpFilter.php"); 
	include("includes/libs.php");
	require_once "services/securityServices.php";  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	
<title>My Kagogo Profile</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

<link rel="stylesheet" type="text/css" media="screen" href="css/styles.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/navigation.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/verticalNav.css" />
<link rel="stylesheet" type="text/css" href="css/mbContainer.css" title="style"  media="screen"/>

<script type="text/javascript" src="plugins/mb/jquery.metadata.js"></script>

<script type="text/javascript" src="js/myaccount.js"></script>
<script type="text/javascript" src="js/util.js"></script>
<script type="text/javascript" src="js/validations.js"></script>

</head>
<body style="font-size: 75%">
 <?php include("includes/header.php"); ?> 
 <?php include("includes/menu.php"); ?>
 
 <div id="loadingPic" style="display: none; position:absolute; left: 0px; top:0px; height:100%; width: 100%; text-align: center;
           background-image:url('images/translucent.gif'); z-index: 1000;">
  <img src="images/loading2.gif" style="margin: 12%"/>
</div>
<br/><br/>

<table width="850" border="0">
	<tr id="message" style="display: none;"><td height="20" colspan="3" align="center" style="font-size: 1.2em;"></td></tr>
	<tr>
		<td align="left" valign="top">
			<table>
				<tr>
					<td id="menu8">
						<ul>
							<li><a href="#1" title="My Details" onclick="showMyDetails();">My Details</a></li>
							<li><a href="#1" title="Change Password" onclick="showChangePassword();">Change Password</a></li>
							<li><a href="#2" title="My Lifts" onclick="showMyLifts();">My Lifts</a></li>
							<li><a href="#3" title="Inbox"><b>Inbox (2)</b></a></li>
						</ul>
					</td>
				</tr>
			</table>
		</td>
		<td align="right" valign="top">
					<table id="mydetails" border="0" width="100%" cellspacing="0" cellpadding="1">
						<tr>
							<td class="formlabel">Username:</td>
							<td><input id="usernameinput" nicename="Username" class="forminput" validation="username" style="font-size: 16px;" type="text" maxlength="20" ></input></td>
						</tr>
						<tr>
							<td class="formlabel">First Name:</td>
							<td><input id="firstnameinput" nicename="Name" class="forminput" validation="mandatory" style="font-size: 16px;" type="text" maxlength="30" ></input></td>
						</tr>
						<tr>
							<td class="formlabel">Last Name:</td>
							<td><input id="lastnameinput" nicename="Surname" class="forminput" validation="mandatory" style="font-size: 16px;" type="text" maxlength="30" ></input></td>
						</tr>
						<tr>
							<td class="formlabel">Email address:</td>
							<td><input id="emailaddressinput" nicename="Email address" class="forminput" validation="email" style="font-size: 16px;" type="text" maxlength="50" ></input></td>
						</tr>
						<tr>
							<td class="formlabel">Cellphone:</td>
							<td><input id="cellphoneinput" nicename="Cellphone" class="forminput" validation="phonenumber" style="font-size: 16px;" type="text" maxlength="15"></input></td>
						</tr>
						<tr>
							<td class="formlabel">Facebook Page:</td>
							<td><input id="facebookpageinput" nicename="Facebook Page" class="forminput" style="font-size: 16px; width: 125;" type="text" maxlength="50"></input></td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							<td colspan="2" align="center">
								<button onclick="updateUserDetails();">Save</button>
							</td>
						</tr>
					</table>
					
					
					<table id="changePassword" border="0" width="100%" cellspacing="0" cellpadding="1" style="display: none;">
						<tr>
							<td class="formlabel">Current Password:</td>
							<td><input id="currentpasswordinput" nicename="Password" class="forminput" validation="password" style="font-size: 16px;" type="password" maxlength="20" ></input></td>
						</tr>
						<tr>
							<td class="formlabel">New Password:</td>
							<td><input id="newpasswordinput" nicename="Password" class="forminput" validation="password" style="font-size: 16px;" type="password" maxlength="20" ></input></td>
						</tr>
						<tr>
							<td class="formlabel">Confirm New Password:</td>
							<td><input id="passwordconfirminput" nicename="Password" class="forminput" validation="password" style="font-size: 16px;" type="password" maxlength="20" ></input></td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							<td colspan="2" align="center">
								<button onclick="updateMyPassword();">Update</button>
							</td>
						</tr>
					</table>
					
					 <table id="myLifts" width="100%" style="display: none;">
				    	<tbody>
					    	<tr id="liftHeader">
					    		<th class="subTitle">Date Posted</th>
					    		<th class="subTitle">Destination</th>
					    		<th class="subTitle">Lift Date</th>
					    		<th class="subTitle">Status</th>
					    		<th class="subTitle" width="1"><input type="checkbox" onchange="selectAllLifts(this);"/></th>
					    	</tr>
				    	</tbody>
				    	<tfoot>
				    		<th colspan="5" align="right">
				    			<button id="liftDeleteBtn" disabled="disabled">Delete</button>
				    			<button>Create New</button>
				    		</th>
				    	</tfoot>
				     </table>
					
		</td>
		<td width="50">
		</td>
	</tr>
</table>


<br/>
<?php include("includes/footer.php"); 
	include("includes/overlays.php"); 
?>
</body>
</html>