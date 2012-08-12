<?php 
require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');

//error_reporting(E_ERROR | E_PARSE);
session_start();
	
//var_dump($_SESSION);
if (isset($_SESSION['name'])) {
	$userid = $_SESSION["userid"];
	$username = $_SESSION['username'];
	$loggedIn = true;
	$dayToDisplay = $_SESSION['dayToDisplay'];
}

$pictureDAO = new PictureDAO();
$pictures = $pictureDAO->getAllPicturesForDay($dayToDisplay);
?>
<table id="pictureShareOverlay" style="overflow-x: scroll; height: 120px;" cellspacing="0" cellpadding="0">
	<tr>
<?php 
	foreach ($pictures as $picture){
		$pictureSrc =  '/pictures/'. $username.'/thumbnails/'.$picture->filename . "?rand=".time();
?>
		<td><img id="" src="<?php echo $pictureSrc?>" height="80"/></td>

<?php }?>
</tr>
<tr>
<?php 
	foreach ($pictures as $picture){
		
?>
		<td align="center"><input class="pictures" type="checkbox" value="<?php echo $picture->id ?>"></input></td>

<?php }?>
</tr>
		 
</table>
<div>
	<fieldset>
	<span style="padding-right: 40px;">Select All pictures<input type="checkbox" onchange="selectAllPics(this);"/></span>
	
	Share picture(s) to:
	<select id="sharedToId">
		<option value="-1">Select User</option>
		<option value="13">Britside</option>
		<option value="12">Test User</option>
	</select>
	</fieldset>
	
	
</div>


<script type="text/javascript">

function doSharePictures(){
	showLoading(true);
	var pictureList = "";
	
	$("#pictureShareOverlay").find("input[type='checkbox']:checked").filter(".pictures").each(function(){
		pictureList += $(this).val() + ",";
	});

	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "sharePicturesToOtherUser";
	parms["picturelist"] = pictureList;
	parms["shareToId"] = $("#sharedToId").val();

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		showLoading(false);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			alert("The picture(s) have been shared successfuly.");
		} else {
			alert($(resultData).find("errMessage").text());
		}
	});
}

function validateInput(){
	var atLeastOneSelected = false;
	$("#pictureShareOverlay").find("input[type='checkbox']:checked").each(function(){
		atLeastOneSelected = true;
	});

	if (!atLeastOneSelected){
		alert("You need to select at least one picture to share"); return false;
	}

	if ($("#sharedToId").val() == "-1"){
		alert("You need to select a user to share to");	return false;
	}
	return true;
}

function selectAllPics(checkbox){
	if ($(checkbox).is(":checked") == true) {
		$("#pictureShareOverlay").find("input[type='checkbox']").each(function(){
			$(this).attr("checked", "true");
		});
	} else {
		$("#pictureShareOverlay").find("input[type='checkbox']").each(function(){
			$(this).removeAttr("checked");
		});
	}
}

</script>
