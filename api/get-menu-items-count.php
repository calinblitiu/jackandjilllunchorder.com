<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$name = $_POST["name"];
	$ftype = $_POST["food_type"];
	$recess = $_POST["recess"];
	$lunch = $_POST["lunch"];
	$global = $_POST["global"];
	
	$mealDeal = null;
	
	$rowCount = $products->getCount($name, $ftype, $recess, $lunch, $mealDeal, $global);

	$response["data"] = $rowCount;

	exit(json_encode($response));
	

?>
