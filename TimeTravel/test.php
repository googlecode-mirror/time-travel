<?php 
require_once "includes/libs.php";


?>

<script type="text/javascript">
	$(document).ready(function(){
		saveLocation();
	});



	function saveLocation(){

		var url = "controller.php";
		var parms = new Object();
		parms["action"] = "recordMyLocation";
		parms["username"] = "test1";
		parms["timestamp"] = "2012-08-07 14:25:56";
		parms["longitude"] = "-15.0266W";
		parms["latitude"] = "12.5 332E";
		

		$.post(url, parms, function(resultData) {
			resultData = parseResult(resultData);
			alert(resultData);
		});
	}
</script>