function rotatePicture(pictureId, direction, callback, src){
	var url = "controller.php";
	var parms = new Object();
	parms["action"] = "rotatePicture";
	parms["pictureId"] = pictureId;
	parms["direction"] = direction;
	
	$.post(url, parms, function(resultData) {
		resultData = parseResult(resultData);
		//alert(resultData);
		var errorCode = $(resultData).find("code").text();
		if (errorCode == 0) {

			if (typeof callback == "function"){
				callback(src);
			}
			
		} else {
			alert($(resultData).find("errMessage").text());
		}
	});
}

function hideShowCaseButtons(div){
	$(div).find(".showcase-arrow-next").hide();
	$(div).find(".showcase-arrow-previous").hide();
	
	 $(div).hover(function(){
		 $(this).find(".showcase-arrow-next").fadeIn("normal");
		 $(this).find(".showcase-arrow-previous").fadeIn("normal");
	 			}, function(){
		 $(this).find(".showcase-arrow-next").fadeOut("normal");
		 $(this).find(".showcase-arrow-previous").fadeOut("normal");
	});
	
}