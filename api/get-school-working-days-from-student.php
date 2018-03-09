<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$studentId = $_POST["student_id"];

	$studentRow = $students->getRowById("ID", $studentId);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);

	$response["data"] = $schoolRow;

	exit(json_encode($response));
	

?>
