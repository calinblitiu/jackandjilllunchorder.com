<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - View Your Cart";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/subs.php");

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
	if ($userId == 0) {
		header("Location: " . $g_webRoot . "products-list");
		exit;
	}

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	// get cart items
	$rowCount = $cart->getCountForAUser($userId);
	$rows = $cart->getGroupedListForAUser($userId, 0, $rowCount);

	// get details of order
	$checkCart = $cart->getListForAUser($userId, 0, 1, "date_desc");
	if ($checkCart && $checkCart[0]["user_id"] == $userId) {
			$studentRow = $students->getRowById("ID" , $checkCart[0]["student_id"]);

			$schoolRow = $schools->getRowbyId("ID", $studentRow["school_id"]);
			$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		
			$orderMessage = "Order for <b>" . $studentRow["name"] . "</b> Class: " . 
					$classRow["name"] . " of "  . $schoolRow["name"];

	}

	// clear any items in subs table
	$subs->deleteByExpression("user_id=" . $userId);
	
	foreach($rows as $row) {
		$arrData = ["product_id"=>$row["product_id"], "student_id"=>$row["student_id"],
		"qty"=>$row["qty"], "price"=>$row["price"],"user_id"=>$row["user_id"],
			"meal_type"=>$row["meal_type"]];

		$subs->update($arrData, 0);

	}

	// clear cart	
	$cart->deleteByExpression("user_id=" . $userId);
    $_SESSION["cart_count"] = "0";

	header("Location:" . $g_webRoot . "subscription-plan");
	exit;
?>
