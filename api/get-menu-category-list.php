<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/categories.php");

	define("MAXPAGELINKS", 1000);

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$startPage = $_POST["start_page"];
	$rowsPerPage = $_POST["rows_per_page"];

	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$rowCount = $cats->getCount();

		// do paging logic
	$nStartPage = $startPage;
	if (!$nStartPage || $nStartPage == 0)
		$nStartPage = 1;
		
	$nPages = 0;
	$nPageCount = intval($rowCount) / intval($rowsPerPage);
	$nPageCount = intval($nPageCount);
	if ($nPageCount * intval($rowsPerPage) < $rowCount)
		$nPageCount++;

	$sPageLinks = "";
	if ($nPageCount > 1) {
		if ($nPageCount < MAXPAGELINKS) {
		  $maxLinks= $nPageCount;
		  $startPoint = 1;
	    } else {
		  $startPoint = ((int)($nStartPage / MAXPAGELINKS) * MAXPAGELINKS)+1;
		  if ($startPoint < 1)
		  	$startPoint = 1;
		  $maxLinks = ($startPoint + MAXPAGELINKS);
		  if ($maxLinks > $nPageCount) {
		  	$maxLinks = $nPageCount;
			$nextSetFrom = null;
		  } else {
			  $nextSetFrom = $maxLinks;
		  }
		
		}

	}

	$nStartRec = 0;
	if ($nStartPage == 0)
		$nStartRec = 0;
	else
		$nStartRec = (intval($rowsPerPage) * ($nStartPage-1));

	$catRows = $cats->getList( $nStartRec, $rowsPerPage, "name asc");
	$response["data"] = $catRows;

	exit(json_encode($response));
	
?>
