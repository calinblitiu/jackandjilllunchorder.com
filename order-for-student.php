<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "order-for-student";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/cart.php");

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}

	// get params
	$studentId = $_GET["id"];
	
	// validate this student belongs to this member
	$row = $students->getRowById("ID", $studentId);
	if ($row["user_id"] != $userId) {
		exit("Invalid access");
	}
	$schoolRow = $schools->getRowById("ID", $row["school_id"]);
	$classRow = $classes->getRowById("ID", $row["class_id"]);

	// clear cart 
	$cart->deleteByExpression("user_id=" . $userId);
    $_SESSION["cart_count"] = "0";


	$_SESSION["cart_student"] = $studentId;
	

	header("Location: " . $g_webRoot . "products-list");

	
?>
