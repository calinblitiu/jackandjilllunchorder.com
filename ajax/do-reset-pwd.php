<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "PHPMailer-master/PHPMailerAutoload.php");

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$email = $_POST["email"];
	if ($email == null || $email == "") {
		exit("No valid email  was entered");
	}
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$row = $members->emailExists($email);
	if ($row && $row["emailid"] == $email) {
		$pwd = 	$otp = get_random_string(null, 6);

		$arrData = ["pwd"=>getPwdHash($pwd)];

		$members->update($arrData, $row["ID"]);
		if ($members->mError != null && $members->mError != "")
			exit("Error=" . $members->mError);
		else {
			$subject = "New Account Password";
			$body = "Dear " . $row["fname"] . " " . $row["lname"] . ",<br><br>You have requested a new password. Your new password is <b>" . $pwd . "</b><br><br> Management<br>Jack & Jill Catering";
			
			sendMail($g_fromEmailId, $g_fromName, $email, $name, $subject, $body);

			exit("SUCCESS");
		}
	} else {
		exit("This emailid is not registered. Please try again");
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
