<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/subscriptions.php");



	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$subsId = $_POST["subscription_id"];

 	$row = $subscriptions->getRowById("ID", $subsId);
	if (!$row) {
		$response["response_code"] = "ERROR";
		$response["error"] = "Invalid subscription id";
	} else {
    	$arrData= ["cancel_flag"=>1];
		$subscriptions->update($arrData, $subsId);
	}

	exit(json_encode($response));
?>
