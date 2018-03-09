<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/nutrition.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$id = $_POST["item_id"];
	
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$nRows = $nutrition->getListForAProduct($id, 0, 100);

	$response["data"] = $nRows;
	exit(json_encode($response));
	
?>
