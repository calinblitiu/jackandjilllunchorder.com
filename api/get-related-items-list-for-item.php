<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$id = $_POST["item_id"];
	
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$pRow = $products->getRowById("ID", $id);
	$relatedRows = $products->getListOfType($pRow["food_type"], $id, 0, 4, "name_asc");

	for($i = 0; $i < count($relatedRows); $i++) {
		$relatedRow = $relatedRows[$i];
		$row = $products->getRowById("ID",  $relatedRow["ID"]);
		
		$relatedRow["name"] = $row["name"];
		$relatedRow["image"] = $row["image"];
		$relatedRow["price"] = $row["price"];
		
		$relatedRows[$i] = $relaedRow;
	}

	$response["data"] = $relatedRows;
	exit(json_encode($response));
	
?>
