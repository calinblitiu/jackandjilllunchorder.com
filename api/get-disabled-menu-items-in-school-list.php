<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/school-items.php");



	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$schoolId = $_POST["school_id"];
	$startPage = $_POST["start_page"];
	$rowsPerPage = $_POST["rows_per_page"];

	$rowCount = $schoolItems->getCountForASchool($schoolId);

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

	$rows = $schoolItems->getListForASchool($schoolId, $nStartRec, $rowsPerPage);
	for($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		
		$productRow = $product->getRowById("ID", $row["product_id"]);
		$row["item"] = $productRow["name"];
		$row["item_image"] = $productRow["image"];
		$row["item_price"] = $productRow["item_price"];

		$rows[$i] = $row;
	}


	$response["data"] = $rows;
	exit(json_encode($response));
	
	

?>
