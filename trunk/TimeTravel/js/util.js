var loading = false;
var calls = 0;

$(document).ready(function(){
	$('.input_Description_active').click(function(){
        $(this).removeClass('input_Description_active').val('');
    });
});


//sets a function to be called when Enter is pressed on an element
function setEnterAction(element, theFunction){
	$(element).keypress(function(event){
		if(event.keyCode == '13') { 
			if (typeof theFunction == "function"){
				theFunction();
			}
		}
	});
}

function showMessage(message, msgtype){
	if (msgtype == "success"){
		$("#message").find("td").attr("bgcolor", "#11cc00");
	} else if (msgtype == "error"){
		$("#message").find("td").attr("bgcolor", "#FF8566");
	} else if (msgtype == "warning"){
		$("#message").find("td").attr("bgcolor", "#FFCC00");
	}

	$("#message").find("td").html(message);
	$("#message").fadeIn("slow");
}


function showLoading(flag){
	if (calls > 0){
		return;
	}
		
	if (flag)
		$("#loadingPic").show();
	else 
		$("#loadingPic").hide();
}


function populateTime(hour, minute){
	$(hour).html("");
	for (var i=0; i<24; i++){
		$(hour).append("<option>"+ (i < 10 ? ("0"+i) : i) +"</option>");
	}
	
	$(minute).html("");
	$(minute).append("<option>00</option>");
	$(minute).append("<option>15</option>");
	$(minute).append("<option>30</option>");
	$(minute).append("<option>45</option>");
}

function checkVersion(){
  var msg = "You're not using Internet Explorer.";
  var ver = getInternetExplorerVersion();

  if ( ver > -1 )
  {
    if ( ver >= 8.0 ) 
      msg = "You're using a recent copy of Internet Explorer."
    else
      msg = "You should upgrade your copy of Internet Explorer.";
  }
  alert( msg );
}

function parseResult(data){
	if (isInternetExplorer()){
		var temp = data;
		data = new ActiveXObject("Microsoft.XMLDOM");
		data.async = "false";
		data.loadXML(temp);
	}
	return data;
}

function getInternetExplorerVersion()
// Returns the version of Internet Explorer or a -1
// (indicating the use of another browser).
{
	var rv = -1; // Return value assumes failure.
	if (navigator.appName == 'Microsoft Internet Explorer') {
		var ua = navigator.userAgent;
		var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null)
			rv = parseFloat(RegExp.$1);
	}
	return rv;
}

function isInternetExplorer(){
	  var result = false;
	  var ver = getInternetExplorerVersion();

	  if ( ver > -1 )
	  {
	    if ( ver >= 8.0 ) 
	     result = true;
	    else
	     result = true;
	  }
	 return result;
}