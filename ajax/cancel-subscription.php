<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "PHPMailer-master/PHPMailerAutoload.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptionItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");


	// get parms
	$id = $_GET["id"];
	
    $row = $subscriptions->getRowById("ID", $id);
	// validate that this subscription belongs to current user
	if ($row["member_id"] != $userId) {
		$response = ["result"=>"ERROR", "message"=>"This subscription does not belong to you"];
		exit(json_encode($response));
		
	}
	$subsAmount = $row["net_total"];

	// get student details
	$studentRow = $students->getRowById("ID", $row["student_id"]);
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
	$onlyStudentName =  $studentRow["name"] ;
	$studentDetails = $studentRow["name"] . " Class " . $classRow["name"] .", " . 
				$schoolRow["name"];

	$mrow = $members->getRowById("ID", $row["member_id"]);

    $arrData= ["cancel_flag"=>1];
	$subscriptions->update($arrData, $id);

	$weekDays = "";
	if ($row["day_sun"] == 1)
		$weekDays .= "Sun ";

	if ($row["day_mon"] == 1)
		$weekDays .= "Mon ";

	if ($row["day_tue"] == 1)
		$weekDays .= "Tue ";

	if ($row["day_wed"] == 1)
		$weekDays .= "Wed ";


	if ($row["day_thu"] == 1)
		$weekDays .= "Thu ";


	if ($row["day_fri"] == 1)
		$weekDays .= "Fri ";

	if ($row["day_sat"] == 1)
		$weekDays .= "Sat ";


		// send notification mail
			if ($mrow["notify_status_email"] == 1) {
				$subject = "JackAndJill Order Status Change";
				$content = file_get_contents($g_docRoot . "mails/subscription-cancel.html");
				$content = str_replace("#name#",  $mrow["fname"] . " " . $mrow["lname"], $content);
				$content = str_replace("#subsno#",  $id, $content);
			    $content = str_replace("#subsamount#",  number_format($subsAmount,2), $content);
			    $content = str_replace("#weekdays#",  $weekDays, $content);
				
				$content = str_replace("#studentdetails#", $studentDetails , $content);

				$content = str_replace("#printlink#",  "http://" . $_SERVER["SERVER_NAME"] . $g_webRoot . "print-invoice/" . $row["invoice"], $content);

				$email = $mrow["emailid"];
				sendMail($g_fromEmailId, $g_fromName, $email, $name, $subject, $content);
			}

			
	
	exit("");

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
