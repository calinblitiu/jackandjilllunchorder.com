<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/school-items.php");
	require_once($g_docRoot . "classes/products.php");

		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$schoolId = $_GET["id"];
	
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	
	$prowCount = $products->getCount();
	$prows = $products->getList(null, null, null, null, null, null, 0, $prowCount, "name_asc");

	$rows = $schoolItems->getListForASchool($schoolId, 0, 100);
	//add extra column to menu rows for those items which are disabled in school
	for($i = 0; $i < count($prows); $i++) {
		$prow = $prows[$i];
		$disabled = 0;
		foreach($rows as $row) {
			if ($row["product_id"] == $prow["ID"]) {
				$disabled = 1;
				break;
			}
		}
		$prow["disabled"] = $disabled;
		$prows[$i] = $prow;

	}
	exit(json_encode($prows));
	
	
?>
