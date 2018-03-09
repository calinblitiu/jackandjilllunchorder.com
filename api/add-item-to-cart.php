<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/meal-deal.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$userId = $_POST["userid"];

	$pid = $_POST["product_id"];
	$studentId = $_POST["student_id"];
	$qty = $_POST["qty"];
	$date = $_POST["date_added"];
	$mealType = $_POST["meal_type"];
	$price = $_POST["price"];
	
	
	if ($pid != MEAL_DEAL_ITEM_DISPLAY_ID) {
		// validate product if this is not a meal deal item
		$pRow = $products->getRowById("ID", $pid);
		if (!$pRow || $pRow["ID"] != $pid) {
			$error = "Error - Invalid menu item";
			$response["response_code"] = "ERROR";
			$response["error"] = $error;
			exit(json_encode($response));
			
			
		}
	} else {
		$pRow = $mealDealRow;
	  
	}
	$date = date("Y-m-d", strtotime($date));

	// check if this entry exists -in which case increment the qty
	$checkRow = $cart->cartEntryExists($userId, $studentId, $pid, $date);
	if ($checkRow && $checkRow["user_id"] == $userId) {
	    $checkRow["qty"] = $checkRow["qty"] + $qty;
		$cart->update($checkRow, $checkRow["ID"]);

		$response["data"] = "INCREMENTED";
		exit(json_encode($response));

	}

	$arrData = ["product_id"=>$pid, "student_id"=>$studentId, "order_date"=>$date, "qty"=>$qty,
			"price"=>$pRow["price"], "date_added"=>date("Y-m-d H:i:s"), "user_id"=>$userId,
			"meal_type"=>$mealType];

	$cart->update($arrData, 0);

	if ($cart->mError != null && $cart->mError != "") {
		$error = "Error=" . $cart->mError;
		$response["response_code"] = "ERROR";
		$response["error"] = $error;
		exit(json_encode($response));

	} else {
	   $count = $cart->getCountForAUser($userId);
	
	   $response["data"] = $count;
	   exit(json_encode($response));

	}

	
?>
