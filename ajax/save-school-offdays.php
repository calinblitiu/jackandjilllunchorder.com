<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/offdays.php");
	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$id = $_POST["id"];
	$schoolId = $_POST["sid"];
	$reason = $_POST["reason"];
	$date = $_POST["date"];
	
	$offdays = new OffDays($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$arrData = ["school_id"=>$schoolId, "reason"=>$reason, "date"=>$date];

	$offdays->update($arrData, $id);
	if ($offdays->mError != null && $offdays->mError != "")
		exit("Error=" . $offdays->mError);
	else
		exit("");
	
	
?>
