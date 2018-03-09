<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "termsfeed-return-refund-policy.php";
	$pageTitle = "Jack & Jill - Returns and Refunds Policy";
	require_once("includes/globals.php");
	require_once("termsfeed-return-refund-policy.php");
	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		


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
    
    
  
     <section class="subscription_pg">
    	<div class="container">
    				<div class="inn_titl">
                             <h3>Returns and Refunds Policy</h3>
                      </div>
                      
                     <div class="terms_contt">
                     		<p>Thank you for shopping at Jack & Jill.<br>
If you are not entirely satisfied with your purchase, we're here to help.<br>
Our products can be returned within 3 days of the original purchase of the product.</p>

<p>Before you return a product, please make sure that:</p>

<ul>
<li>The product was purchased in the last 3 days</li>
<li>The product is in its original packaging</li>
</ul>

<p>Send the product with its original packing to:</p>

<p>Staff will collect the order on the day and evaluate the contents. If the order is not returned for inspection, no exchange of order will apply. Should the product refund be approved, a credit to your online account will be applied.</p>

<h4>Shipping charges</h4>
<p>Shipping charges incurred in connection with the return of a product are refundable.</p>

<h4>Damaged items</h4>
<p>If you received a damaged product, please notify us immediately for assistance.</p>

<h4>Sale items</h4>
<p>Sale items can be refunded.</p>

<h4>Contact us</h4>
<p>If you have any questions about our Returns and Refunds Policy, please contact us:</p>


<ul>
<li>By email: <a href="mailto:orders@jackandjill.com.au">orders@jackandjill.com.au</a></li>
<li>By visiting this page on our website: <a href="https://jackandjill.com.au/contact">jackandjill.com.au/contact</a></li>
</ul>


                    </div>

                      
    	 </div><!--container-->
    </section>
    
     
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
