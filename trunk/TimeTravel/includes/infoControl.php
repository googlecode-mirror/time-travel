<?php
	require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/includes/user.session.initializer.php');
	date_default_timezone_set('Africa/Johannesburg');
?>

<script>
	$(document).ready(function(){
		$("button").button();

		$(".randBtn").click(function(button){
			toggleRandomizeButton(this);
		});
	});

	$(function() {
		$( "#datepicker" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: true,
		 	onSelect: function(dateText, inst) { 
		 		if (params.mainContentType == "facebook"){
					loadStatusUpdatesForDate();
		 		} else if (params.mainContentType == "sms"){
		 			loadSmsForDate();
		 		}else{
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
<div id="displayDate" style="font-size: 1.4em; color: #C0C0C0; background-color: #FAF5F5; padding: 3px;"></div>


<br/>
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

<fieldset style="height: 120px; padding: 0px; position: relative; top: 6px;" class="ui-state-default">
<div class="ui-widget-header" style="padding: 2px;">Randomize by:</div>
<div >

	<button class="randBtn" style="padding-right: 0em;" title="Randomize by pictures" value="picture"><img src="/images/picture.png"/></button>
	<button class="randBtn" style="padding-right: 0em;" title="Randomize by status updates" value="s-update"><img src="/images/update.png"/></button>
	<button class="randBtn" style="padding-right: 0em;" title="Randomize by emails" value="email"><img src="/images/email_attach.png"/></button>
	<button class="randBtn" style="padding-right: 0em;" title="Randomize by sms's" value="sms"><img src="/images/email.png"/></button>
	<button class="randBtn" style="padding-right: 0em;" title="Randomize by phone calls" value="phone"><img src="/images/phone.png"/></button>

</div>
</fieldset>

<script type="text/javascript">
	function reloadPage(){
		showLoading(true);
		if (params.mainContentType == "facebook")
			loadRandomDateStatusUpdates();
 		else {
 			$.get("/includes/mainContentArea.php?randOption="+getRandomizeOptions(), function(data) {
 				$("#contentArea1").html(data);
 				updateDatePicker(null);
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

	function loadSmsForDate(){
		var theDate = $("#datepicker").datepicker("getDate");
		theDate = $.datepicker.formatDate('yy-mm-dd', $("#datepicker").datepicker("getDate"));
		$("#displayDate").html(params.diplayDateInput);
		$("#contentArea1").load("includes/smsContent.php?dateText="+theDate, function(){
			updateDatePicker(false);
		});
	}
	
	function loadStatusUpdatesForDate(){
		var theDate = $("#datepicker").datepicker("getDate");
		theDate = $.datepicker.formatDate('yy-mm-dd', $("#datepicker").datepicker("getDate"));
		$("#displayDate").html(params.diplayDateInput);
		$("#contentArea1").load("includes/facebookContent.php?dateText="+theDate, function(){
			updateDatePicker(false);
		});
	}

	function loadRandomDateStatusUpdates(){
		$("#contentArea1").load("includes/facebookContent.php", function(){
			$("#displayDate").html(params.diplayDateInput);
			updateDatePicker();
		});
	}

	
	function doTimeTravel(){
		var targetDate = $("#year").val()+"-"+$("#month").val()+"-01";
		$("#datepicker").datepicker("setDate", targetDate);
		params.chosenDate = targetDate;
		loadContentForDate(targetDate);
	}

	function toggleRandomizeButton(button){
		if ($(button).filter(".ui-state-focus").size() == "1"){
			$(button).removeClass("ui-state-focus");
		} else {
			$(button).addClass("ui-state-focus");
		}
	}

	function getRandomizeOptions(){
		var randOptions="|";
		$(".randBtn").filter(".ui-state-focus").each(function(){
			randOptions += $(this).attr("value") +"|";
		});
		return randOptions;
	}
	
</script>
