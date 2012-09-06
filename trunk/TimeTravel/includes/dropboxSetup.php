<?php 
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');
require_once(dirname(dirname(__FILE__)) .'/util.php');
require_once(dirname(dirname(__FILE__)) .'/services/DropboxService.php');
require_once(dirname(dirname(__FILE__)) .'/viewbean/DropboxFile.php');
?>
<script type="text/javascript">
var currentFile = '<?php echo isset($_GET['currentFile']) ? $_GET['currentFile'] : "none"?>';

$(document).ready(function(){
	$("button").button();
	
	if (currentFile == "first"){
		var fileToSync = $(".syncFile:first").val();
		alert("filetosync: "+fileToSync);
		$("#contentArea1").load("includes/fetchDropboxFile.php?filename="+fileToSync, function(){
			$("#contentArea1").load("includes/dropboxSetup.php?action=syncDir&dir="+dirToSync+"&currentFile=next");
		});
	}

});

function loadRootDir(){
	$("#contentArea1").load("includes/dropboxSetup.php?action=loadRootDir");
}

function loadDirectory(dir){
	$("#contentArea1").load("includes/dropboxSetup.php?action=loadDir&dir="+dir);
}

function enableSubmitBtn(){
	$("#submitBtn").button("enable");
}

function syncSelectedDir(){
	var dirToSync = $("#folderList input:radio:checked").val();
	$("#contentArea1").load("includes/dropboxSetup.php?action=syncDir&dir="+dirToSync+"&currentFile=first");
}
</script>

<?php 
	
	$OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
	$dropbox = new \Dropbox\API($OAuth);


	$action = "";
	if (isset($_GET["action"])){
		$action = $_GET["action"];
	}
	$service = new DropboxService();

?>
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