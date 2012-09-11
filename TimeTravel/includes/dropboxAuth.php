<?php 
require_once(dirname(dirname(__FILE__)) .'/bootstrap.php');

?>
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		window.location = "/index.php?response=dropboxAuthenticated";
	});
</script>