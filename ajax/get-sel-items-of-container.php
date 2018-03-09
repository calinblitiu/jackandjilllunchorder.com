<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/container-items.php");

		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$containerId = $_POST["container_id"];
	
	$contItems = new ContainerItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$count = $contItems->getCountForAContainer($containerId);
	$rows = $contItems->getListForAContainer($containerId, 0, $count);
	
	exit(json_encode($rows));


	
?>
