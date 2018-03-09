<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "about-us";
	$pageTitle = "Jack & Jill - About Us";
	require_once("includes/globals.php");

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
    
    
     <section class="products-headbg">
    	<div class="container">
    				<div class="inner_title">
                    		<h2>About Us</h2>
                    </div>
    	 </div><!--container-->	
    </section>
    
     <section class="subscription_pg">
    	<div class="container">
    				<div class="inn_titl">
                             <h3>About Us</h3>
                      </div>
                      
                     <div class="aboutus_contt">
                     		<h4>What is Jack & Jill?</h4>
                            
							Jack & Jill Catering is a Australian food service that focuses on providing:<br><br>
							<ul>
							<li>Healthy, homemade delicious low-cost food options for student and teacher lunches, supporting a school's healthy eating policy</li>

							<li>We provide a service that offers convenience (online ordering available) and value for busy working parents - and it's also a healthy option</li>

							<li>Environmentally friendly, re-useable stainless steel and plastic containers (no cost) and minimal packaging. Limiting wastage and supporting the environment and nude food.</li>

							<li>Don't spend hours preparing your child's party food.<br>													We prepare fun catering options delivered right to your door!<br>
									You get to spend time on enjoying the time with your child  
							</li>		
							</ul>
							<br>
							<p>
								<b>The food is prepared in certified kitchens with certified staff and we can also staff onsite school tuckshops!</b>
							</p>
							<p>
								<b>We service local businesses and do business catering from gourmet options through to finger food.<br><br>
Email us any enquiries. We'd love to cater your next business function and party!</b>
							</p>
							</div> 
                      
    	 </div><!--container-->
    </section>
    
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
