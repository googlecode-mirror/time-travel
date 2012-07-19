var loggedIn = false;
var loaded = false;
var loginRedirectUrl = 'index.php?response=pictures';

$(document).ready(function() {
	loaded = true;
	//showLoading(true);

	isUserLoggedIn("refresh", null);

	$("#password").keypress(function(event) {
		if (event.keyCode == '13') {
			$("#loginBtn").click();
		}
	});

	$("button").button();

	
	
	$("#contentArea2").load("includes/infoControl.php");
	
	//we set a function to be called when Enter is pressed
	setEnterAction($("#password"), loginUser);
	
	
	if ($("#mainContentType").val() == "facebook"){
		loadStatusUpdate(); 
	} else {
		loadPictures();
	}
});

function loadStatusUpdate(){
	$("#contentArea1").load("includes/facebookContent.php", function(){
		updateDatePicker();
	});
}

function updateDatePicker(){
	var chosenDate = $("#chosenDate").val();
	$("#datepicker").datepicker("setDate", chosenDate);
	
	$("#displayDate").html($("#diplayDateInput").val());
	console.log("chosenDate: "+chosenDate);
	if (chosenDate != null){
		$("#year").val(chosenDate.substring(0, 4));
		$("#month").val(chosenDate.substring(5, 7));
		
	}
}

function loadPictures(){
	$("#contentArea1").load("includes/mainContentArea.php", function(){
		updateDatePicker();
	});
}

function showPictureUploadDiv(){
	$("#actionArea").html($("#uploadPicsDiv").html());
}