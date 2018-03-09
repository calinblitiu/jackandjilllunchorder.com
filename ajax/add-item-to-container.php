<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/container-items.php");

		// check for valid page referer
	$rDomain = getDomain($_SERVER["HTTP_REFERER"]);
	$thisDomain = $_SERVER['SERVER_NAME'];

	if (strtolower(trim($rDomain)) != strtolower(trim($thisDomain))) {
		exit("ERROR - Cross domain posting detected");
	}


	// get params
	$containerId = $_POST["container_id"];
	$itemId = $_POST["item_id"];
	$increment = $_POST["qty"];	// if this is negative then qty decreases
	
	$contItems = new ContainerItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	// if item is in container increment it else add it
	$checkRow = $contItems->getRowForAContainerAndProduct($containerId, $itemId);
	if ($checkRow["qty"] > 0) {
		$qty = $checkRow["qty"] + $increment;
	}
	else
		$qty = $increment;
	if ($qty == 0) {
		$contItems->deleteByExpression("container_id=" . $containerId . " and product_id=".		
				$itemId);
	} else {
		$arrData = ["product_id"=>$itemId, "container_id"=>$containerId, "qty"=>$qty];
		$contItems->update($arrData, $checkRow["ID"]);
	}

	if ($contItems->mError != null && $contItems->mError != "")
		exit("Error " . $contItems->mError);
	else
		exit("");
	
	
?>
