<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/settings.php");

		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$email = $_POST["email"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$srow = $settings->getRowById("ID", 1);
	
	$row = $members->emailExists($email);
	if ($row && $row["emailid"] == $email) {
	  	$otp = get_random_string(null, 4);

		$arrData = ["verify_code"=>$otp];

		$members->update($arrData, $row["ID"]);
		if ($members->mError != null && $members->mError != "")
			exit("Error=" . $members->mError);
		else {

				// send sms to mobile
				$msg = urlencode("Your new Verification code for Jack and Jill account is " . $otp);
				$smsURL = "https://1s2u.com/sms/sendsms/sendsms.asp?username=" . $srow["sms_api_userid"] . "&password=" . $srow["sms_api_pwd"] . "&mt=0&fl=0&sid=JACKANDJILL&msg=" . $msg . "&mno=" . $row["mobile"]. "&ipcl=127.0.0.1";

				 $ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $smsURL);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 $return = curl_exec ($ch);
				 curl_close ($ch);
				 //echo($smsURL . "<br>");
				 //var_dump($return);
				

			exit($otp);
		}
	} else {
		exit("Error - invalid email id");
	}
	
?>
