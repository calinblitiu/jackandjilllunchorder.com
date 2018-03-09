<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");


	$response = array();
	$response["response_code"] = "OK";

	exit(json_encode($response));

?>
