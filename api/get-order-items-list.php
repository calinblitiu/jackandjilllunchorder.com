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

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$orderId = $_POST["id"];
	
    $itemCount = $orderItems->getCountForOrder($orderId);
	$irows = $orderItems->getRowsForOrder($row["ID"], 0, $itemCount);

	for($i = 0; $i < count($irows); $i++) {
		$irow = $irows[$i];
		$productRow = $products->getRowById("ID", $irow["product_id"]);
		$studentRow = $products->getRowById("ID", $irow["student_id"]);
		
		$irow["item"] = $productRow["name"];
		$irow["student"] = $studentRow["name"];

		$irows[$i] = $irow;
	}

	$response["data"] = $irows;

	exit(json_encode($response));
	

?>
