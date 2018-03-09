<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/allergies.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;


	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$id = $_POST["item_id"];
	
	$pRow = $products->getRowById("ID", $id);

	$response["data"] = $pRow;

	exit(json_encode($response));
	

?>
