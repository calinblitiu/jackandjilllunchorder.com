<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/allergies-master.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];
	$id = $_POST["student_id"];
	$name = $_POST["name"];
	$school = $_POST["school_id"];
	$class = $_POST["class_id"];
	$dob = date("Y-m-d", strtotime($_POST["dob"]));
	$allergiesString = $_POST["allergy_ids"];
	$allergyIds = implode("," , $allergiesString);
	$otherAllergies = $_POST["other_allergies"];

	$arrData = ["school_id"=>$school, "class_id"=>$class, "dob"=>$dob, "allergies"=>$allergyIds,
				"other_allergies"=>$otherAllergies, "name"=>$name];

	$students->update($arrData, $id);
	if ($students->mError != null && $students->mError != "") {
		$response["response_code"] = "ERROR";
		$response["error"] = $students->mError;
	} else {

	}

	exit(json_encode($response));



?>
