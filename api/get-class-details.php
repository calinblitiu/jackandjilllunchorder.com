<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/classes.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$classId = $_POST["class_id"];

	$row = $classes->getRowById("ID", $classId);
	$response["data"] = $row;

	exit(json_encode($response));


?>
