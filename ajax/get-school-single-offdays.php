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
	$schoolId = $_GET["sid"];
	$id = $_GET["id"];
	
	$offdays= new OffDays($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$row = $offdays->getRowById("ID", $id);
	
	exit(json_encode($row));
	
	
?>
