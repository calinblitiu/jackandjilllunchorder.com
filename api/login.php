<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "../classes/members.php");
	// require_once($g_docRoot . "../classes/settings.php");
	// require_once($g_docRoot . "../classes/cart.php");
	require_once($g_docRoot . "../classes/credits.php");
	require_once($g_docRoot . "../classes/orders.php");
	require_once($g_docRoot . "../classes/subscription-wallet-payments.php");

	// require_once( "../classes/members.php");
	// require_once( "../classes/settings.php");
	// require_once( "../classes/cart.php");
	// require_once( "../classes/credits.php");
	// require_once( "../classes/orders.php");
	// require_once( "../classes/subscription-wallet-payments.php");
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	// $cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$error = null;

		$email = $_POST["email"];
		$pwd = $_POST["pwd"];
		


		$row = $members->authenticate($email, getPwdHash($pwd));
		if ($row && $row["emailid"] == $email) {
		
			$totalRow = $credits->getTotalCreditsForMember($row["ID"]);
			$totalCredits = 0;
			if ($totalRow)
				$totalCredits = $totalRow["total"];

			// get total payments
			$totalDebits = 0;
			$ordersRow = $orders->getTotalPurchasesForMember($row["ID"]);
			if ($ordersRow)
				$totalDebits = $ordersRow["total"];

			// get total subscription payments
			$totalSDebits = 0;
			$subsRow = $spayments->getTotalForMember($row["ID"]);
			if ($subsRow)
				$totalSDebits = $subsRow["total"];

			// check items in cart
			// $itemsCount = $cart->getCountForAUser($row["ID"]);
			
			$arrData = array();
			$arrData["user_id"] = $row["ID"];
			$arrData["email"] = $row["emailid"];
			$arrData["name"] = $row["fname"] . " " . $row["lname"];
		    // $arrData["cart_count"] = $itemsCount;
			$arrData["wallet_balance"] = $totalCredits - ($totalDebits + $totalSDebits);
			
			$response["data"] = $arrData;
			
		} else {
			$error = "Login credentials were invalid";
		}
	if ($error != null) {
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
	}
	exit(json_encode($response));
	// echo 'test';

?>
