<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/meal-deal.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	$response["data"] = $mealDealRow;

	exit(json_encode($response));
	

?>
