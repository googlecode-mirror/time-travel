function fetchUserDetails(userid){
	calls++;
	showLoading(true);
	
	//reset
	$("#myPicture").html('<img src="images/loading3.gif"/>');
	$("#userName").html("");
	
	var url="controller.php";
	var parms = new Object();
	parms["action"]= "getUserById";
	parms["userid"]= userid;

	$.post(url,parms,function(resultData){
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if(errorCode == 0){
			displayUserDetails(resultData);
	} else {
			showLoading(false);
			alert($(resultData).find("errMessage").text());
		} 
	});
}

function showForgotPasswordDlg() {
	resetValidationMessages($("#forgotPasswordDlg"));
	$("#compulsoryLoginDlg").dialog("destroy");
	$("#loginDlg").dialog("destroy");
	$("#identify_email").val("");
	$("#forgotPasswordDlg").dialog( {
		bgiframe : true,
		autoOpen : false,
		width : 400,
		height : 200,
		modal : true,
		resizable : false,
		title : "Kagogo.co.za",
		buttons : {
			Cancel : function() {
				$(this).dialog("destroy");
			},
			Search : function() {
				doPasswordForgot();
			}
		}
	});
	$("#forgotPasswordDlg").dialog("open");
}

function doPasswordForgot() {
	if (!validateInputs($("#forgotPasswordDlg"))) {
		return false;
	}

	showLoading(true);
	$("#forgotPasswordDlg").dialog("destroy");
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "doForgotPassword";
	parms["identify_email"] = $("#identify_email").val();

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			showLoading(false);
			$("#forgotPasswordDlg").dialog("destroy");
			alert($(resultData).find("errMessage").text());
		} else {
			showLoading(false);
			$("#forgotPasswordDlg").dialog("destroy");
			alert($(resultData).find("errMessage").text());
		}
	});
}

function createUser() {

	if (!validateInputs($("#signUpForm"))) {
		return false;
	}

	if ($("#emailaddressinput").val() != $("#emailaddressconfirminput").val()) {
		alert("Email addresses do not match.");
		return false;
	}

	if ($("#passwordinput").val() != $("#passwordconfirminput").val()) {
		alert("Passwords do not match.");
		return false;
	}

	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "createUser";
	parms["username"] = $("#usernameinput").val();
	parms["password"] = $("#passwordinput").val();
	parms["email"] = $("#emailaddressinput").val();
	parms["name"] = $("#firstnameinput").val();
	parms["surname"] = $("#lastnameinput").val();

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			$("#signUpForm").dialog("destroy");
			alert("Your account has been created successfully.");
		} else {
			$("#signUpForm").dialog("destroy");
			alert($(resultData).find("errMessage").text());
		}
	});
}

function showCompulsoryLoginDlg() {
	$("#compulsoryLoginDlg").dialog( {
		bgiframe : true,
		autoOpen : false,
		width : 510,
		height : 200,
		modal : true,
		resizable : false,
		title : "Kagogo.co.za",
		buttons : {
			Cancel : function() {
				$(this).dialog("destroy");
			},
			Login : function() {
				showLoginDlg();
			}
		}
	});
	$("#compulsoryLoginDlg").dialog("open");
}

function showSignUp() {
	$("#compulsoryLoginDlg").dialog("destroy");
	resetValidationMessages($("#signUpForm"));
	$("#loginDlg").dialog("close");
	clearSignUpFields();
	$("#signUpForm").dialog( {
		bgiframe : true,
		autoOpen : false,
		width : 400,
		height : 480,
		modal : true,
		resizable : false,
		title : "Kagogo.co.za",
		buttons : {
			Cancel : function() {
				$(this).dialog("destroy");
			},
			'Sign Up' : function() {
				createUser();
			}
		}
	});
	$("#signUpForm").dialog("open");
}

var loginUser = function userLogin(redirectUrl) {

	if (!validateInputs($("#loginDlg"))) {
		return false;
	}

	showLoading(true);
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "loginUser";
	parms["username"] = $("#username").val();
	parms["password"] = $("#password").val();

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		//alert(resultData);
		//return;
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			$("#loginUrl").hide();
			$("#logoutUrl").show();
			window.location = loginRedirectUrl;
		} else {
			showLoading(false);
			alert($(resultData).find("errMessage").text());
		}
	});
	return true;
};


function showLoginDlg(displayCompulsoryLogin) {
	
	if (displayCompulsoryLogin){
		$("#infoMsg").show();
	} else {
		$("#infoMsg").hide();
	}
	
	$("#compulsoryLoginDlg").dialog("destroy");
	resetValidationMessages($("#loginDlg"));
	$("#username").val("");
	$("#password").val("");

	var dlgHeight = 280;

	$("#loginDlg").dialog( {
		bgiframe : true,
		autoOpen : false,
		width : 380,
		height : dlgHeight,
		modal : true,
		resizable : false,
		title : "Login to Time Travel",
		buttons : {
			Cancel : function() {
				$(this).dialog("destroy");
			},
			Submit : function() {
				if (loginUser()) {
					$(this).dialog("destroy");
				}
			}
		}
	});
	$("#loginDlg").dialog("open");
	$("#username").focus();
	showLoading(false);
}

function isUserLoggedIn(trigger, anchor) {

	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "isUserLoggedIn";

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			if ($(resultData).find("loggedIn").text() == "true") {

				if (trigger == "showLift") {
					showLoading(false);
					fetchLift($(anchor).parent().parent().attr("liftid"));
					fetchUserDetails($(anchor).parent().parent().attr("userid"));
				}

			} else {

				if (trigger == "showLift") {
					showLoading(false);
					showLoginDlg(true);
				}

			}
		} else {
			alert($(resultData).find("errMessage").text());
		}
	});
}

var fbUserLogout = function logoutFbUser(){
	
	FB.logout(function(response) {
		  	console.log("fb user is logged out");
			alert("fb user is logged out");
		});
};

function logoutUser(callback) {
	showLoading(true);
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "logoutUser";

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		
		errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			window.location = "index.php";
		} else {
			showLoading(false);
			alert($(resultData).find("errMessage").text());
		}
		
		if (typeof callback == "function"){
			callback();
		}
	});
}

function displayUserDetails(data){
	$("#userName").html($(data).find("name").text()+" "+$(data).find("surname").text());
	
	var facebook = $(data).find("facebook").text();
	var url;
	if (facebook != "none"){
		url = "http://graph.facebook.com/"+ facebook +"/picture";
	} else {
		url = "images/NoPicture.gif";
	}
	
	var img = new Image();
    img.src = url;
    img.onload = function(){
    	this.height = 70;
    	this.width = 70;
     	$("#myPicture").html(this);
    };
	
	calls--;
	showLoading(false);
}

function clearSignUpFields(){
	$("#signUpForm").find("input").each(function(){
		$(this).val("");
	});
}

function showLogin() {
	$(".containerPlus").hide();
	$("#loginTbl").show();
	$("button").button();
}

function cancelButtonClick() {
	$(".containerPlus").show();
	$("#loginTbl").hide();
}