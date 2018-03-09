<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];
	$delId = $_POST["student_id"];

	$checkRow = $students->getRowById("ID", $delId);
	if (!$checkRow || $checkRow["user_id"] != $userId) {
		$response["response_code"] = "ERROR";
		$response["error"] = "This is an invalid student entry";
	} else {
		$students->delete($delId);
	}
	
	exit(json_encode($response));

?>
