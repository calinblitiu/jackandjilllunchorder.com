<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");


	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;


	$schoolId = $_GET["school_id"];

	$url = $_SERVER['SERVER_NAME'] . "ajax/print-school-menu.php";
	$data = array(
    	'id' => $schoolId
	);
	$ch = curl_init();  
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    
    $output=curl_exec($ch);
 
    curl_close($ch);

	$response["data"] = $output;

	exit(json_encode($response));

?>
