<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/allergies.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$id = $_POST["item_id"];
	
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$aRows = $allergies->getListForAProduct($id, 0, 100);

	$response["data"] = $aRows;
	exit(json_encode($response));
	
?>
