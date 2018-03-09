<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/subs.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/meal-deal.php");


	// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		exit("Error - login has expired");
	}

	// get params
	$studentId = $_POST["student"];

	// check if this student has no subscription item, in which case 
	// delete any existing subscription items for this user 
	$checkRow = $subs->subsEntryExistsForUserAndStudent($userId, $studentId);
	if (!$checkRow || $checkRow["student_id"] != $studentId) {
		$subs->deleteByExpression("user_id=" . $userId);
	}

	exit("");	
	
?>
