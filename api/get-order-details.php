<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	define("MAXPAGELINKS", 1000);

	$orderId = $_POST["order_id"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$row = $orders->getRowById("ID", $orderId);
		
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
