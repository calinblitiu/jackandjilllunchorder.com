<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/offdays.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$schoolId = $_POST["school_id"];
	
	$offdays = new OffDays($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$rows = $offdays->getListForASchool($schoolId, 0, 500);

	$response["data"] = $rows;

	exit(json_encode($response));
	
?>
