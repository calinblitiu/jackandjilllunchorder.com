<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/categories.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$catCount = $cats->getCount();

	$response["data"] = $catCount;

	exit(json_encode($response));
	
?>
