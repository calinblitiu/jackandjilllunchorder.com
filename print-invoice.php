<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "print-invoice";
	$pageTitle = "Jack & Jill - Orders";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/orders.php");
	

	$invoice = $_GET["invoice"];
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$row = $orders->getOrderByInvoice($invoice);
	if (!$row)
		exit("Invalid invoice");

	// do  a POST to generate the PDF 
		$url = "http://" . $_SERVER["SERVER_NAME"] . $g_webRoot . "ajax/print-order.php";
		$fields = array(
				'id' => urlencode($row["ID"])
				);

		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		$result = curl_exec($ch);
		curl_close($ch);	

	// redirect to the pdf url
	header("Location:" . $g_webRoot . "output/" . $invoice . ".pdf");
	exit;

		

?>

