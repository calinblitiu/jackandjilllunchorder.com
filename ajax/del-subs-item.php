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

	// get params
	$pid = $_POST["pid"];
	
	$subs->deleteByExpression("user_id=" . $userId . " and product_id=". $pid);

	if ($subs->mError != null && $subs->mError != "")
		exit("Error=" . $subs->mError);
	else {
	// send updated subs item count back
	   exit("");

	}
	
	
?>
