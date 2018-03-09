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
	$amount = $_POST["amount"];
	$cc = $_POST["EWAY_CARDNUMBER"]; //$_POST["card_number"];
	$mm = $_POST["mm"];
	$yyyy = $_POST["yyyy"];
	$cvv = $_POST["EWAY_CARDCVN"]; //$_POST["card_cvn"];
	$namec = $_POST["namec"];
	
	// fix amount . put 00 as suffix
	$famount = $amount . "00";

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
			'Number' => $cc,
			'ExpiryMonth' => trim($mm),
			'ExpiryYear' => substr($yyyy,2,2),
			'CVN' => trim($cvv),
		]
	];
	$response = $client->createCustomer(\Eway\Rapid\Enum\ApiMethod::DIRECT, $customer);
	if ($response->Customer->TokenCustomerID == null) {  ?>
	    <script>
		alert("Error , Your Card is invalid or has expired.");
		window.history.back();
		</script>
	  <?php 
	  exit;
	  
	   
	}

	// update token if in mmber table
	$arrData = ["eway_token_id"=>$response->Customer->TokenCustomerID];
	$members->update($arrData, $userId);
	
	$transaction = [
		'Customer' => [
			'CardDetails' => [
					'Name' => $namec,
				'Number' => $cc, /*'4444333322221111',*/
				'ExpiryMonth' => trim($mm),
				'ExpiryYear' => substr($yyyy,2,2),
				'CVN' => trim($cvv),
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
	} else { ?>

	  <script>
		alert("Error , Your Card transaction failed.");
		window.history.back();
		</script>

	<?php
		exit;
	}
	// update db

	$arrData = ["member_id"=>$userId, "date"=>date("Y-m-d h:I:s"), "txn_id"=>$txnId,
				"amount"=>$amount, "details"=>"Wallet update via CC"];

	$credits->update($arrData, 0);

	if ($credits->mError != null && $credits->mError != "") { ?>

	  <script>
		alert("Error , Credit Error ");
		window.history.back();
		</script>

	<?php
	 exit; }
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


	   header("Location: " . $g_webRoot . "my-wallet?success=1");
	   exit("");

	}
	
	
?>
