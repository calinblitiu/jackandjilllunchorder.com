<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");

	define("MAXPAGELINKS", 1000);

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];
	$name = $_POST["name"];
	$sort = $_POST["sort"];
	$p = $_POST["start_page"];
	$rowsPerPage = $_POST["rows_per_page"];
	
	if ($sort == null || $sort == "" || $sort == "none")
		$sort = "date_desc";

	$rowCount = $students->getCountForAUserWithSearch($userId, $name);

	// do paging logic
	$nStartPage = $p;
	if (!$nStartPage || $nStartPage == 0)
		$nStartPage = 1;
		
	$nPages = 0;
	$nPageCount = intval($rowCount) / intval($rowsPerPage);
	$nPageCount = intval($nPageCount);
	if ($nPageCount * intval(rowsPerPage) < $rowCount)
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
		$nStartRec = (intval(MAXROWSPERPAGE) * ($nStartPage-1));
	
	$rows = $students->getListForAUserWithSearch($userId, $name, $nStartRec, rowsPerPage, $sort);

	for($i = 0; $i < count($rows); $i++) {
		 $row = $rows[$i];
		 $schoolRow = $schools->getRowbyId("ID", $row["school_id"]);
		 $classRow = $classes->getRowById("ID", $row["class_id"]);
					   
		 $allergyIds = explode(",", $row["allergies"]);
		 $allergyNames = "";
		 foreach($allergyIds as $allergy) {
			 if ($allergy != "") {
				 $allergyRow = $allergies->getRowbyId("ID", $allergy);
				 if ($allergyNames != "")
					 $allergyNames .= ",";
				 $allergyNames .= $allergyRow["name"];
			 }
		 }
		 if ($row["other_allergies"] != null && $row["other_allergies"] != "") {
			 if ($allergyNames != "")
				 $allergyNames .= ",";
			 $allergyNames .=  $row["other_allergies"];
		 }
		$row["class"] = $classRow["name"];
		$row["school"] = $schoolRow["name"];
		$row["allergies"] = $allergyNames;
		
		$rows[$i] = $row;
	}


	$response["data"] = $rows;

	exit(json_encode($response));

?>
