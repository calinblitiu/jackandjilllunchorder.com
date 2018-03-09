<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");

	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	$srow = $settings->getRowById("ID", 1);
	
	// get params
	$amount = $_POST["amt"];
	$cvv = $_POST["cvv"];
	
	// fix amount . put 00 as suffix
	$famount = $amount . "00";

	// check if user has already got eway token
	$mrow = $members->getRowById("ID", $userId);
	$tokenId = $mrow["eway_token_id"];
	if ($tokenId == null || $tokenId == "") {
		exit("Your card information is not available. Please use manual card payment");
	}
	// make payment
	if ($srow["eway_sandbox_flag"] == 1) {
		$apiKey = EWAY_SANDBOX_API_KEY;
		$apiPassword = EWAY_SANDBOX_API_PWD;
		$apiEndpoint = \Eway\Rapid\Client::MODE_SANDBOX;
	} else {
		$apiKey = EWAY_API_KEY;
		$apiPassword = EWAY_API_PWD;
		$apiEndpoint = \Eway\Rapid\Client::MODE_PRODUCTION;
	
	}
	$client = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);


	// the data below is not used as it uses CVV
	/*$transaction = [
		'Customer' => [
			'TokenCustomerID' => $tokenId,
			'CardDetails' => [
				'CVN' => $cvv,
			]

		],
		'Payment' => [
			'TotalAmount' => $famount,
		],
		'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
	];*/

	// using this data without CVV
	$transaction = [
		'Customer' => [
			'TokenCustomerID' => $tokenId

		],
		'Payment' => [
			'TotalAmount' => $famount,
		],
		'TransactionType' => \Eway\Rapid\Enum\TransactionType::RECURRING,
	];

	$response = $client->createTransaction(\Eway\Rapid\Enum\ApiMethod::DIRECT, $transaction);
	if ($response->TransactionStatus) {
		$txnId = $response->TransactionID;
	} else {
		exit("Error Card transaction failed " . var_dump($response));
	}
	// update db

	$arrData = ["member_id"=>$userId, "date"=>date("Y-m-d h:I:s"), "txn_id"=>$txnId,
				"amount"=>$amount, "details"=>"Wallet update via CC"];

	$credits->update($arrData, 0);

	if ($credits->mError != null && $credits->mError != "")
		exit("Error=" . $credits->mError);
	else {

			// update wallet balance in session

			$totalRow = $credits->getTotalCreditsForMember($userId);
			$totalCredits = 0;
			if ($totalRow)
				$totalCredits = $totalRow["total"];

			// get total payments
			$totalDebits = 0;
			$ordersRow = $orders->getTotalPurchasesForMember($userId);
			if ($ordersRow)
				$totalDebits = $ordersRow["total"];

			// get total subscription payments
			$totalSDebits = 0;
			$subsRow = $spayments->getTotalForMember($userId);
			if ($subsRow)
				$totalSDebits = $subsRow["total"];
				
			$_SESSION["wallet_balance"] = $totalCredits - ($totalDebits + $totalSDebits);


	   exit("");

	}
	
	
?>
