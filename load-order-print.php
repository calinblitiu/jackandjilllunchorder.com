
<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("includes/globals.php")

	$id = $_POST["id"];
	header("Location: " . $g_webRoot . "ajax/print-order.php
?>
