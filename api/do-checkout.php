<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");
	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');
	require_once($g_docRoot . "classes/settings.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$userId = $_POST["userid"];
	$notes = $_POST["notes"];


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

	// get total subscription payments
	$totalSDebits = 0;
	$subsRow = $spayments->getTotalForMember($userId);
	if ($subsRow)
		$totalSDebits = $subsRow["total"];

	// check cart total
	$rowCount = $cart->getCountForAUser($userId);
	$rows = $cart->getListForAUser($userId, 0, $rowCount, "date_asc");
	$cartTotal = 0;
	foreach($rows as $row) {
		$cartTotal += $row["price"] * $row["qty"];
	}

	$checkCart = $cart->getListForAUser($userId, 0, 1, "date_desc");
	
	if ((abs($totalCredits-($totalDebits + $totalSDebits)) - abs($cartTotal)) > 0.00001) {
		 // add row to orders table
		 $invoice = $userId . "-" . date("Ymd-His");
		 $arrOrder = ["member_id"=>$userId, "date"=>date("Y-m-d H:i:s"), "invoice"=>$invoice,
		 			  "gross_total"=>$cartTotal, "net_total"=>$cartTotal, "tax_percent"=>0,
					  "txn_id"=>$invoice, "details"=>"Paid from Wallet",
					  "used_credits"=>1, "student_id"=>$checkCart[0]["student_id"],
					  "status"=>ORDER_STATUS_RECEIVED, "meal_type"=>$checkCart[0]["meal_type"],
					  "delivery_date"=>$checkCart[0]["order_date"],
					  "notes"=>$notes];

	 	 $newId = $orders->update($arrOrder, 0);
		 if ($orders->mError != null && $orders->mError != "") {
		 		$response["response_code"] = "ERROR";
				$response["error"] = "Error creating order:" . $orders->mError;
				exit(json_encode($response));
				
		 } else {
		 
		 	// add order items
			foreach($rows as $row) {

				$arrItems = ["member_id"=>$userId, "purchase_id"=>$newId, 
				"product_id"=>$row["product_id"], "item_price"=>$row["price"],
				"item_qty"=>$row["qty"], "student_id"=>$row["student_id"],
				"meal_type"=>$row["meal_type"]];

				$orderItems->update($arrItems, 0);
				if ($orderItems->mError != null && $orderItems->mError != "") {
					$response["response_code"] = "ERROR";
					$response["error"] = "Error creating order:" .  $orderItems->mError;
					exit(json_encode($response));
				}

			}

			$arrResponse =  array();
			// save order details in session to show in confirmation page
			$arrResponse["order_conf_id"] = $newId;
			$arrResponse["order_conf_date"] = $checkCart[0]["order_date"];
			$arrResponse["order_conf_total"] = $cartTotal;			

			// clear cart	
			$cart->deleteByExpression("user_id=" . $userId);

					
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

			// auto update wallet balance if user token id is there
			$mrow = $members->getRowById("ID", $userId);
			$tokenId = $mrow["eway_token_id"];
			$amount = $mrow["auto_charge_amount"];

			if ($tokenId != null && $tokenId != "" &&
				$amount > 0 &&
			    ($totalCredits - ($totalDebits + $totalSDebits)) < 5) {
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

				if (stripos($amount, ".") == -1)
					$famount = $amount . "00";
				else
					$famount = str_replace(".", "", $amount);

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
					} else {
						$cardError = true;
						$response["response_code"] = "ERROR";
						$response["error"] = var_dump($response);
						exit(json_encode($response));
					}

					// update db
					if (!$cardError) {
						$arrData = ["member_id"=>$userId, "date"=>date("Y-m-d h:I:s"), "txn_id"=>$txnId,
							"amount"=>$amount, "details"=>"Wallet update via CC"];

						$credits->update($arrData, 0);
						if ($credits->mError != null && $credits->mError != "") {
							echo("Error=" . $credits->mError . "\n");
							$response["response_code"] = "ERROR";
							$response["error"] = "Error creating order:" .  $orderItems->mError;
							exit(json_encode("Error " . $credits->mError));

						}
					}

					// take total again
					$totalRow = $credits->getTotalCreditsForMember($userId);
					$totalCredits = 0;
					if ($totalRow)
						$totalCredits = $totalRow["total"];



			}

		 }
		
	} else {
		$response["response_code"] = "ERROR";
		$response["error"] = "ERROR: Wallet does not have balance";
	}

	exit(json_encode($response));
	
	


?>
