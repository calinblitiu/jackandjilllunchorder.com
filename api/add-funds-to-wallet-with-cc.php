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



	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];

	$amount = $_POST["amount"];
	$cc = $_POST["cc"];
	$mm = $_POST["mm"];
	$yyyy = $_POST["yyyy"];
	$cvv = $_POST["cvv"];
	$namec = $_POST["name"];


	// fix amount . put 00 as suffix
	$famount = $amount . "00";

	$error = null;
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

	// create customer eway token id first
	if (strpos(trim($namec), " ") > -1) {
		$arr = explode(" ", $namec);
		$fname = $arr[0];
		$lname = $arr[1];
	} else {
		$fname = $namec;
		$lname = $namec;
	}
	$customer = [
		'Title' => 'Mr.',
		'FirstName' => $fname,
		'LastName' => $lname,
		'Country' => 'au',
		'CardDetails' => [
			'Name' => $namec,
			'Number' => '4444333322221111',
			'ExpiryMonth' => '12',
			'ExpiryYear' => '25',
			'CVN' => '123',
		]
	];
	$response = $client->createCustomer(\Eway\Rapid\Enum\ApiMethod::DIRECT, $customer);
	if ($response->Customer->TokenCustomerID == null) {
		$error = "Error Token creation failed " . var_dump($response);
	
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
		exit(json_encode($response));
	}

	// update token if in mmber table
	$arrData = ["eway_token_id"=>$response->Customer->TokenCustomerID];
	$members->update($arrData, $userId);
	
	$transaction = [
		'Customer' => [
			'CardDetails' => [
					'Name' => $namec,
				'Number' => $cc, /*'4444333322221111',*/
				'ExpiryMonth' => $mm,
				'ExpiryYear' => substr($yyyy,2,2),
				'CVN' => $cvv,
			]
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
		$error = "Error Card transaction failed " . var_dump($response);
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
		exit(json_encode($response));

	}
	// update db

	$arrData = ["member_id"=>$userId, "date"=>date("Y-m-d h:I:s"), "txn_id"=>$txnId,
				"amount"=>$amount, "details"=>"Wallet update via CC"];

	$credits->update($arrData, 0);

	if ($credits->mError != null && $credits->mError != "") {
		$error =  $credits->mError;
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
		exit(json_encode($response));

	} else {


	}

	exit(json_encode($response));
	
?>
