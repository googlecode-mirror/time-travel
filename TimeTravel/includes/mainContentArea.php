<?php
	require_once(dirname(dirname(__FILE__)) .'/viewbean/Picture.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/DayDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/PictureDAO.php');
	require_once(dirname(dirname(__FILE__)) . '/dao/UserDAO.php');
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
});

function doChangePictureDate(){
	var img = $(".showcase-content").find("img");
	var imageId = $(img).parent().attr("id");

	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "updatePictureCaption";
	parms["caption"] = $("#captionValue").val();
	parms["pictureId"] = imageId;
	parms["dateandtime"] = $("#pictureDate").html() + " "+$(".hourTempl").filter(".working").val()+":"+$(".minuteTempl").filter(".working").val();

	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {
			//$(img).parent().parent().parent().find(".picDescription").html('"'+ parms["caption"] +'"');
		} else {
			alert($(resultData).find("errMessage").text());
		}
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

function showPicture(anchor){
	var originalImage = $(anchor).find("img");
	var originalHeight = $(originalImage).height();
	var originalWidth = $(originalImage).width();
	var newHeight = screen.height - 300;
	$(originalImage).css("height", newHeight+"px");
	$(originalImage).removeAttr("width");

	$("#pictureDiv").html($(originalImage).clone());
	
	var caption = $(anchor).parent().parent().find(".showcase-caption");
	$(caption).css({bottom: '100px'});
	$("#pictureDiv").append(caption);

	var newWidth = (((newHeight/originalHeight)* originalWidth) + 50) > screen.width ? screen.width : (((newHeight/originalHeight)* originalWidth) + 50);
	
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
				//returnPicture(originalWidth, originalHeight, anchor);
				$("#pictureDiv").html("");
				$(this).dialog("close");
			}
		}
	});

	$("#picOverlay").dialog("open");
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

		loadThisImage(imageId, picSoure);
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

function loadThisImage(imageId, imageUrl){
	var loader = $("a[id='"+ imageId +"']");
	var numRand = Math.random();
	var url = imageUrl;
	var img = new Image();
	//this.height = 500;
	//this.width = 400;
    
    img.src = url+"?rand="+numRand;
    img.onload = function(){
    	//this.height = 400;
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
	$("#dateTakenOverlay").dialog('open');
}


$(function() {
		$( "#tabs" ).tabs();
		$( ".tabs-bottom .ui-tabs-nav, .tabs-bottom .ui-tabs-nav > *" )
			.removeClass( "ui-corner-all ui-corner-top" )
			.addClass( "ui-corner-bottom" );
	});
</script>
	
	<?php
			error_reporting(E_ERROR | E_PARSE);
			session_start();
			
			$userid = "";
			$loggedIn = false;
			if (isset($_SESSION['name'])) {
				$userid = $_SESSION["userid"];
				$username = $_SESSION['username'];
				$loggedIn = true;
				error_log("session is set");
			}
			
			echo "USERID: ".$userid;
			$dayDAO = new DayDAO();

			if (isset($_GET["dateText"])){
				$theDate = $_GET["dateText"];
				$dayToDisplay = $dayDAO->getIdForDay($userid, $theDate);
			} else {
				$dayToDisplay = $dayDAO->getRandomDay($userid);
			}
			
			//error_log("date : ". $dayToDisplay);
			
			$pictureDAO = new PictureDAO();
			$pictures = $pictureDAO->getAllPicturesForDay($dayToDisplay);
			
			//Facebook Statues
			$userDAO = new UserDAO();
			$statusUpdate = $userDAO->retrieveRandomStatusUpdateForDay($userid, $dayToDisplay);
			
			$statusUpdateFound = false;
			if (isset($statusUpdate)){
				$statusUpdateFound = true;
			}
			
			$picturesFound = false;
			
		?>
			
			<!--  <img src="showimage.php?image_id=<?php echo $picture->id; ?>" alt="Image from DB" width="100%"/> -->
		<div style="width: 400;">
	
			<div id="showcase" class="showcase">
				
				<?php
				foreach($pictures as $picture){
					$picturesFound = true;
					$pictureSrc =  '/pictures/'.$username.'/main/'.$picture->filename;
				?>
				<!-- Each child div in #showcase represents a slide -->
				<div class="showcase-slide">
					<!-- Put the slide content in a div with the class .showcase-content. -->
					<div class="showcase-content">
						<input id="pictureId" type="hidden" value="<?php echo$picture->id ?>"/>
						<input id="pictureSrc" type="hidden" value="<?php echo$pictureSrc?>"/>
						<a id="<?php echo$picture->id ?>" href="#" onclick="showPicture(this);"><img id="<?php echo$picture->id ?>" src="/images/loading.gif" width="60" style="position: relative; top: 120px;"/></a>
					</div>
					<!-- Put the caption content in a div with the class .showcase-caption -->
					<div class="showcase-caption">
						<div class="picDescription" style="float: left"><?php echo ($picture->description == "" ? "" : "\"") ?><?php echo $picture->description ?><?php echo ($picture->description == "" ? "" : "\"") ?></div>
						
						<div style="font-size: 0.7em; text-align: right; float: right;"><?php echo date("Y F j l g:i a", strtotime($picture->timetaken))?></div>
					</div>
				</div>
		
		<?php }?>
		
		</div>
				
		</div>
				
		<?php
			if ($picturesFound) {
				$chosenDate = date("Y-m-d", strtotime($picture->timetaken));
				$diplayDate = date("Y F j l", strtotime($picture->timetaken));
				$pictureTime = date("g:i a", strtotime($picture->timetaken));
			} 

			 if ($statusUpdateFound) {
				$chosenDate = date("Y-m-d", strtotime($statusUpdate->theDate));
				$diplayDate = date("Y F j l", strtotime($statusUpdate->theDate));
				$statusUpdateTime = date("g:i a", strtotime($statusUpdate->theDate));
			 }
			 
			 $_SESSION['chosenDate'] = $chosenDate;
			 $_SESSION['diplayDate'] = $diplayDate;
		?>
		
		<script type="text/javascript">
				$("#chosenDate").val("<?php echo$chosenDate?>");
				$("#diplayDateInput").val("<?php echo$diplayDate?>");
		</script>
		
		 
		<div class="formlabel" style="display: <?php echo$picturesFound? "none" : "block"; ?>;">No Pictures taken on this day.</div>
		

		
		<div style="padding-top: 5px; position: relative; left: 0px; background-image:  url(../images/ui-bg_highlight-soft_100_eeeeee_1x100.png) 50% top repeat-x; width:100%; background-color: #EEE; display: <?php echo$picturesFound? "block" : "none"; ?>;">
			<a href="#" onclick="callPictureRotate('left');" title="Rotate picture left"><img src="/images/rotate-left.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="callPictureRotate('right');" title="Rotate picture right"><img src="/images/rotate-right.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showCaptionOverlay();" title="Edit caption"><img src="/images/comment.png"width="22"/></a>&nbsp;&nbsp;
			<a href="#" onclick="showDateTakenOverlay();" title="Edit picture date"><img src="/images/calendar.png"width="22"/></a>
		</div>
		<br/><br/>
		<div class="formlabel" style="display: <?php echo$statusUpdateFound? "block" : "none"; ?>;">
			 <div style="font-size: 1.1em; text-align: left;">"<?php echo $statusUpdate->message ?>"</div><br/>
			 <div style="font-size: 0.8em; text-align: right;"><?php echo $statusUpdateTime ?></div>
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
		<div id="picturedatepicker"></div>
		<br/>
		<div><span style="font-size: 1.1em; color: grey;">The picture was taken on </span> <span id="pictureDate" style="font-size: 1.3em; color: grey; font-weight: bold;"></span> at
		<span id="timeSelectorDiv">
			
		</span>
		
		</div>
</div>	

<script>
	$(function() {
		$( "#picturedatepicker" ).datepicker({
			numberOfMonths: 1,
			showButtonPanel: true,
		 	onSelect: function(dateText, inst) { 
		 		$("#pictureDate").html(dateText);
		   }
		});
	});

	$('#picturedatepicker').datepicker("option", "dateFormat", "yy-mm-dd" );
</script>