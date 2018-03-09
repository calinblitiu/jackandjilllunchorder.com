<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/products.php");

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	
	// get params
	$id = $_POST["id"];
	$qty = $_POST["qty"];
	

	$arrData = ["qty"=>$qty];

	$cart->update($arrData, $id);

	if ($cart->mError != null && $cart->mError != "")
		exit("Error=" . $cart->mError);
	else {

			// update cart items in session
	   $count = $cart->getCountForAUser($userId);
	   $_SESSION["cart_count"] = $count;

	   exit("");

	}
	
	
?>
