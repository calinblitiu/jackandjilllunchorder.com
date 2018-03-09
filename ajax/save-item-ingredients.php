<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$id = $_POST["id"];
	$content = $_POST["content"];

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$arrData = ["ingredients"=>$content];
	$products->update($arrData, $id);
	if ($products->mError != null && $products->mError != "")
		exit("Error=" . $products->mError);
	else
		exit("");
	
	
?>
