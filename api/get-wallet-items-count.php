<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/credits.php");



	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$userId = $_POST["userid"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$rowCount = $credits->getCountForMember($userId);

	$response["data"] = $rowCount;

	exit(json_encode($response));
	
?>
