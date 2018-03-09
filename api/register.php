<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");

	require_once($g_docRoot . "../classes/members.php");
	// require_once($g_docRoot . "classes/settings.php");

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	// $settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	
	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	// $members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	// $settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$error = "";
		$success = "";
		// check if this email already exists
		$checkRow = $members->emailExists($_POST["email"]);
		if ($checkRow && $checkRow["emailid"] == $_POST["email"]) {
		  $error .= "This email id already exists!<br>";
		}
		// check if mobile already exists
		$checkRow = $members->mobileExists($_POST["mobile"]);
		if ($checkRow && $checkRow["mobile"] == $_POST["mobile"]) {
		  $error .= "This mobile already exists!<br>";
		}
		
		
		if ($error == "") {
			// generate sms otp
		    $otp = get_random_string(null, 4);

			$arrData = ["emailid"=>$_POST["email"], "pwd"=>getPwdHash($_POST["pwd"]),
					    "is_blocked"=>0, "signup_date"=>date("Y-m-d H:i:s"), 
						"mobile"=>"614" . $_POST["mobile"], "fname"=>$_POST["fname"],
						"lname"=>$_POST["lname"], "verify_code"=>$otp
						];

			$newId = $members->update($arrData, 0);
			
			if ($members->mError != null && $members->mError != "") {
				$error = $members->mError;
			} else {

				// send sms to mobile
				// $srow = $settings->getRowById("ID", 1);
				// $smsURL = "https://1s2u.com/sms/sendsms/sendsms.asp?username=" . $srow["sms_api_userid"] . "&password=" . $srow["sms_api_pwd"] . "&mt=0&fl=0&sid=JACKANDJILLmsg=Verify Code: " . $otp . "&mno=" . $_POST["mobile"]. "&ipcl=127.0.0.1";

				 /*$ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $smsURL);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 $return = curl_exec ($ch);
				 curl_close ($ch);
				 echo($smsURL . "<br>");
				 var_dump($return);
				 exit;*/

				 $response["data"] = $newId;
				
			}
		} // 	if ($error == "") 

		else {
		
			$response["response_code"] = "OK";
			$response["error"] = $error;
		} // if (error == "") else

		exit(json_encode($response));
?>
