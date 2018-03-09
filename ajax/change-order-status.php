<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "PHPMailer-master/PHPMailerAutoload.php");
	require_once($g_docRoot . "classes/settings.php");
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
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);



	// get params
	$oid = $_POST["order_id"];
	$status = $_POST["status"];
	
	if ($_SESSION["admin_id"] != "1") {
		exit("Admin session is invalid or expired");
	}

	$srow = $settings->getRowById("ID", 1);


	// retrieve order and get member notification details
	$row = $orders->getRowById("ID", $oid);
	$mrow = $members->getRowById("ID", $row["member_id"]);


	// get student details
	$studentRow = $students->getRowById("ID", $row["student_id"]);
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
	$onlyStudentName =  $studentRow["name"] ;
	$studentDetails = $studentRow["name"] . " Class " . $classRow["name"] .", " . 
				$schoolRow["name"];


	$arrData = ["status"=>$status];
	$orders->update($arrData, $oid);
	if ($orders->mError != null && $orders->mError != "") {
		 	exit("Error updating order:" . $orders->mError);
	 } else {

	  if ($mrow) {
			// send notification mail
			if ($mrow["notify_status_email"] == 1) {
				$subject = "JackAndJill Order Status Change";
				$content = file_get_contents($g_docRoot . "mails/order-status-change.html");
				$content = str_replace("#name#",  $mrow["fname"] . " " . $mrow["lname"], $content);
				$content = str_replace("#orderno#",  $row["ID"], $content);
			    $content = str_replace("#orderamount#",  number_format($row["net_total"],2), $content);
			    $content = str_replace("#deliverydate#",  date("Y-M-d", strtotime($row["delivery_date"])), $content);
				$content = str_replace("#status#",  $status, $content);
				$content = str_replace("#studentdetails#", $studentDetails , $content);

				$content = str_replace("#printlink#",  "http://" . $_SERVER["SERVER_NAME"] . $g_webRoot . "print-invoice/" . $row["invoice"], $content);

				$email = $mrow["emailid"];
				sendMail($g_fromEmailId, $g_fromName, $email, $name, $subject, $content);
			}

			// send sms to mobile
			if ($mrow["notify_status_sms"] == 1) {
				$msg = "New Status:" . $status . " Order#" . $row["ID"] . " placed for $" . number_format($row["net_total"],2) . ". Order For:" . $onlyStudentName . ". Delivery On:" .  date("Y-M-d", strtotime($row["delivery_date"]));
				
				$smsURL = "https://1s2u.com/sms/sendsms/sendsms.asp?username=" . $srow["sms_api_userid"] . "&password=" . $srow["sms_api_pwd"] . "&mt=0&fl=0&sid=JACKANDJILL&msg=" . urlencode($msg) . "&mno=" . $mrow["mobile"]. "&ipcl=127.0.0.1";

				 $ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $smsURL);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 $return = curl_exec ($ch);
				 curl_close ($ch);
				 //echo($smsURL . "<br>");
				 //var_dump($return);
			}
			
		
    	}
	 
		 exit("");
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
