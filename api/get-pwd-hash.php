<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$pwd = $_POST["pwd"];

	$hash = getPwdHash($pwd);
	$response["data"] = $hash;

	exit(json_encode($response));

?>
