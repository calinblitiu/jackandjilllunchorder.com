<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/nutrition.php");
	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$id = $_POST["id"];
	$productId = $_POST["pid"];
	$cropType = $_POST["crop_type"];
	$yield = $_POST["yield"];
	$percent = $_POST["percent"];
	$adjusted = $_POST["adjusted"];
	$protein = $_POST["protein"];
	$fat = $_POST["fat"];
	$carbs = $_POST["carbs"];
	$fiber = $_POST["fiber"];
	$xtime = $_POST["xtime"];
	$proteiny = $_POST["proteiny"];
	
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$arrData = ["product_id"=>$productId, "crop_type"=>$cropType, "yield"=>$yield, "percent"=>$percent,
		"adjusted"=>$adjusted, "protein"=>$protein, "fat"=>$fat, "carbs"=>$carbs, "fiber"=>$fiber,
		"xtime"=>$xtime, "proteiny"=>$proteiny];

	$nutrition->update($arrData, $id);
	if ($nutrition->mError != null && $nutrition->mError != "")
		exit("Error=" . $nutrition->mError);
	else
		exit("");
	
	
?>
