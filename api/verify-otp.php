<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$otp = $_POST["otp"];
	$error = null;

	if ($otp == null || $otp == "") {
		$error = "No valid code was entered";
	}
	else {

		$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
		$row = $members->verifyCode($otp);
		if ($row && $row["verify_code"] == $otp) {
			$arrData = ["verify_code"=>"1"];

			$members->update($arrData, $row["ID"]);
			if ($members->mError != null && $members->mError != "")
				$error =  $members->mError;
		} else {
			$error = "This code was wrong";
		}
	} // if (otp == null) else

	if ($error != null) {
			$response["response_code"] = "ERROR";
			$response["error"] = $error;
	}

	exit(json_encode($response));
?>
