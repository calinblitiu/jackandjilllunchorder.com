<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/subs.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");
	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');
	require_once($g_docRoot . "PHPMailer-master/PHPMailerAutoload.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/students.php");

	
	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptionItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	$srow = $settings->getRowById("ID", 1);
	
	// get params
	$student = $_POST["student"];
	$mealDealQty = $_POST["mdqty"];
	$days= $_POST["days"];
	$payType = $_POST["paytype"];
	$reminder7am = $_POST["reminder7am"];
	$reminder7pm = $_POST["reminder7pm"];
	$amount = $_POST["amount"];
	$subsAmount = $amount;
	
	$cc = $_POST["EWAY_CARDNUMBER"];
	$mm = trim($_POST["mm"]);
	$yyyy = trim($_POST["yyyy"]);
	$cvv = $_POST["EWAY_CARDCVN"];
	$namec = $_POST["namec"];

	$ajax= $_POST["ajax"];		// if this is 1 then send all requests back as ajax
	

	// get student details
	$studentRow = $students->getRowById("ID", $student);
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
	$onlyStudentName =  $studentRow["name"] ;
	$studentDetails = $studentRow["name"] . " Class " . $classRow["name"] .", " . 
	$schoolRow["name"];

	$arrDays = explode(",", $days);
	
	// get all subs items
	$subsCount = $subs->getCountForAUser($userId);
	$subsRows = $subs->getListForAUser($userId, 0, $subsCount, "id_asc");
	
	// fix amount . put 00 as suffix
	if (strpos($amount, ".") == -1)
		$famount = $amount . "00";
	else
		$famount = str_replace(".", "", $amount);

	// check if user has already got eway token
	$mrow = $members->getRowById("ID", $userId);
	$tokenId = $mrow["eway_token_id"];

	// ignore token id if cc has been specified
	if ($cc != null && $cc != "")
		$tokenId = null;

	if ($payType == "WALLET") {

		if ($tokenId == null || $tokenId == "") { 
			if ($ajax == 1)
				exit("Your card information is not available. Please use manual EWay payment.");
		?>
		  <script>
			alert("Your card information is not available. Please use manual EWay payment.");
			window.history.back();
			</script>

		<?php
			exit;
		}

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
	    if (($totalCredits - ($totalDebits + $totalSDebits)) < 0.0) {
			if (ajax == 1)
				exit("Error , Your wallet does not have enough balance");
		?>
		 <script>
			alert("Error , Your wallet does not have enough balance.");
			window.history.back();
		 </script>
		 <?php 
		  exit;
		  
		}
		
	} // if paytype =="wallet"
	else if ($payType == "EWAY") {
	
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

		if ($tokenId == null || $tokenId == "") {
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
			if ($response->Customer->TokenCustomerID == null) {
			
				if ($ajax == 1)
					exit("Error , Your Card is invalid or has expired.");
			?>
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
				} else { 
					if (ajax == 1)
						exit("Error Card transaction failed " . var_dump($response));
				?>
					  <script>
						alert("Error Card transaction failed <?php var_dump($response); ?>");
						window.history.back();
					  </script>

				<?php 
					exit;
				}

	 	} //	if ($tokenId == null || $tokenId == "") 
		else {
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
					if ($ajax == 1)
						exit("Error Card token transaction failed " . var_dump($response));
				?>
				  <script>
					alert("Error Card token transaction failed <?php var_dump($response); ?>");
					window.history.back();
				</script>

				<?php
					exit;
				}

		
		} // //	if ($tokenId == null || $tokenId == "")  else


	} // if paytype == "EWAY"

	
	$weekDays = "";
	// update db
	$invoice = $userId . "-" . date("Ymd-His");
	$arrSubs = ["member_id"=>$userId, "date"=>date("Y-m-d H:i:s"),
		 			  "gross_total"=>$amount, "net_total"=>$amount, "tax_percent"=>0,
					  "txn_id"=>$invoice, "details"=>"Paid from Wallet",
					  "student_id"=>$student, "notes"=>$notes];
	if (in_array("0", $arrDays)) {
		$arrSubs["day_sun"] = 1;
		$weekDays .= "Sun ";
	} else
		$arrSubs["day_sun"] = 0;
		
	if (in_array("1", $arrDays)) {
		$arrSubs["day_mon"] = 1;
		$weekDays .= "Mon ";

	} else
		$arrSubs["day_mon"] = 0;
					  
	if (in_array("2", $arrDays)) {
		$arrSubs["day_tue"] = 1;
		$weekDays .= "Tue ";
	} else
		$arrSubs["day_tue"] = 0;
					  
	if (in_array("3", $arrDays)) {
		$arrSubs["day_wed"] = 1;
		$weekDays .= "Wed ";

	} else
		$arrSubs["day_wed"] = 0;
					  
	if (in_array("4", $arrDays)) {
		$arrSubs["day_thu"] = 1;
		$weekDays .= "Thu ";

	} else
		$arrSubs["day_thu"] = 0;
					  
	if (in_array("5", $arrDays)) {
		$arrSubs["day_fri"] = 1;
		$weekDays .= "Fri ";

	} else
		$arrSubs["day_fri"] = 0;

	if (in_array("6", $arrDays)) {
		$arrSubs["day_sat"] = 1;
		$weekDays .= "Sat ";

	} else
		$arrSubs["day_sat"] = 0;
		
	$arrSubs["pay_type"] = $payType;
	$arrSubs["flag_reminder_7am"] = $reminder7am;
	$arrSubs["flag_reminder_7pm"] = $reminder7pm;

	// get first sunday of next week
	$nextSun = strtotime("next sunday");
	$arrSubs["start_date"] = date("Y-m-d", $nextSun) . " 00:00:00";
	$arrSubs["next_date"] = date("Y-m-d", $nextSun) . " 00:00:00";
	$arrSubs["cancel_flag"] = 0;

 	 $newId = $subscriptions->update($arrSubs, 0);
	 if ($subscriptions->mError != null && $subscriptions->mError != "") { 
	 	if ($ajax == 1)
			exit("Error creating subscription:" . $subscriptions->mError);
	 ?>

 	  <script>
		alert("Error creating subscription: <?php echo($subscriptions->mError);?>");
		window.history.back();
		</script>
		<?php 
		 	exit;
	 } else {
		 
		 	// add subscription items
			foreach($subsRows as $row) {

				$arrItems = ["member_id"=>$userId, "purchase_id"=>$newId, 
				"product_id"=>$row["product_id"], "item_price"=>$row["price"],
				"item_qty"=>$row["qty"], "student_id"=>$student,
				"meal_type"=>$row["meal_type"]];

				$subscriptionItems->update($arrItems, 0);
				if ($subscriptionsItems->mError != null && $subscriptionItems->mError != "") {
					if ($ajax == 1)
						exit("Error creating subscription:" . $subscriptionItems->mError);
				?>
				 <script>
					alert("Error creating subscription: <?php echo($subscriptionItems->mError);?>");
					window.history.back();
					</script>

				<?php 
				 	exit;
				}

			}
			// add meal deal if its included
			if ($mealDealQty > 0) {
				$arrItems = ["member_id"=>$userId, "purchase_id"=>$newId, 
				"product_id"=>MEAL_DEAL_ITEM_DISPLAY_ID, "item_price"=>$mealDealRow["price"],
				"item_qty"=>$mealDealQty, "student_id"=>$student,
				"meal_type"=>"LUNCH"];

				$subscriptionItems->update($arrItems, 0);
				if ($subscriptionItems->mError != null && $subscriptionItems->mError != "") { 
					if ($ajax == 1)
						exit("Error creating subscription:"  . $subscriptionItems->mError);
				?>
				  <script>
					alert("Error creating subscription:<?php echo($subscriptionItems->mError);?>");
					window.history.back();
				  </script>

				<?php 
				 	exit;
				}

			}
			
			// save order details in session to show in confirmation page
			$_SESSION["subs_conf_id"] = $newId;
			$_SESSION["subs_conf_date"] = date("Y-m-d", $nextSun) . " 00:00:00";
			$_SESSION["subs_conf_total"] = $amount;			

			// clear subs
			$subs->deleteByExpression("user_id=" . $userId);

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
			$_SESSION["wallet_balance"] = $totalCredits - $totalDebits;


		if ($payType == "WALLET") {

			// add entry to subscription wallet payments table
			$arrData = ["subscription_id"=>$newId, "student_id"=>$student, "member_id"=>$userId,
						"date"=>date("Y-m-d H:i:s"), "amount"=>$amount];
			$spayments->update($arrData, 0);
			if ($spayments->mError != null && $spayments->mError != "") { 
				if ($ajax == 1)
					exit("Error creating wallet payment:" . $spayments->mError);
			?>

				  <script>
					alert("Error creating wallet payment: <?php echo($spayments->mError);?>");
					window.history.back();
					</script>

			<?php
				 	exit;
			}

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
			$_SESSION["wallet_balance"] = $totalCredits - $totalDebits;

		}

		// send notification mail
		if ($mrow["notify_neworder_email"] == 1) {
				$subject = "JackAndJill Subscription Confirmation";
				$content = file_get_contents($g_docRoot . "mails/new-subscription.html");
				$content = str_replace("#name#",  $mrow["fname"] . " " . $mrow["lname"], $content);
				$content = str_replace("#studentdetails#", $studentDetails , $content);

				$content = str_replace("#subsno#",  $newId, $content);
			    $content = str_replace("#subsamount#",  number_format($subsAmount,2), $content);
			    $content = str_replace("#weekdays#",  $weekDays, $content);

				$email = $mrow["emailid"];
				sendMail($g_fromEmailId, $g_fromName, $email, $name, $subject, $content);
		}

		// send sms to mobile
		if ($mrow["notify_neworder_sms"] == 1) {
				$msg = "Your new subscription details are Subs#" . $newId . " placed for $" . number_format($subsAmount,2) . ". Order for: " . $onlyStudentName .  " Weekdays:" . $weekDays;
				
				$smsURL = "https://1s2u.com/sms/sendsms/sendsms.asp?username=" . $srow["sms_api_userid"] . "&password=" . $srow["sms_api_pwd"] . "&mt=0&fl=0&sid=JACKANDJILL&msg=" . urlencode($msg) . "&mno=" . $mrow["mobile"]. "&ipcl=127.0.0.1";

				 $ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $smsURL);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 $return = curl_exec ($ch);
				 curl_close ($ch);
				 //echo($smsURL . "<br>");
				 //var_dump($return);
			}

		
	   if ($ajax == 1)
	   	exit("");
		
	   header("Location: " . $g_webRoot . "subscription-confirmation");
	   exit("");

	} //  if ($subscriptions->mError != null && $subscriptions->mError != "") {

	
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
