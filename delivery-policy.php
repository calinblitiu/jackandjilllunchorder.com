<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "delivery-policy.php";
	$pageTitle = "Jack & Jill - Delivery Policy";
	require_once("includes/globals.php");
	require_once("delivery-policy.php");
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
                             <h3>Delivery Policy</h3>
                      </div>
                      
                     <div class="terms_contt">
                     		
                            <p>This policy sets out details of delivery for your online purchases. If you require clarification or have any questions please contact us via email on: <a href="mailto:orders@jackandjill.com.au">orders@jackandjill.com.au</a></p>

<h4>Where we deliver</h4>
<p>We will deliver to the locations listed on our website, but do not deliver to international locations. We require a street address for delivery – and cannot deliver to a P.O. Box. <br>
The locations we can deliver to may change from time-to-time. Please email us to find out if we deliver to your location.</p>


<h4>How and when we deliver</h4>
<p>All deliveries are made in the morning and at lunch time as we service both morning recess and lunch time meal options.</p>

<p>We will contact you via email to confirm your order upon receipt and will deliver on the date and time you selected.</p>

<h4>Cost of delivery</h4>
<p>Delivery charges are calculated on a per order basis. <br>
If you would like an estimate of your cost of delivery, put the item into your shopping bag and displayed at the bottom is your estimated delivery charge.</p>

<h4>Acceptance of delivery</h4>
<p>Deliveries will be provided on the date and time selected on your order (morning recess or lunch time delivery). Should you not notify us that the order is not needed by 7am the morning of the delivery. No refund will be applied to your online account.</p>

<h4>Delivery Problems</h4>
<p>If your delivery has not arrived, please contact us on the same day at orders@jackandjill.com.au. If you wish to return a product delivered damaged or otherwise, please refer to our refund policy. If your request is approved upon inspection of the order, a refund will be applied to your online account.</p>


                    </div>

                      
    	 </div><!--container-->
    </section>
    
     
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
