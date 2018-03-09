<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/offdays.php");

		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}


	// get params
	$studentId = $_GET["id"];
	
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$offdays = new OffDays($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$row = $students->getRowById("ID", $studentId);
	// check if this student belongs to this user
	if (!$row | $row["user_id"] != $userId) {
		exit("This student does not belong to you");
	}
	
	// start date has to be 2 days ahead of today
	$startDate = date("Y-m-d", strtotime("+2 days"));
	$schoolRow = $schools->getRowById("ID", $row["school_id"]);
	$arr = ["sun"=>$schoolRow["work_sun"], 
			"mon"=>$schoolRow["work_mon"],
			"tue"=>$schoolRow["work_tue"],
			"wed"=>$schoolRow["work_wed"],
			"thu"=>$schoolRow["work_thu"],
			"fri"=>$schoolRow["work_fri"],
			"sat"=>$schoolRow["work_sat"],
			"start_date"=>$startDate];

	// get off days if any
	$rows = $offdays->getListForASchool($row["school_id"], 0, 500);
	if ($rows)
		$arr["off_days"] = $rows;
	else
		$arr["off_days"] = null;
		
	exit(json_encode($arr));
	
	
?>
