<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");


	
	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$error = null;
	$email = $_POST["email"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$row = $members->emailExists($email);
	if ($row && $row["emailid"] == $email) {
	  	$otp = get_random_string(null, 4);

		$arrData = ["verify_code"=>$otp];

		$members->update($arrData, $row["ID"]);
		if ($members->mError != null && $members->mError != "")
			$error =  $members->mError;
		else
			$response["data"] = $otp;
	} else {
		$error = "Error - invalid email id";
	}

	if ($error != null) {
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
	}

	exit(json_encode($response));

?>
