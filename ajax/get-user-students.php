<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/allergies-master.php");

	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	
	// get params
	$userId = $_GET["id"];
	
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$rowCount = $students->getCountForAUser($userId);
	$rows = $students->getListForAUser($userId, 0, $rowCount, "schoolname_asc");

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
		   $row["schoolname"] = $schoolRow["name"];
		   $row["classname"] = $classRow["name"];
		   $row["allergies"] = $allergyNames;
		   $rows[$i] = $row;
				
	}
	exit(json_encode($rows));
	
	
?>
