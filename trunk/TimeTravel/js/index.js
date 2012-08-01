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
	
	if (params.mainContentType == "facebook"){
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
	$("#datepicker").datepicker("setDate", params.chosenDate);
	
	$("#displayDate").html(params.diplayDateInput);
	console.log("chosenDate: "+params.chosenDate);
	if (params.chosenDate != null){
		$("#year").val(params.chosenDate.substring(0, 4));
		$("#month").val(params.chosenDate.substring(5, 7));
		
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