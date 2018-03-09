<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "dashboard";
	$pageTitle = "Jack & Jill - Dashboard";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}
		
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$orderCount = $orders->getCountForMember($userId);

	$totalRow = $credits->getTotalCreditsForMember($userId);
	$totalCredits = 0;
	if ($totalRow)
		$totalCredits = $totalRow["total"];
		
	// get total payments
	$totalDebits = 0;
	$ordersRow = $orders->getTotalPurchasesForMember($userId);
	if ($ordersRow)
		$totalDebits = $ordersRow["total"];


	// get total subscription payments
	$totalSDebits = 0;
	$subsRow = $spayments->getTotalForMember($userId);
	if ($subsRow)
		$totalSDebits = $subsRow["total"];

	$sCount = $students->getCountForAUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
     <section class="my_profilepg">
        <div class="container">
        			<div id="horizontalTab">
						<?php require_once($g_docRoot . "components/account-menu.php"); ?>
                            <div class="resp-tabs-container">
                                    
                                      <div>
                                              <div class="tab_tittle">
                                                                <h2>Dashboard</h2>                                          
                                               </div>
                                               
                                               <div class="dashbrd_items">
                                               			<ul>
                                                        		<li>
                                                                		<div class="dashbrd_img">
                                                                                <div class="dashbrd_info"><?php echo($sCount);?> Students</div>
                                                                        </div>
                                                                </li>
                                                                <li>
                                                                		<div class="dashbrd_img">
                                                                                <div class="dashbrd_info"><?php echo($orderCount);?> <br>Orders</div>
                                                                        </div>
                                                                </li>
                                                                <li>
                                                                		<div class="dashbrd_img">
                                                                                <div class="dashbrd_info">$<?php echo(number_format($totalCredits -  ($totalDebits + $totalSDebits),2)); ?><Br> Wallet</div>
                                                                        </div>
                                                                </li>                                                       
                                                        </ul>
                                               </div>
                                    </div><!--dashboard-->
                                    
                                    
                            </div>
                    </div>
         </div><!--container-->
    </section>

   
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
