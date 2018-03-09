<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	define("MAXPAGELINKS", 1000);

	$userId = $_POST["userid"];
	$startPage = $_POST["start_page"];
	$rowsPerPage = $_POST["rows_per_page"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

		
	$rowCount = $orders->getCountForMember($userId);
        	
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

	$rows = $orders->getRowsForMember($userId, $nStartRec, $rowsPerPage);

	for($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		
		$studentRow = $students->getRowById("ID", $row["student_id"]);
		$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
		$classRow = $classes->getRowById("ID", $studentRow["class_id"]);

		$row["student"] = $studentRow["name"];
		$row["school"] = $schoolRow["school"];
		$row["class"] = $classRow["name"];

		$rows[$i] = $row;
	}

	$response["data"] = $rows;
	exit(json_encode($response));

?>
