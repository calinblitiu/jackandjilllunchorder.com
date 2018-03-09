<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	define("MAXPAGELINKS", 1000);

	$subsId = $_POST["subscription_id"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subsItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$row = $subscriptions->getRowById("ID", $subsId);
		
	$studentRow = $students->getRowById("ID", $row["student_id"]);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
	$memberRow = $members->getRowById("ID", $row["member_id"]);

	$row["student"] = $studentRow["name"];
	$row["school"] = $schoolRow["school"];
	$row["class"] = $classRow["name"];
	$row["member"] = $memberRow["fname"] . " " . $memberRow["lname"];

	$response["data"] = $row;
	exit(json_encode($response));

?>
