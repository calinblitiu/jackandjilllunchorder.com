<?php 
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "fpdf17/fpdf.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/products.php");

	

$date = $_GET["date"];
$reportDate = $date;
if (!$date)
	exit("");

// get weekday for a date
$weekDay  = date("w", strtotime($reportDate));

	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptionItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$count = $subscriptions->getActiveRowCountForWeekday($weekDay, $reportDate);
	$rows = $subscriptions->getActiveRowsForWeekday($weekDay, $reportDate, 0, $count);

	for($i = 0; $i < count($rows); $i++) {
		$row = $rows[$i];
		$studentRow = $students->getRowById("ID", $row["student_id"]);
		$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
		$classRow = $classes->getRowById("ID", $studentRow["class_id"]);

		$row["school_name"] = $schoolRow["name"];
		$row["class_name"] = $classRow["name"];
		$row["student_name"] = $studentRow["name"];

		$rows[$i] = $row;
	}

	exit(json_encode($rows));
?>
