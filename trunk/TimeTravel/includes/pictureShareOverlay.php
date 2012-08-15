<?php 
require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');

//error_reporting(E_ERROR | E_PARSE);
session_start();
	
//var_dump($_SESSION);
if (isset($_SESSION['name'])) {
	$userid = $_SESSION["userid"];
	$username = $_SESSION['username'];
	$loggedIn = true;
	$dayToDisplay = $_SESSION['dayToDisplay'];
}

$userDAO = new UserDAO();
$fbToken = $userDAO->getUserToken($userid);

$pictureDAO = new PictureDAO();
$pictures = $pictureDAO->getAllPicturesForDay($dayToDisplay);
?>

<style>
	.ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
	#city { width: 25em; }
</style>

<script>
var picShareOverlay = {};
picShareOverlay.fbToken = "<?php echo $fbToken?>";


	$(function() {
		function log( message ) {
			$( "<div/>" ).text( message ).prependTo( "#log" );
			$( "#log" ).scrollTop( 0 );
		}

		$( "#city" ).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "https://graph.facebook.com/fql?q=SELECT name, pic_square FROM user WHERE uid IN(SELECT uid2 FROM friend WHERE uid1 = me()) AND strpos(lower(name), lower('"+ request.term  +"')) >=0&access_token=AAACEdEose0cBANATBE8LUkDadJXk88kCvlWkIwbZAvAg53ljBnRRm2hTyg5SV3sqEUQsUsNju5ngnnC6JAM2ZBE7Ppbs4MvVro0BIYywZDZD",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
					},
					success: function( data ) {
						response( $.map( data.data, function( item ) {
							return {
								label: item.name,
								value: item.name
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				log( ui.item ?
					"Selected: " + ui.item.label :
					"Nothing selected, input was " + this.value);
			},
			open: function() {
				$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});
	});
	</script>





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
	
	<div class="ui-widget">
	<label for="city">Your city: </label>
	<input id="city" />
	</div>
	
	<div class="ui-widget" style="margin-top:2em; font-family:Arial">
		Result:
		<div id="log" style="height: 200px; width: 300px; overflow: auto;" class="ui-widget-content"></div>
	</div>
	
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
