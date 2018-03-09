<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	session_destroy();

	header("Location:index.php");
	exit;
?>
