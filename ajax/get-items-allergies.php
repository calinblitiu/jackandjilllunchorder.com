<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/allergies.php");
	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$productId = $_GET["id"];
	
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$rows = $allergies->getListForAProduct($productId, 0, 100);
	
	exit(json_encode($rows));
	
	
?>
