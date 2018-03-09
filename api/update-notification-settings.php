<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$userId = $_POST["userid"];

	$notify_neworder_email = $_POST["notify_neworder_email"];
	$notify_neworder_sms = $_POST["notify_neworder_sms"];
	$notify_status_email = $_POST["notify_status_email"];
	$notify_status_sms = $_POST["notify_status_sms"];
	$notify_newsletter_email = $_POST["notify_newsletter_email"];
	$notify_newsletter_sms = $_POST["notify_newsletter_sms"];
	
	$arrData = ["notify_neworder_email"=>$notify_neworder_email,
				    "notify_neworder_sms"=>$notify_neworder_sms,
					"notify_status_email"=>$notify_status_email,
					"notify_status_sms"=>$notify_status_sms,
					"notify_newsletter_email"=>$notify_newsletter_email,
					"notify_newsletter_sms"=>$notify_newsletter_sms];
		
	$members->update($arrData, $userId);
	if ($members->mError != null && $members->mError != "") {
		$error = $members->mError;
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
	} else {
	}

	exit(json_encode($response));
	


?>
