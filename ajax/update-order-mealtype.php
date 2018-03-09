<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	// get date
	$mealType = $_POST["mt"];
	$oid = $_POST["oid"];
	

	// check if this order belongs to the user
	$row = $orders->getRowbyId("ID", $oid);
	if (!$row || $row["member_id"] != $userId) {
		exit("This order does not belong to you");
	}

	// check if order status is received
	if ($row["status"] != ORDER_STATUS_RECEIVED) {
		exit("Order cannot be edited since its status has changed.");
	}
	
	$arrData = ["meal_type"=>$mealType];
	$orders->update($arrData, $oid);
	if ($orders->mError != null && $orders->mError != "") {
		 	exit("Error updating order:" . $orders->mError);
	 } else {
		 exit("");
	}
	
?>
