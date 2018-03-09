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

	define("MAXPAGELINKS", 1000);


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subsItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$subsId = $_POST["subscription_id"];
	$startPage = $_POST["start_page"];
	$rowsPerPage = $_POST["rows_per_page"];
	

	$itemCount = $subsItems->getCountForSubscription($subsId);
	$irows = $subsItems->getRowsForSubscription($subsId, 0, $itemCount);
	for($i = 0; $i < count($irows); $i++) {
		$irow = $irows[$i];
		$productRow = $products->getRowById("ID", $irow["product_id"]);
		$studentRow = $products->getRowById("ID", $irpw["student_id"]);
		
		$irow["item"] = $productRow["name"];
		$irow["student"] = $studentRow["name"];

		$irows[$i] = $irow;
	}

	$response["data"] = $irows;

	exit(json_encode($response));

	
?>
