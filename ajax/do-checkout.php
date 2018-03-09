<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");
	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "PHPMailer-master/PHPMailerAutoload.php");

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);



	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	$srow = $settings->getRowById("ID", 1);
	
	// get notes
	$notes = $_POST["notes"];
	
	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

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
	
	if ((($totalCredits-($totalDebits + $totalSDebits)) - ($cartTotal)) >= 0.0) {
		// get student details
		$studentRow = $students->getRowById("ID", $checkCart[0]["student_id"]);
		$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
		$onlyStudentName =  $studentRow["name"] ;
		$studentDetails = $studentRow["name"] . " Class " . $classRow["name"] .", " . 
				$schoolRow["name"];
				
		 // add row to orders table
		 $invoice = $userId . "-" . date("Ymd-His");
		 $arrOrder = ["member_id"=>$userId, "date"=>date("Y-m-d H:i:s"), "invoice"=>$invoice,
		 			  "gross_total"=>$cartTotal, "net_total"=>$cartTotal, "tax_percent"=>0,
					  "txn_id"=>$invoice, "details"=>"Paid from Wallet", "notes"=>"",
					  "used_credits"=>1, "student_id"=>$checkCart[0]["student_id"],
					  "status"=>ORDER_STATUS_RECEIVED, "meal_type"=>$checkCart[0]["meal_type"],
					  "delivery_date"=>$checkCart[0]["order_date"],
					  "notes"=>$notes];

	 	 $newId = $orders->update($arrOrder, 0);
		 if ($orders->mError != null && $orders->mError != "") {
		 	exit("Error creating order:" . $orders->mError);
		 } else {
		 
		 	// add order items
			foreach($rows as $row) {

				$arrItems = ["member_id"=>$userId, "purchase_id"=>$newId, 
				"product_id"=>$row["product_id"], "item_price"=>$row["price"],
				"item_qty"=>$row["qty"], "student_id"=>$row["student_id"],
				"meal_type"=>$row["meal_type"]];

				$orderItems->update($arrItems, 0);
				if ($orderItems->mError != null && $orderItems->mError != "") {
				 	exit("Error creating order:" . $orderItems->mError);
				}

			}

			// save order details in session to show in confirmation page
			$_SESSION["order_conf_id"] = $newId;
			$_SESSION["order_conf_date"] = $checkCart[0]["order_date"];
			$_SESSION["order_conf_total"] = $cartTotal;			

			$deliveryDate = $checkCart[0]["order_date"];
			$orderAmount = $cartTotal;
			// clear cart	
			$cart->deleteByExpression("user_id=" . $userId);
		    $_SESSION["cart_count"] = "0";


					
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
						var_dump($response);
					}

					// update db
					if (!$cardError) {
						$arrData = ["member_id"=>$userId, "date"=>date("Y-m-d h:I:s"), "txn_id"=>$txnId,
							"amount"=>$amount, "details"=>"Wallet update via CC"];

						$credits->update($arrData, 0);
						if ($credits->mError != null && $credits->mError != "")
							echo("Error=" . $credits->mError . "\n");
					}

					// take total again
					$totalRow = $credits->getTotalCreditsForMember($userId);
					$totalCredits = 0;
					if ($totalRow)
						$totalCredits = $totalRow["total"];



			}

			// update wallet balance in session
			$_SESSION["wallet_balance"] = $totalCredits - ($totalDebits + $totalSDebits);

			// check notification status of user and send notifications
			$mrow = $members->getRowById("ID", $userId);
			
			// send notification mail
			if ($mrow["notify_neworder_email"] == 1) {
				$subject = "JackAndJill Order Confirmation";
				$content = file_get_contents($g_docRoot . "mails/new-order.html");
				$content = str_replace("#name#",  $mrow["fname"] . " " . $mrow["lname"], $content);
				$content = str_replace("#studentdetails#", $studentDetails , $content);

				$content = str_replace("#orderno#",  $newId, $content);
			    $content = str_replace("#orderamount#",  number_format($orderAmount,2), $content);
			    $content = str_replace("#deliverydate#",  date("Y-M-d", strtotime($deliveryDate)), $content);
				$content = str_replace("#printlink#",  "http://" . $_SERVER["SERVER_NAME"] . $g_webRoot . "print-invoice/" . $invoice, $content);

				$email = $mrow["emailid"];
				sendMail($g_fromEmailId, $g_fromName, $email, $name, $subject, $content);
			}

				// send sms to mobile
			if ($mrow["notify_neworder_sms"] == 1) {
				$msg = "Your new order details are Order#" . $newId . " placed for $" . number_format($orderAmount,2) . ". Order for: " . $onlyStudentName .  " Delivery on:" . date("Y-M-d", strtotime($deliveryDate));
				
				$smsURL = "https://1s2u.com/sms/sendsms/sendsms.asp?username=" . $srow["sms_api_userid"] . "&password=" . $srow["sms_api_pwd"] . "&mt=0&fl=0&sid=JACKANDJILL&msg=" . urlencode($msg) . "&mno=" . $mrow["mobile"]. "&ipcl=127.0.0.1";

				 $ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $smsURL);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 $return = curl_exec ($ch);
				 curl_close ($ch);
				 //echo($smsURL . "<br>");
				 //var_dump($return);
			}

			exit("");
			
		}
		
	} else {
		exit("ERROR: Wallet does not have balance");
	}
	

function sendMail($from_emailId, $from_name, $to_emailId, $to_name, $subject, $body) {
	global $g_smtpServer, $g_smtpPort, $g_smtpUserId, $g_smtpPwd;
		
		/*echo($g_smtpServer. "," . $g_smtpPort ."," .  $g_smtpUserId ."," .  $g_smtpPwd 
 . "\n\n" . $from_emailId . "," .  $from_name ."," .  $to_emailId . "," .  $to_name . "," . $subject . "," .  ",userid=" . $g_smtpUserId . ", pwd=" . $g_smtpPwd . "\n". $body);*/
		$mail = new PHPMailer();
		$mail->isSMTP();
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $g_smtpServer;
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $g_smtpPort;

		//$mail->SMTPSecure ="tls";

		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username =  $g_smtpUserId;
		//Password to use for SMTP authentication
		$mail->Password = $g_smtpPwd ;
		//Set who the message is to be sent from
		$mail->setFrom($from_emailId, $from_name);
		//Set an alternative reply-to address
		$mail->addReplyTo($from_emailId, $from_name);
		//Set who the message is to be sent to
		$mail->addAddress($to_emailId, $to_name);
		//Set the subject line
		$mail->Subject =  $subject;
		$mail->msgHTML($body);
		//Replace the plain text body with one created manually
		$mail->Body = $body;
		$mail->IsHTML(true); 



		//send the message, check for errors
		if (!$mail->send()) {
			$msg =  $mail->ErrorInfo;
		} else {
			$msg = "";
		}				

		return $msg;
}
	
	
?>
