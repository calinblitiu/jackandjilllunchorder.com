<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/classes.php");
	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}



	// get params
	$id = $_GET["id"];
	$flag = $_GET["flag"];
	
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$arrData = ["flag"=>$flag];

	$classes->update($arrData, $id);
	if ($classes->mError != null && $classes->mError != "")
		exit("Error=" . $classes->mError);
	else
		exit("");
	
	
?>