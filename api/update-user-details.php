<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");



	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userid = $_POST["userid"];
	$emailid = $_POST["emailid"];
	$fname = $_POST["fname"];
	$lname = $_POST["lname"];
	
	$members->update($arrData, $userId);
	if ($members->mError != null && $members->mError != "") {
				$error = $members->mError;
				$response["response_code"] = "ERROR";
				$response["error"] = $error;

	} else {
	}

	exit(json_encode($response));
	

?>
