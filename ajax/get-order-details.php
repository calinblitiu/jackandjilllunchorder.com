<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");


	// get parms
	$id = $_GET["id"];
	
    $row = $orders->getRowById("ID", $id);
	// validate that this order belongs to current user
	if ($row["member_id"] != $userId) {
		$response = ["result"=>"ERROR", "message"=>"This order does not belong to you"];
		exit(json_encode($response));
		
	}

	$studentRow = $students->getRowById("ID", $row["student_id"]);
	
	// get school
	$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);

	// get class
	$classRow = $classes->getRowById("ID", $studentRow["class_id"]);

	$row["student_name"] = $studentRow["name"];
	$row["school_name"] = $schoolRow["name"];
	$row["class_name"] = $classRow["name"];
	$row["nice_order_date"] = date("D, M d, Y", strtotime($row["delivery_date"]));
 	$row["meal_type_string"] = mealTypeToString($row["meal_type"]);
	
	// get order items
	$itemCount = $orderItems->getCountForOrder($id);
	$irows = $orderItems->getGroupedRowsForOrder($id, 0, $itemCount);
	for($i= 0 ; $i < count($irows); $i++) {
		$irow = $irows[$i];
		$irow["meal_type_string"] = mealTypeToString($irow["meal_type"]);
		$irows[$i] = $irow;
	}
	
	// add details to mealdeal item if it is in the list
	for($i = 0; $i < count($irows); $i++) {
	    $irow = $irows[$i];
		if ($irow["product_id"] == MEAL_DEAL_ITEM_DISPLAY_ID) {
		  $irow["productname"] = $mealDealRow["name"];
		  $irow["image"] = $mealDealRow["image"];
		  $irows[$i] = $irow;
		  
		}
	}

	$response = ["result"=>"OK", "order"=>$row, "order_items"=>$irows];

	exit(json_encode($response));
	
	
?>
