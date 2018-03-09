<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];

	// check if user has enough balance to make payment
	$totalCredits = 0;
	$totalRow = $credits->getTotalCreditsForMember($userId);
	$totalCredits = 0;
	if ($totalRow)
		$totalCredits = $totalRow["total"];
		
	// get total payments
	$totalDebits = 0;
	$ordersRow = $orders->getTotalPurchasesForMember($userId);
	if ($ordersRow)
		$totalDebits = $ordersRow["total"];
		
	// check cart total
	$rowCount = $cart->getCountForAUser($userId);
	$rows = $cart->getListForAUser($userId, 0, $rowCount, "date_asc");
	$cartTotal = 0;
	foreach($rows as $row) {
		$cartTotal += $row["price"] * $row["qty"];
	}

	if ((abs($totalCredits-$totalDebits) - abs($cartTotal)) > 0.00001) {
		
	} else {
		$response["response_code"] = "ERROR";
		$response["error"] = "Error - wallet does not have enough balance";
	}

	exit(json_encode($response));
	
	


?>
