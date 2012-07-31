<?php
	require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/includes/user.session.initializer.php');
	date_default_timezone_set('Africa/Johannesburg');
?>

<script>
	$(document).ready(function(){
		$("button").button();
		
	});

	$(function() {
		$( "#datepicker" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: true,
		 	onSelect: function(dateText, inst) { 
		 		if ($("#mainContentType").val() == "facebook"){
					loadStatusUpdatesForDate();
		 		} else{
			 		showLoading(true);
		 			loadContentForDate();
		 		}
		 		
		   }
		});
	});

	$('#datepicker').datepicker("option", "dateFormat", "yy-mm-dd" );

</script>


<div id="datepicker"></div>
<div id="picturedatepicker"></div>

<br/>
<div id="displayDate" style="font-size: 1.4em; color: #C0C0C0;"></div>


<br/><br/>
<button onclick="reloadPage();">Random</button>
<button onclick="gotoPictureUpload();" >Upload Pictures</button>

<div id="dateSelector"></div>


<?php 
	$userid = $_SESSION["userid"];
	$dayDAO = new DayDAO();
	$earliestDate = $dayDAO->getEarliestDateOfMemory($userid);
	$earliestYear = intval(substr($earliestDate, 0, 4));
	$currentYear = date("Y");
	error_log($earliestYear."  AND ".$currentYear);
?>
<select id="year" class="selectInput" style="font-size: 1.3em; color: #C0C0C0;">
	<?php for ($i = $currentYear; $i >= $earliestYear; $i--) { ?>
		<option value="<?php echo$i?>"><?php echo$i?></option>
	<?php }?>
</select >
<select id="month" class="selectInput" style="font-size: 1.3em; color: #C0C0C0;">
	<option value="01">January</option>
	<option value="02">February</option>
	<option value="03">March</option>
	<option value="04">April</option>
	<option value="05">May</option>
	<option value="06">June</option>
	<option value="07">July</option>
	<option value="08">August</option>
	<option value="09">September</option>
	<option value="10">October</option>
	<option value="11">November</option>
	<option value="12">December</option>
</select >

<button style="font-size: 1.0em; color: #C0C0C0;" onclick="doTimeTravel();">GO</button>


<script type="text/javascript">
	function reloadPage(){
		showLoading(true);
		if ($("#mainContentType").val() == "facebook")
			loadRandomDateStatusUpdates();
 		else {
 			$.get("/includes/mainContentArea.php", function(data) {
 				 $("#contentArea1").html(data);
 				updateDatePicker();
 			});
 		}
	}

	function gotoPictureUpload(){
		location = "testing/index.html";
	}

	function loadContentForDate(targetDate){
		var theDate = null;
		if (targetDate == null){
			theDate = $("#datepicker").datepicker("getDate");
			theDate = $.datepicker.formatDate('yy-mm-dd', $("#datepicker").datepicker("getDate"));
		} else {
			theDate = targetDate;
		}
		$.get("/includes/mainContentArea.php?dateText=" +theDate, function(data) {
			  $("#contentArea1").html(data);
			  updateDatePicker(false);
			  //alert($("#chosenDate").val());
		});
	}

	function loadStatusUpdatesForDate(){
		var theDate = $("#datepicker").datepicker("getDate");
		theDate = $.datepicker.formatDate('yy-mm-dd', $("#datepicker").datepicker("getDate"));
		$("#displayDate").html($("#diplayDateInput").val());
		$("#contentArea1").load("includes/facebookContent.php?dateText="+theDate, function(){
			updateDatePicker(false);
		});
	}

	function loadRandomDateStatusUpdates(){
		$("#contentArea1").load("includes/facebookContent.php", function(){
			$("#displayDate").html($("#diplayDateInput").val());
			updateDatePicker();
		});
	}

	
	function doTimeTravel(){
		var targetDate = $("#year").val()+"-"+$("#month").val()+"-01";
		$("#datepicker").datepicker("setDate", targetDate);
		$("#chosenDate").val(targetDate);
		loadContentForDate(targetDate);
	}

	function updateDatePicker(changeDatepicker){
		var chosenDate = $("#chosenDate").val();

		if (changeDatepicker == null){
			$("#datepicker").datepicker("setDate", chosenDate);
		}

		$("#displayDate").html($("#diplayDateInput").val());
		console.log("chosenDate: "+chosenDate);
		if (chosenDate != null){
			$("#year").val(chosenDate.substring(0, 4));
			$("#month").val(chosenDate.substring(5, 7));
			
		}
	}
</script>
