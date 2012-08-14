<?php
	require_once(dirname(dirname(__FILE__)) .'/viewbean/Picture.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/LocationDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/GmailDAO.php');
	require_once(dirname(dirname(__FILE__)) .'/conf.php');
	require_once(dirname(dirname(__FILE__)) .'/util.php');
	require_once(dirname(dirname(__FILE__)) . '/services/securityServices.php');
	
	date_default_timezone_set('Africa/Johannesburg');
?>

<script type="text/javascript" src="/js/vendor/jquery.aw-showcase.min.js"></script>
<link rel="stylesheet" href="../css/scroller.css" />
<script>
$(document).ready(function()	{
		$("#timeSelectorDiv").html($("#timeSelector").html());

		$("#timeSelectorDiv").find("select").each(function(){
			$(this).addClass("working");
		});
		
		$("#pictureDate").html($.datepicker.formatDate('yy-mm-dd', new Date()));

		doShowCase();
		$("button").button();


		hideShowCaseButtons($("#showcase"));
		showLoading(false);
		loadFirstPic();
		//LoadAllimages();

		$("#picCaptionOverlay").dialog( {
			bgiframe : true,
			autoOpen : false,
			width : 600,
			position: ['center','center'],
			modal : true,
			resizable : false,
			title : "Edit Description",
			buttons : {
				Cancel : function() {
					$(this).dialog("close");
				},
				Submit : function() {
					updatePictureCaption();
					$(this).dialog("close");
				}
			}
		});


		$("#dateTakenOverlay").dialog( {
			bgiframe : true,
			autoOpen : false,
			width : 600,
			height: 420,
			position: ['center','center'],
			modal : true,
			resizable : false,
			title : "Set The Date The Picture Was Taken",
			buttons : {
				Cancel : function() {
					$(this).dialog("close");
				},
				Submit : function() {
					doChangePictureDate();
					$(this).dialog("close");
				}
			}
		});

		$("#picCaption").blur(function(){
			$("#captionValue").val($(this).val());
		});


		$(".selectInput").change(function(){
			updateSelectedTimeForNewDate($(this));
		});


		$("#smsAccordion").accordion({ autoHeight: true, clearStyle: true });
});

function doChangePictureDate(){
	var img = $(".showcase-content").find("img");
	var imageId = $(img).parent().attr("id");

	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "updatePictureCaption";
	parms["caption"] = $("#captionValue").val();
	parms["pictureId"] = imageId;
	parms["dateandtime"] = $("#pictureDate").html() + " "+$("#selectedTimeForNewDate").val();

	$.post(url, parms, function(resultData) {
		$("#dateTakenOverlay").dialog('close');
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			loadContentForDate($("#pictureDate").html());
			params.chosenDate = $("#pictureDate").html();
			updateDatePicker();
		} else {
			alert($(resultData).find("errMessage").text());
		}
		//$("#dateTakenOverlay").dialog("close");
	});
}

function doShowCase(){
	$("#showcase").awShowcase(
			{
				content_width:			460,
				content_height:			400,
				fit_to_parent:			false,
				auto:					false,
				interval:				3000,
				continuous:				true,
				loading:				true,
				tooltip_width:			200,
				tooltip_icon_width:		32,
				tooltip_icon_height:	32,
				tooltip_offsetx:		18,
				tooltip_offsety:		0,
				arrows:					true,
				buttons:				true,
				btn_numbers:			true,
				keybord_keys:			true,
				mousetrace:				false, /* Trace x and y coordinates for the mouse */
				pauseonover:			true,
				stoponclick:			false,
				transition:				'hslide', /* hslide/vslide/fade */
				transition_delay:		0,
				transition_speed:		500,
				show_caption:			'onload', /* onload/onhover/show */
				thumbnails:				false,
				thumbnails_position:	'outside-last', /* outside-last/outside-first/inside-last/inside-first */
				thumbnails_direction:	'vertical', /* vertical/horizontal */
				thumbnails_slidex:		1, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
				dynamic_height:			false, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
				speed_change:			true, /* Set to true to prevent users from swithing more then one slide at once. */
				viewline:				false, /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
				custom_function:		firePicChange /* Define a custom function that runs on content change */
			});

}

var firePicChange = function LoadAllimages(){
	setTimeout(function(){
		$(".showcase-content:visible").each(function(){
			var pictureId = $(this).find("input[id='pictureId']").val();
			var pictureSrc = $(this).find("input[id='pictureSrc']").val();
			loadThisImage(pictureId, pictureSrc);
		});
	}, 1000);
};



function loadFirstPic(){
	var pictureId = $(".showcase-content:visible #pictureId").val();
	var pictureSrc = $(".showcase-content:visible #pictureSrc").val();
	loadThisImage(pictureId, pictureSrc);
}

function showCaptionOverlay(){
	if ($(".picDescription").html() != ""){
		$("#picCaption").val($(".picDescription").html().replace('"',  '').replace('"',  ''));
	} else {
		$("#picCaption").val("");
	}
	
	$("#picCaptionOverlay").dialog('open');
}

function showPicture(anchor, originalWidth, originalHeight, image){
	
	var caption = $(anchor).parent().parent().find(".showcase-caption");
	$(caption).css({bottom: '100px'});
	$("#pictureDiv").append(caption);

	var newHeight = screen.height - 300;
	var newWidth = (((newHeight/originalHeight)* originalWidth) + 50) > screen.width ? screen.width : (((newHeight/originalHeight)* originalWidth) + 50) - 250;

	$("#picOverlay").dialog( {
		bgiframe : true,
		autoOpen : false,
		width : newWidth,
		height : screen.height - 170,
		position: ['center', 1],
		modal : true,
		resizable : false,
		title : "Detailed picture",
		//show: { show: 'slide', direction: "up" },
		buttons : {
			Close : function() {
				$(this).dialog("destroy");
			}
		}
	});

	$("#picOverlay").dialog("open");
	$("#pictureDiv").html(image);
}

function returnPicture(width, height, anchor){
	var picture = $("#pictureDiv").find("img");
	$(picture).removeAttr("width");
	$(picture).height(height);
	$(anchor).html(picture);

	//we return the caption
	var caption = $("#pictureDiv").parent().find(".showcase-caption");
	$(caption).css({bottom: '10px'});
	$(anchor).parent().parent().append(caption);
}

function callPictureRotate(direction){
	showLoading(true);
	var anchor = $(".showcase-content").find("a");
	var imageId = $(anchor).attr("id");

	var picSoure = $(anchor).find("img").attr("src");
	rotatePicture(imageId, direction, function(picSoure){
		//var timestamp = new Date().getTime();
		//$(img).attr("src", picSoure + '?' +timestamp);

		loadThisImage(imageId, picSoure, true);
	}, picSoure);
}

function updatePictureCaption(){
	var img = $(".showcase-content").find("img");
	var imageId = $(img).parent().attr("id");

	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "updatePictureCaption";
	parms["caption"] = $("#captionValue").val();
	parms["pictureId"] = imageId;
	
	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			$(img).parent().parent().parent().find(".picDescription").html('"'+ parms["caption"] +'"');
		} else {
			alert($(resultData).find("errMessage").text());
		}
	});
}

function loadMainImage(anchor){
	var holder = $("#pictureDiv");
	var url = $(anchor).parent().find("#mainPicUrl").val();
	var img = new Image();

	var numRand = Math.random();
  	img.src = url+"?rand="+numRand;

    img.onload = function(){
		this.height = screen.height - 290;
    	showPicture(anchor, this.width, this.height, this);
     	//showLoading(false);
    };
    img.onerror = function(){
     	this.src = "/images/NoPicture.gif";
     	this.onload = function(){
     		showLoading(false);
     		$(holder).html(this);
     	};
    };
}

function loadThisImage(imageId, imageUrl, refresh){
	var loader = $("a[id='"+ imageId +"']");

	if ($(loader).size() == 0){
		loader = $(imageId);
	}
	var url = imageUrl;
	var img = new Image();

	refresh = true;
    if (refresh){
        var numRand = Math.random();
    	img.src = url+"?rand="+numRand;
    } else {
    	img.src = url;
    }

    img.onload = function(){
		if (this.width > this.height)
			 this.height = 400;
		else
    		this.width = 460;
     	$(loader).html(this);
     	showLoading(false);
    };
    img.onerror = function(){
     	this.src = "/images/NoPicture.gif";
     	this.onload = function(){
     		showLoading(false);
     		$(loader).html(this);
     	};
    };
}

function showDateTakenOverlay(){
	
	if ($("#dateTakenOverlay").find("div[id='picturedatepicker']").size() == 0){
		$("#dateTakenOverlay").html($("#picturedatepicker"));
		$("#dateTakenOverlay").append($("#dateTakenDates"));
		$("#dateTakenDates").fadeIn("normal");

		$("#picturedatepicker").datepicker({
			numberOfMonths: 1,
			showButtonPanel: true,
			dateFormat: "yy-mm-dd",
		 	onSelect: function(dateText, inst) { 
		 		$("#dateTakenDates #pictureDate").html(dateText);
		   }
		});
	}

	$("#dateTakenOverlay").dialog('open');
	$("#picturedatepicker").datepicker("setDate", params.chosenDate);
	$("#dateTakenDates #pictureDate").html(params.chosenDate);

}


function updateSelectedTimeForNewDate(control){
	var theTime = $(control).parent().find(".hourTempl").val()+":"+$(control).parent().find(".minuteTempl").val();
	$("#selectedTimeForNewDate").val(theTime);
}


function showPictureShareOverlay(){
	$("#genericOverlay").load("/includes/pictureShareOverlay.php");
	$("#genericOverlay").dialog( {
		bgiframe : true,
		autoOpen : false,
		width : 600,
		height : 280,
		position: ['center'],
		modal : true,
		resizable : false,
		title : "Share pictures",
		//show: { show: 'slide', direction: "up" },
		buttons : {
			Cancel : function() {
				$(this).dialog("destroy");
			},
			Share : function() {
				if (validateInput()){
					$(this).dialog("destroy");
					doSharePictures();
				}
			}
		}
	});

	$("#genericOverlay").dialog("open");
}
</script>
	
	<?php
			error_reporting(E_ERROR | E_PARSE);
			session_start();
			
			//var_dump($_SESSION);
			if (isset($_SESSION['name'])) {
				$userid = $_SESSION["userid"];
				$username = $_SESSION['username'];
				$loggedIn = true;
				error_log("session is set");
			}
			
			$dayDAO = new DayDAO();
			$securityService = new SecurityService();

			if (isset($_GET["dateText"])){
				$theDate = $_GET["dateText"];
				$dayToDisplay = $dayDAO->getIdForDay($userid, $theDate);
			} else {
				$dayToDisplay = $dayDAO->getRandomDay($userid, $_GET["randOption"]);
			}
			
			error_log("date : ". $dayToDisplay);
			
			if ($dayToDisplay === 0){
				$chosenDate = date("Y-m-d", strtotime($theDate));
				$diplayDate = date("Y F j l", strtotime($theDate));
			} else {
				$chosenDate = date("Y-m-d", strtotime($dayDAO->getDateForDayId($userid, $dayToDisplay)));
				$diplayDate = date("Y F j l", strtotime($dayDAO->getDateForDayId($userid, $dayToDisplay)));
			}

			error_log("chosenDate : ". $chosenDate);
			
			$_SESSION['chosenDate'] = $chosenDate;
			$_SESSION['diplayDate'] = $diplayDate;
			$_SESSION['dayToDisplay'] = $dayToDisplay;
			
			$pictureDAO = new PictureDAO();
			$pictures = $pictureDAO->getAllPicturesForDay($dayToDisplay);
			
			$sharedpictures = $pictureDAO->getSharedPicturesForUser($userid, $chosenDate);
			error_log("ID: ".sizeof($sharedpictures));
			
			$pictures = array_merge($pictures, $sharedpictures);
			
			//Facebook Statuses
			$userDAO = new UserDAO();
			$statusUpdate = $userDAO->retrieveRandomStatusUpdateForDay($userid, $dayToDisplay);
			
			$statusUpdateFound = false;
			if (isset($statusUpdate)){
				$statusUpdateFound = true;
			}
			
			$picturesFound = false;
			
		?>
		
		<script type="text/javascript">
			$(document).ready(function()	{
				params.chosenDate = "<?php echo$chosenDate?>";
				params.diplayDateInput = "<?php echo$diplayDate?>";
			});
		</script>
			
			<!--  <img src="showimage.php?image_id=<?php echo $picture->id; ?>" alt="Image from DB" width="100%"/> -->
		<div style="width: 400;">
	
			<div id="showcase" class="showcase">
				
				<?php
				foreach($pictures as $picture){
					$picturesFound = true;
					$pictureIsShared = isset($picture->sharerUsername) ? true : false;
					$username = $pictureIsShared ? $picture->sharerUsername : $username;
					
					$pictureSrc =  '/pictures/'. $username.'/optimized/'.$picture->filename;
					$mainPicUrl =  '/pictures/'.$username.'/main/'.$picture->filename;
					$pictureDescription = ($picture->description == "" ? "" : "\""). $picture->description . ($picture->description == "" ? "" : "\"");
					$pictureDescription = $pictureIsShared ? ("Shared to you by '".($securityService->getUserByUsername($picture->sharerUsername)->name))."'" : $pictureDescription;
				?>
				<!-- Each child div in #showcase represents a slide -->
				<div class="showcase-slide">
					<!-- Put the slide content in a div with the class .showcase-content. -->
					<div class="showcase-content">
						<input id="pictureId" type="hidden" value="<?php echo$picture->id ?>"/>
						<input id="pictureSrc" type="hidden" value="<?php echo$pictureSrc?>"/>
						<input id="mainPicUrl" type="hidden" value="<?php echo$mainPicUrl?>"/>
						<a id="<?php echo$picture->id ?>" filename="<?php echo$picture->filename?>" href="#" onclick="loadMainImage(this);"><img id="<?php echo$picture->id ?>" src="/images/loading.gif" width="60" style="position: relative; top: 120px;"/></a>
					</div>
					<!-- Put the caption content in a div with the class .showcase-caption -->
					<div class="showcase-caption" style="width: 430px; left: 0px;">
						<div class="picDescription" style="float: left"><?php echo $pictureDescription ?></div>
						
						<div style="font-size: 0.7em; text-align: right; float: right;"><?php echo date("Y F j l g:i a", strtotime($picture->timetaken))?></div>
					</div>
				</div>
		
		<?php }?>
		
		</div>
				
		</div>
				
		<?php
			if ($picturesFound) {
				$pictureTime = date("g:i a", strtotime($picture->timetaken));
				$pictureIsShared = isset($picture->sharerUsername) ? true : false;
			
		?>
				
		 
		<!-- <div class="formlabel" style="display: <?php echo$picturesFound? "none" : "block"; ?>;">No Pictures taken on this day.</div>  -->
		
		<div class="ui-datepicker-inline ui-widget ui-widget-content ui-helper-clearfix" style="padding-top: 5px; position: relative; left: 0px; width:450px; display: <?php echo $pictureIsShared ? "none" : "block"?>; ?>;">
			<a href="#" onclick="callPictureRotate('left');" title="Rotate picture left"><img src="/images/rotate-left.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="callPictureRotate('right');" title="Rotate picture right"><img src="/images/rotate-right.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showCaptionOverlay();" title="Edit caption"><img src="/images/comment.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showDateTakenOverlay();" title="Edit picture date"><img src="/images/calendar.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showPictureShareOverlay();" title="Share pictures"><img src="/images/share.png"width="22"/></a>
		</div>
		<br/><br/>
		
		<?php }?>
		
		<!-- STATUS UPDATES -->
		<?php if ($statusUpdateFound) {
				$statusUpdateTime = date("g:i a", strtotime($statusUpdate->theDate));
			 ?>
		<div class="formlabel ui-corner-top" style="display:block;">
			 <div style="font-size: 1.1em; text-align: left; background-color: #FAF5F5; padding: 5px;"><img src="/images/facebook.jpg" width="20"></img>&nbsp;&nbsp; "<?php echo $statusUpdate->message ?>"</div><br/>
			 <div style="font-size: 0.8em; text-align: right;"><?php echo $statusUpdateTime ?></div>
		</div>
		
		<?php }?>
		
	
		<!--GEO LOCATIONS -->
		<?php 
			$locationDAO = new LocationDAO();
			
			$geoLocations = $locationDAO->getLocationsForDay($dayToDisplay);
			$url = "http://maps.googleapis.com/maps/api/staticmap?size=480x480&sensor=true&path=";
			$count = 0;
			foreach ($geoLocations as $location){
				if ($count > 0){
					$url .= "|";
				}
				$url .=$location->latitude.",".$location->longitude;
				$count++;
			}
			
			
			$onlyMarkFirstAndLastPoints = false;
			$numOfPoints = sizeof($geoLocations);
			if ($numOfPoints > 26){
				$onlyMarkFirstAndLastPoints = true;
			}
			
			$count = 0;
			$label = "A";
			foreach ($geoLocations as $location){
				$count++;
				if (($onlyMarkFirstAndLastPoints && ($count == 1)) || ($onlyMarkFirstAndLastPoints && ($count == $numOfPoints))){
					$url .= "&markers=label:". $label."|";
					$url .=$location->latitude.",".$location->longitude;
					$label++;
				} else if (!$onlyMarkFirstAndLastPoints){
					$url .= "&markers=label:". $label."|";
					$url .=$location->latitude.",".$location->longitude;
					$label++;
				}
			}
			
			error_log("URL: ".$url);
			
			if ($numOfPoints > 0){
		?>
		<div id="geoMap" class="formlabel" style="display:block;">
			<img src="<?php echo $url?>" style="position: relative; left: -6px;"></img>
		</div>
		<br/>
		<?php }?>
	
		
		<div id="smsAccordion">
	
		<!-- SMS's -->
		<?php 
			$gmailDAO = new GmailDAO();
			$userSubscribedForSms = $gmailDAO->hasUserSetupContentUpdate($userid, 'sms');
			if ($userSubscribedForSms){
				$smsList = $gmailDAO->getCommunicationContentForDay($dayToDisplay, "sms");
				$smsList = array_reverse($smsList);
				foreach ($smsList as $sms){
					$source = Util::getSourceName($sms->from);
				
	?>
		<h3><span style="float: left; left: 25px; position: relative;"><?php echo $source?></span>
		
		
			<span align="right"><?php echo date("H:m:s", strtotime($sms->timestamp))?></span>
		</h3>
		<div>
	 		<p class="formlabel" style="text-align: left; background-color: #FAF5F5;"><?php echo $sms->body?></p>
		</div>
	
	<?php 
				}
			}
		?>
		
		</div>
		
		
</div>
<div id="picOverlay" style="display: none;">
	<div id="pictureDiv" align="center">
	</div>
</div>

<div id="picCaptionOverlay" style="display: none;" align="center">
	<input id="captionValue" type="hidden" /> 
	<input id="picCaption" type="text" class="forminput" style="width: 500px;"/>
</div>

<div id="dateTakenOverlay" style="display: none;" align="center">
		<br/>
		
</div>

<input type="hidden" id="selectedTimeForNewDate">

<div id="dateTakenDates" style="display: none;">
	<span style="font-size: 1.1em; color: grey;">The picture was taken on </span> <span id="pictureDate"
		style="font-size: 1.3em; color: grey; font-weight: bold;"></span> at <span id="timeSelectorDiv"> </span>

</div>
