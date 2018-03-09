<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	set_time_limit(50000);
	session_start();
	
	
	require_once("includes/globals.php");
	
	$currPath = getcwd();
	if (stripos($currPath, "/var/websites/jackjill") > -1) {
		 $g_connServer = "localhost";
		 $g_connUserid = "root";
		 $g_connPwd = "master";
		 $g_connDBName = "jackjill";
		 $g_docRoot = "/var/websites/jackjill/";
  		 $g_serverName = "jackjill.dev";
		 define("DEFAULT_TIME_ZONE", "Asia/Calcutta");
		 $g_webRoot = "/";	
		 $g_zendPath = "";
	} else {
		 $g_connServer = "localhost";
		 $g_connUserid = "med176_juser";
		 $g_connPwd = 'z!!9cMHoGUD8';
		 $g_connDBName ="med176_jackjill";
		 $g_docRoot = "/home/med176/public_html/jack/";
		 $g_serverName = "6";
	 
		 define("DEFAULT_TIME_ZONE", "America/Fort_Wayne");
		 $g_webRoot = "/jack/";
		 $g_zendPath = "" ;
		 $g_apiReferer = "http://mediawarrior.com";
	
	}


	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/settings.php");
	
	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$srow = $settings->getRowById("ID", 1);
	

	$count = $members->getMemberListCount(null, null);
	$rows = $members->getMemberList(null, null, "idasc", 0, $count);
	foreach($rows as $row) {
		echo($row["fname"] . " " . $row["lname"] . "\n");

		$amount = $row["auto_charge_amount"];
		echo("Auto charge amount:" . $amount . "\n");
		if ($amount == null || $amount < 1) {
			echo("Skipping as no auto charge amount set\n");
		}
		$tokenId = $row["eway_token_id"];
		if ($tokenId == null || $tokenId == "") {
			echo("Skipping as No eway token id found.\n");
			continue;
		}

		$totalRow = $credits->getTotalCreditsForMember($row["ID"]);
		$totalCredits = 0;
		if ($totalRow)
			$totalCredits = $totalRow["total"];

		// get total payments
		$totalDebits = 0;
		$ordersRow = $orders->getTotalPurchasesForMember($row["ID"]);
		if ($ordersRow)
			$totalDebits = $ordersRow["total"];

		if ($totalCredits - $totalDebits > 5) {
			echo("Balance :" . number_format($totalCredits - $totalDebits,2) . "\n");
			continue;
		} else {
			echo("Credits:" . $totalCredits . ", Debits:" .  $totalDebits . 
					", Balance: " . (number_format($totalCredits - $totalDebits,2)) . "\n");
		}
		
		// fix amount . put 00 as suffix
		if (stripos($amount, ".") == -1)
			$famount = $amount . "00";
		else
			$famount = str_replace(".", "", $amount);

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

		    $cardError = false;	
			$response = $client->createTransaction(\Eway\Rapid\Enum\ApiMethod::DIRECT, $transaction);
			if ($response->TransactionStatus) {
				$txnId = $response->TransactionID;
				echo("$" . $amount . " successfully charged.\n");
			} else {
				$cardError = true;
				echo("Error Card transaction failed " . var_dump($response));
				echo("\n");
			}
			
			// update db
			if (!$cardError) {
				$arrData = ["member_id"=>$row["ID"], "date"=>date("Y-m-d h:I:s"), "txn_id"=>$txnId,
					"amount"=>$amount, "details"=>"Wallet update via CC"];

				$credits->update($arrData, 0);

				if ($credits->mError != null && $credits->mError != "")
					echo("Error=" . $credits->mError . "\n");
			}
	
	} // foreach($rows as $row)
	
	exit("DONE");
?>
