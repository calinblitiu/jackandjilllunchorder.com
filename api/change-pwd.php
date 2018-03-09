<?php

	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");

	$response = array();
	$response["response_code"] = "OK";
	$response["data"] = null;

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_POST["userid"];
	$oldPwd = $_POST["old_pwd"];
	$newPwd = $_POST["new_pwd"];

	$mrow = $members->getRowById("ID", $userId);

	$checkRow = $members->authenticate($mrow["emailid"], $oldPwd); 
	if (!$checkRow || $checkRow["ID"] != $userId ) {
		  $error = "The old password you entered is not correct!";
		  $response["response_code"] = "ERROR";
		  $response["error"] = $error;
		  exit(json_encode($response));
		  
		}
		
		if ($error == "") {
			// check if new pwd is same as old pwd
			$checkRow = $members->authenticate($mrow["emailid"], $oldPwd); 
			if ($checkRow && $checkRow["ID"] == $userId ) {
			  $error = "The new password and old password cannot be the same!";
			  $response["response_code"] = "ERROR";
		  	  $response["error"] = $error;
		  	  exit(json_encode($response));

			} else {

				$arrData = ["pwd"=>newPwd];

				$members->update($arrData, $userId);
				if ($members->mError != null && $members->mError != "") {
					$error = $members->mError;
					$response["response_code"] = "ERROR";
		  			$response["error"] = $error;
				    exit(json_encode($response));

				} else {
				}
			}
		} // 	if ($error == "") 
		else {
		
		}
		exit(json_encode($error));
		

?>
