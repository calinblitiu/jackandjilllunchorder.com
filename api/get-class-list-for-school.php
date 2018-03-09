<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/classes.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$schoolId = $_POST["school_id"];

	$rows = $classes->getListForASchool($schoolId, 0, 100);

	for($i= 0 ; $i < count($rows); $i++) {
		$row = $rows[$i];
		$crow = $classes->getRowById("ID", $row["ID"]);
		$row["name"] = $crow["name"];

		$rows[$i] = $row;
	}

	$response["data"] = $rows;

	exit(json_encode($response));


?>
