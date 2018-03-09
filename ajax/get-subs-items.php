<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/subs.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/meal-deal.php");


	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	$mealDealRow = $mealdeal->getRowById("ID", "1");

	$rowCount = $subs->getCountForAUser($userId);
	$rows = $subs->getGroupedListForAUser($userId, 0, $rowCount);
	// check for meal deal item
	for($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		if ($row["product_id"] == MEAL_DEAL_ITEM_DISPLAY_ID) {
		  $row["image"] = $mealDealRow["image"];
		  $row["productname"] = $mealDealRow["name"];
		  $rows[$i] = $row;
		}
	}

	exit(json_encode($rows));


	
?>
