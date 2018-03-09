<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");

	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];
	
	// get total subscription payments
	$totalSDebits = 0;
	$subsRow = $spayments->getTotalForMember($userId);
	if ($subsRow) {
		$totalSDebits = $subsRow["total"];
		$response["data"] = $totalSDebits;
	} else {
		$response["response_code"] = "ERROR";
		$response["error"] = "No data found";
	}

	exit(json_encode($response));

?>
