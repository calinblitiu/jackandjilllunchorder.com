<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];
	
	$row = $members->getRowById("ID", $userId);

	$response["data"] = $row;

	exit(json_encode($response));
	

?>
