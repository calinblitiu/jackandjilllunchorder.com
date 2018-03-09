<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	$_SESSION["user_id"] = null;
	session_destroy();

	header("Location:index");
	exit;
?>
