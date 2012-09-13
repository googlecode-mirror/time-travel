<?php 
require_once(dirname(dirname(__FILE__)) .'/util.php');
require_once(dirname(dirname(__FILE__)) .'/services/DropboxService.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/DropboxFile.php');\

error_reporting(E_ERROR | E_PARSE);
session_start();


if ($_SESSION["dropbox"] == "authenticated"){
	require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');
	$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
	$dropbox = new \Dropbox\API($OAuth);
}
?>
<script type="text/javascript">
<?php 
		$action = "";
		if (isset($_GET["action"])){
			$action = $_GET["action"];
		} else {
			
		}
		$service = new DropboxService();

	?>

params.action = '<?php echo $action?>';
var lastFile = '<?php echo isset($_GET['lastFile']) ? $_GET['lastFile'] : ""?>';
dirToSync = '<?php echo (isset($_GET['action']) && ($_GET['action']=="syncDir")) ? $_GET['dir'] : ""?>';

$(document).ready(function(){
	$("button").button();

	if (lastFile == "none"){
		var fileToSync = $(".syncFile:first").val();
		$("#contentArea1").load("includes/fetchDropboxFile.php?filename="+fileToSync, function(){
			$("#contentArea1").load("includes/dropboxSetup.php?action=syncDir&dir="+ encodeURIComponent(dirToSync)+"&lastFile="+encodeURIComponent(fileToSync));
		});
	} else if (lastFile != "") {
		 currentFile = $(".syncFile[value='"+ lastFile +"']");
		 $(currentFile).attr('checked','checked');
		 nextFile = $(currentFile).parent().next().find(".syncFile").val();
		// checkAllPreviousFiles($(currentFile).val());
		if ($(currentFile).parent().next().find(".syncFile").size() > 0){
			 $("#contentArea1").load("includes/fetchDropboxFile.php?filename="+encodeURIComponent(nextFile), function(){
					$("#contentArea1").load("includes/dropboxSetup.php?action=syncDir&dir="+encodeURIComponent(dirToSync)+"&lastFile="+encodeURIComponent(nextFile));
			});
		}
	}

	
});


function checkAllPreviousFiles(currentFile){
	if (currentFile == $(".syncFile:first").val()) return;
	while (currentFile != $(".syncFile:first").val()){
		$(currentFile).attr('checked','checked');
		currentFile = $(currentFile).parent().prev().find(".syncFile").val();
	}
}

function loadRootDir(){
	$("#contentArea1").load("includes/dropboxSetup.php?action=loadRootDir");
}

function loadDirectory(dir){
	$("#contentArea1").load("includes/dropboxSetup.php?action=loadDir&dir="+encodeURIComponent(dir));
}

function enableSubmitBtn(){
	$("#submitBtn").button("enable");
}

function syncSelectedDir(){
	dirToSync = $("#folderList input:radio:checked").val();
	$("#contentArea1").load("includes/dropboxSetup.php?action=syncDir&dir="+encodeURIComponent(dirToSync)+"&lastFile=none");
}
</script>

<div>
	<img src="/images/gmail.png" width="80"></img>
</div>
<br/><br/>

<!-- DEFAULT -->
<?php if ($action == ""){?>

<div id="gmailRegisterDiv" class="formlabel">Setup your Dropbox folders
	<button onclick="loadRootDir();">Sync Dropbox</button>
</div>
<?php }?>


<!-- LOAD DIRECTORIES -->
<?php 
if ($action == "loadRootDir" || $action == "loadDir"){
?>

<div id="folderList">
<div style="text-align: left;"><a href="#" onclick="loadDirectory('<?php echo "root" ?>')"><img src="/images/arrow_up.png" width="20"> </a></div>
<br/>
<?php 
	$fileList = $service->readDirContents($action == "loadRootDir" ? "root" :  $_GET["dir"], $dropbox);
	
	foreach ($fileList as $file){
		if (!$file->is_dir) continue;
?>

<div class="formlabel" style="text-align: left;">
<input type="radio" name="syncDir" onchange="enableSubmitBtn();" value="<?php echo $file->name ?>"/>
<img src="/images/folder.png" width="20"> <a href="#" onclick="loadDirectory('<?php echo $file->name ?>')"><?php echo Util::getFileNameFromPath($file->name) ?></a></div>

<?php 
	}
?>
<button id="submitBtn" onclick="syncSelectedDir();" disabled="disabled">Sync Selected Folder</button>
<?php 
}
?>
</div>


<!-- LOAD DIRECTORIES -->
<?php 
if ($action == "syncDir"){
?>
<div id="syncFolderList">
<?php 
	$fileList = $service->readDirContents($_GET["dir"], $dropbox);
	foreach ($fileList as $file){
		if ($file->is_dir) continue;
?>
<div class="formlabel" style="text-align: left;">
<input class="syncFile" type="checkbox" disabled value="<?php echo $file->name ?>"/>
<img src="/images/jpeg.png" width="20"> <?php echo Util::getFileNameFromPath($file->name) ?></div>

<?php }?>
<button id="cancelBtn" onclick="cancelSync();">Cancel</button>
<?php }?>
</div>
<div id="msgDlg"></div>