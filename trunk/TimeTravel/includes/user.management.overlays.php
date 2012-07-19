<div id="signUpForm" style="display: none;">
	<table border="0" width="100%" cellspacing="0" cellpadding="1">
		<tr><td colspan="2" style="color: #0E385F; font-size: 15px;">Sign up with Gogo. It's free!</td></tr>
		<tr>
			<td colspan="2"><hr width="100%" color="#0E385F" size="1px"></hr></td>
		</tr>
		<tr>
			<td class="formlabel">Username:</td>
			<td><input id="usernameinput" nicename="Username" class="forminput" validation="username" style="font-size: 16px;" type="text" maxlength="20"></input></td>
		</tr>
		<tr>
			<td class="formlabel">First Name:</td>
			<td><input id="firstnameinput" nicename="Name" class="forminput" validation="mandatory" style="font-size: 16px;" type="text" maxlength="30"></input></td>
		</tr>
		<tr>
			<td class="formlabel">Last Name:</td>
			<td><input id="lastnameinput" nicename="Surname" class="forminput" validation="mandatory" style="font-size: 16px;" type="text" maxlength="30"></input></td>
		</tr>
		<tr>
			<td class="formlabel">Email address:</td>
			<td><input id="emailaddressinput" nicename="Email address" class="forminput" validation="email" style="font-size: 16px;" type="text" maxlength="50"></input></td>
		</tr>
		<tr>
			<td class="formlabel">Confirm email:</td>
			<td><input id="emailaddressconfirminput" nicename="Email address" class="forminput" validation="email" style="font-size: 16px;" type="text" maxlength="50"></input></td>
		</tr>
		<tr>
			<td class="formlabel">Password:</td>
			<td><input id="passwordinput" nicename="Password" class="forminput" validation="password" style="font-size: 16px;" type="password" maxlength="20"></input></td>
		</tr>
		<tr>
			<td class="formlabel">Confirm Password:</td>
			<td><input id="passwordconfirminput" nicename="Password" class="forminput" validation="password" style="font-size: 16px;" type="password" maxlength="20"></input></td>
		</tr>
	</table>
</div>

<div id="loginDlg" style="display: none;">
	<table width="100%" border="0" style="font-size: 1.0em">
		<tr>
			<td colspan="2"><span id="infoMsg" style="display: none; color: red;"> <img src="images/information.png" width="20" /> &nbsp;You need to
					login to see the details of this lift.
			</span>
			</td>
		</tr>
		<tr>
			<td class="formlabel">Username:</td>
			<td><input id="username" nicename="Username" type="text" autocomplete="off" class="forminput" validation="username"
				style="font-size: 16px;" maxlength="20" /></td>
		</tr>
		<tr>
			<td class="formlabel">Password:</td>
			<td><input id="password" nicename="Password" type="password" class="forminput" validation="password" style="font-size: 16px;"
				maxlength="20" /></td>
		</tr>
		<tr>
			<td></td>
			<td style="font-size: 1em; color: grey;"><br /> <a href="#" class="underlineLink" onclick="showForgotPasswordDlg();">Forgot Password?</a>&nbsp;&nbsp;&nbsp;
				<a href="#" class="underlineLink" onclick="showSignUp();">Sign Up, it's free!</a>
			</td>
		</tr>
	</table>
</div>


<div id="forgotPasswordDlg" style="display: none;">
	<table width="100%" border="0" style="font-size: 1.0em">
		<tr>
			<td align="center"><img src="images/email.gif" width="32" height="32" /> &nbsp; Enter your email address or username.</td>
		</tr>
		<td align="center"><input id="identify_email" nicename="Password or Email" type="text" validation="mandatory"
			class="fbForgotPasswordInput"></input></td>
		</tr>
	</table>
</div>

<div id="compulsoryLoginDlg" style="display: none;">
	<table width="100%" border="0" style="font-size: 1.0em">
		<tr>
			<td align="right"><img src="images/information.png" width="30" /> &nbsp;&nbsp; You need to login to see the details of this lift. Do you
				want to login?</td>
		</tr>
		<td align="right"><br /> <a href="#" onclick="showForgotPasswordDlg();">I forgot my password.</a> &nbsp;&nbsp; <a href="#"
			onclick="showSignUp();">I do not have an account.</a></td>
		</tr>
	</table>
</div>
