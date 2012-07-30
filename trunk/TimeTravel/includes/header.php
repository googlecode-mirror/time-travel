<table border="0" style="width: 100%; background-color: #C80000; border-bottom :1px solid #E0E0E0; margin: 0px; padding: 0px; position:absolute; top:0; left:0; height: 40px;">
	<tr>
		<td></td>
		<td align="center" width="40%">
		<?php if (isset($_SESSION['name'])) {	?>
			<div style="font-size: 1em; color: white; font-style: italic; float: left; display: block;"><?php echo $name?>, enjoy your time travel!</div>
		<?php } ?>
			<div style="color: white; font-size: 1.6em; font-weight: bold; font-family: verdana;">Time Travel<div>
			
		</td>
		<td width="15%" align="center">
		
		<?php
			if (!isset($_SESSION['name'])) {
		?>
			<a id="loginUrl" href="#" class="header" onclick="showLoginDlg();"><img src="images/login.png" border="0"/>Login</a>
		<?php   } else { ?>

			<a id="logoutUrl" href="#" class="header" onclick="logoutUser(fbUserLogout);"><img src="images/logout.png" border="0"/>Logout
			</a>
		<?php } ?>
		
		
		
		</td>
		<td width="15%"  align="center">	
			<a href="#" class="header" onclick="showSignUp();" style="display: <?php echo $loggedIn ? 'none' : 'block'?>;"><img src="images/signup.png" border="0"/>Sign up</a>
		</td>
		<td ></td>
	</tr>
	
</table>