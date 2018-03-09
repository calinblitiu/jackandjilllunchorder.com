<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/allergies.php");
	
	// get params
	$id = $_POST["id"];
	$productId = $_POST["pid"];
	$allergy = $_POST["allergy"];
	$flag = $_POST["flag"];
	
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$arrData = ["product_id"=>$productId, "name"=>$allergy, "flag"=>$flag];

	$allergies->update($arrData, $id);
	if ($allergies->mError != null && $allergies->mError != "")
		exit("Error=" . $allergies->mError);
	else
		exit("");
	
	
?>
