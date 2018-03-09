<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - Order Confirmation";
	require_once("includes/globals.php");

	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		
	if ($_SESSION["order_conf_id"] == null) {
		header("Location:" . $g_webRoot . "orders");;
		exit;
	}


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
    
    
   
<section class="thank_upg">
	<div class="container">
    			<div class="inn_titl">
                             <h3>Order Confirmation</h3>
                      </div>
                      
                      <div class="thank_youordr">
                      			<div class="thank_u"><img src="<?php echo($g_webRoot);?>images/thank_uicon.png"> Thank you for your order</div>
                      		<div class="thank_uinfo">
                            		<p>Order Number:  <span><?php echo($_SESSION["order_conf_id"]);?></span></p>
                                    <p>Order Delivery Date:  <span>
										<?php echo(getNiceDate($_SESSION["order_conf_date"],DATE_NOTIME));?>
									</span></p>
                           				 <p>Total Amount: <span>$<?php echo(number_format($_SESSION["order_conf_total"],2));?></span></p>
                            </div>
                            <a href="<?php echo($g_webRoot);?>orders">View Your Orders</a>
							<a href="<?php echo($g_webRoot);?>products-list">Start New Order</a>

                            <div class="thank_note">
                            <p><span>NOTE:</span> Please keep your ORDER NUMBER safe and remember  for order reference.</p>
                            </div>
                      </div>
                      
    
	</div>  <!--container--> 
</section>

   
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

<?php
	$_SESSION["order_conf_id"] = null;
	$_SESSION["order_conf_date"] = null;
	$_SESSION["order_conf_total"] = null;

?>
</body>
</html>
