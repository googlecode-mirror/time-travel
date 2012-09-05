<?php 
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');
?>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	
});

function displayEmailFolders(data){
		$("#gmailFolderList").filter(".folder").remove();
		$(data).find("list").children().each(function(){
			$('<tr class="folder"><td class="ui-widget-content"><input type="checkbox"/></td><td class="formlabel ui-widget-content">'+ $(this).html() +'</td></tr>').insertAfter($("#gmailFolderList-head"));
		})
		
		$("#gmailFolderList").show();
		$("#gmailFolderList").dialog( {
			bgiframe : true,
			autoOpen : false,
			width : 600,
			height : 600,
			position: ['center'],
			modal : true,
			resizable : false,
			title : "Choose folders to synchronize",
			buttons : {
				Submit : function() {
					$(this).dialog("destroy");
					showLoading(true);
					submitFolderList();
				},
				Cancel : function() {
					$(this).dialog("destroy");
				}
			}
		});

		$("#gmailFolderList").dialog("open");
	}

	function submitFolderList(){
		var folderList = "";
		$("#gmailFolderList").find("input[type='checkbox']:checked").each(function(){
			folderList += $(this).parent().next().html() +",";
			//alert($(this).parent().next().html());
		});
		console.log("FOLDERS: "+folderList);
		var url = "controller.php";
		var parms = new Object();
		parms["action"] = "saveProviderFolders";
		parms["type"] = 'sms_gmail';
		parms["folders"] = folderList;
		parms["syncStart"] = $("#syncStartYear").val()+"-"+$("#syncStartMonth").val()+"-01";

		$.post(url, parms, function(resultData) {
			resultData = parseResult(resultData);
			var errorCode = $(resultData).find("code").text();
			if (errorCode == 0) {
				alert("Thank you! We are now pulling your sms's from your provider. They will be available shortly.");
			} else {
				alert($(resultData).find("errMessage").text());
			}
			showLoading(false);
		});
	}

	function loadFolders(){
		$("#contentArea1").load("includes/dropboxSetup.php?action=loadFolders", function(){
			//updateDatePicker();
		});
	}
</script>

<?php 

	$action = "";
	if (isset($_GET["action"])){
		$action = $_GET["action"];
	}

?>
<div>
	<img src="/images/gmail.png" width="80"></img>
</div>
<br/><br/>
<div id="gmailRegisterDiv" class="formlabel">Setup your Dropbox folders
	<button onclick="loadFolders();">Setup Dropbox</button>
</div>


<?php 
	if ($action == "loadFolders"){
		
		$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
		$dropbox = new \Dropbox\API($OAuth);
		$accountInfo = $dropbox->metaData();
		var_dump($accountInfo);
?>


<?php }?>