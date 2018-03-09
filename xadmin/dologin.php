<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "users";
	$pageTitle = "Jack & Jill Admin - List Of Users";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	if ($_SESSION["admin_id"] != "1") {
		exit("This is only for administrator");
	}


	$id = $_GET["id"];
	$row = $members->getRowById("ID", $id);
		if ($row) {
		
			// check if account is blocked
			if ($row["is_blocked"] == 1) {
				?>
				<script>
					alert("Your account has been deactivated. Kindly contact administrator to activate your account.");
					window.history.back();
				</script>
				<?php
				  exit;
				
			}
			$totalRow = $credits->getTotalCreditsForMember($row["ID"]);
			$totalCredits = 0;
			if ($totalRow)
				$totalCredits = $totalRow["total"];

			// get total payments
			$totalDebits = 0;
			$ordersRow = $orders->getTotalPurchasesForMember($row["ID"]);
			if ($ordersRow)
				$totalDebits = $ordersRow["total"];

			// get total subscription payments
			$totalSDebits = 0;
			$subsRow = $spayments->getTotalForMember($row["ID"]);
			if ($subsRow)
				$totalSDebits = $subsRow["total"];

			// check items in cart
			$itemsCount = $cart->getCountForAUser($row["ID"]);
			
			$_SESSION["user_id"] = $row["ID"];
			$_SESSION["email"] = $row["emailid"];
			$_SESSION["name"] = $row["fname"] . " " . $row["lname"];
		    $_SESSION["cart_count"] = $itemsCount;

			$_SESSION["wallet_balance"] = $totalCredits - ($totalDebits + $totalSDebits);
			
			header("Location: ../dashboard");
			exit;
		} else {
			$error = "Login failed";
			exit($error);
		}


?>
