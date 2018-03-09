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
	$studentId = $_POST["student"];
	$qty = $_POST["qty"];
	$mealType = $_POST["mtype"];
	$price = $_POST["price"];
	
	if ($pid != MEAL_DEAL_ITEM_DISPLAY_ID) {
		// validate product if this is not a meal deal item
		$pRow = $products->getRowById("ID", $pid);
		if (!$pRow || $pRow["ID"] != $pid) {
			exit("Error - Invalid menu item");
		}
	} else {
		$pRow = $mealDealRow;
	  
	}
	$date = date("Y-m-d", strtotime($date));

	// check if this entry exists -in which case increment the qty
	$checkRow = $subs->subsEntryExistsForMealType($userId, $studentId, $pid, $mealType);
	if ($checkRow && $checkRow["user_id"] == $userId) {
	    $checkRow["qty"] = $checkRow["qty"] + $qty;
		$subs->update($checkRow, $checkRow["ID"]);
		exit("INCREMENTED");
	}

	$arrData = ["product_id"=>$pid, "student_id"=>$studentId, "qty"=>$qty,
			"price"=>$pRow["price"],"user_id"=>$userId,
			"meal_type"=>$mealType];

	$subs->update($arrData, 0);

	if ($subs->mError != null && $subs->mError != "")
		exit("Error=" . $subs->mError);
	else {
	// send updated subs item count back
	   exit($count);

	}
	
	
?>
