$(document).ready(function(){
	showMyDetails();
	$("button").button();
});


function updateUserDetails(){
	if (!validateInputs($("#mydetails"))) {
		return false;
	}
	
	showLoading(true);
	
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "updateUser";
	parms["username"] = $("#usernameinput").val();
	parms["email"] = $("#emailaddressinput").val();
	parms["name"] = $("#firstnameinput").val();
	parms["surname"] = $("#lastnameinput").val();
	parms["cellphone"] = $("#cellphoneinput").val();
	parms["facebook"] = $("#facebookpageinput").val();
	

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			showLoading(false);
			showMessage($(resultData).find("errMessage").text(), "success");
		} else {
			showLoading(false);
			showMessage($(resultData).find("errMessage").text(), "error");
		}
	});
}

function updateMyPassword(){
	if (!validateInputs($("#changePassword"))) {
		return false;
	}
	
	if ($("#newpasswordinput").val() != $("#passwordconfirminput").val()) {
		alert("Passwords do not match.");
		return false;
	}
	
	showLoading(true);
	
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "updatePassword";
	parms["currentPassword"] = $("#currentpasswordinput").val();
	parms["newPassword"] = $("#newpasswordinput").val();
	

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			showLoading(false);
			showMessage($(resultData).find("errMessage").text(), "success");
		} else {
			showLoading(false);
			showMessage($(resultData).find("errMessage").text(), "error");
		}
	});
}

function getUserDetails(){
showLoading(true);
	
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "getUserDetails";

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			showLoading(false);
			displayMyDetails(resultData);
		} else {
			showLoading(false);
			alert($(resultData).find("errMessage").text());
		}
	});
}

function getAllLiftsForUser(){
	showLoading(true);
		
		var url = "controller.php";
		var parms = new Object();
		parms["action"] = "getAllLiftsForUser";

		$.post(url, parms, function(resultData) {
			resultData = parseResult(resultData);
			var errorCode = $(resultData).find("code").text();
			if (errorCode == 0) {
				showLoading(false);
				displayMyLifts(resultData);
			} else {
				showLoading(false);
				alert($(resultData).find("errMessage").text());
			}
		});
}

function hideAllTables(){
	$("#mydetails").hide();
	$("#changePassword").hide();
	$("#myLifts").hide();
	$("#message").fadeOut("normal");
}

function showChangePassword(){
	hideAllTables();
	$("#currentpasswordinput").val("");
	$("#newpasswordinput").val("");
	$("#passwordconfirminput").val("");
	$("#changePassword").fadeIn("normal");
}

function showMyDetails(){
	hideAllTables();
	getUserDetails();
	$("#mydetails").fadeIn("normal");
}
	

function showMyLifts(){
	getAllLiftsForUser();
	hideAllTables();
	$("#myLifts").show();
}

function getLift(anchor){
	showLoading(true);
	fetchLift($(anchor).parent().parent().attr("liftid"));
	fetchUserDetails($(anchor).parent().parent().attr("userid"));
}

function displayMyLifts(data){
	$(".liftRow").remove();
	var count = 0;
	$(data).find("Lift").each(function() {
		var liftid = $(this).find("id").text();
		var postedDate = $(this).find("createdDate").text();
		var destination = $(this).find("destination").text();
		var liftDate = $(this).find("departuredate").text();
		var status = $(this).find("status").text();
		var advertiserId = $(this).find("advertiserId").text();
		
		var row = "<tr class='liftRow' liftid='"+liftid +"' userid='"+ advertiserId +"'>"+
					"<td class='list'>"+postedDate+"</td>"+
					"<td class='list'><a href='#' onclick='getLift(this);'>"+destination+"</a></td>"+
					"<td class='list'>"+liftDate+"</td>"+
					"<td class='list'>"+status+"</td>"+
					"<td class='list'><input class='liftCheckbox' type='checkbox'/></td>"+
				"</tr>";
			$(row).insertAfter($("#liftHeader"));
			count++;
	});
	
	$(".liftCheckbox").change(function(){
		if (isAnyLiftSelected()){
			$("#liftDeleteBtn").button("enable");
		} else {
			$("#liftDeleteBtn").button("disable");
		}
	});
	
	if (count == 0){
		$("<tr class='liftRow'><td colspan='5' class='list' align='center'>No lifts found.</td></tr>").insertAfter($("#liftHeader"));
	}
}

function selectAllLifts(checkbox){
	if ($(checkbox).attr("checked") == true){
		$(".liftCheckbox").attr("checked", "checked");
	} else {
		$(".liftCheckbox").removeAttr("checked");
	}
	
	//We enable or disable Delete Button
	if (isAnyLiftSelected()){
		$("#liftDeleteBtn").button("enable");
	} else {
		$("#liftDeleteBtn").button("disable");
	}
}

function isAnyLiftSelected(){
	var count = 0;
	$(".liftCheckbox:checked").each(function(){
		count++;
	});
	if (count > 0)
		return true;
	else
		return false;
}

function displayMyDetails(data){
	$(data).find("User").each(function() {
		$("#usernameinput").val($(this).find("username").text());
		$("#firstnameinput").val($(data).find("name").text());
		$("#lastnameinput").val($(data).find("surname").text());
		$("#emailaddressinput").val($(data).find("email").text());
		$("#cellphoneinput").val($(data).find("cellphone").text());
		$("#facebookpageinput").val($(data).find("facebook").text());
	});
}