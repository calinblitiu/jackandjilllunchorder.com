<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/orders.php");
	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$student = $_POST["student"];
	$date = $_POST["date"];
	$mealType = $_POST["meal_type"];

	$_SESSION["cart_student"] = $student;
	$_SESSION["cart_date"] = $date;
	$_SESSION["cart_meal_type"] = $mealType;
	
	exit("");	
	
?>
