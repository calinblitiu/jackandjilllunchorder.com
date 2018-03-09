<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/school-items.php");


	
		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$studentId = $_GET["id"];
	$mealType = $_GET["mt"];
	$cat = $_GET["cat"];
	
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDB);

	// get menu items
	$rowCount = $products->getCount(null, null, null, null, null, null);
	$rows = $products->getList(null, null, null, null, null, null, 0, $rowCount, "name_asc");

	
	// remove items which are not in selected category
	if ($cat != "0") {
		$catRow = $cats->getRowById("ID", $cat);
		$catItems = $catRow["items"];
		$arrItems = explode(",", $catItems);
		$rows2 = array();
		foreach($rows as $row) {
			if (in_array($row["ID"], $arrItems)) {
				$rows2 [] = $row;
			}
		}	
		$rows = $rows2;

	}
	
	// remove items which are in school  disabled list
	if ($disabledItems != null && $disabledItems != "") {
		$rows2 = array();
		foreach($rows as $row) {
			if (!in_array($row["ID"], $disabledItems)) {
				$rows2 [] = $row;
			}
		}	
		$rows = $rows2;
	}

	// remove items which are not for the selected meal type

		$selMealType = $mealType;
		$rows2 = array();
		foreach($rows as $row) {
			if ($selMealType == "R" && $row["flag_recess"] == 1 ||
			    $selMealType == "L" && $row["flag_lunch"] == 1 ||
				$selMealType == "RL") {
				
				$rows2 [] = $row;
			}
		}	
		$rows = $rows2;
		
	
	exit(json_encode($rows));
	
	
?>
