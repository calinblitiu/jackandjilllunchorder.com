<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "privacy";
	$pageTitle = "Jack & Jill - Privacy Policy";
	require_once("includes/globals.php");
	require_once("privacy-policy.php");
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
                             <h3>Privacy Policy</h3>
                      </div>
                      
                     <div class="terms_contt">
                     
                     <h4>Jack Jill Catering</h4>
                     
                   <p>  Jack and Jill Catering ABN 72505679195 – PRIVACY POLICY This Privacy Policy sets out our commitment to protecting the privacy of your personal information that we collect through this website http://www.jackandjill.com.au (Site) or directly from you. Please read this Privacy Policy carefully. Please contact us if you have any questions.</p>
<p>You providing us with personal information indicates that you have had sufficient opportunity to access this Privacy Policy and that you have read and accepted it. If you do not wish to provide personal information to us, then you do not have to do so, however it may affect your use of this Site or any products and services offered on it.</p>

<ol>
<li>Type of personal information collected Personal Information: The type of personal information we collect may include is set out on our website. If we receive your personal information from third parties, we will protect it as set out in this Privacy Policy.</li>
<li>Collection and use of personal information We collect and use the personal information for purposes including to contact and communicate with you, for internal record keeping and for marketing. 3. Disclosure of personal information We may disclose personal information for purposes including to provide our products and services to you, and as required by law.</li>
<li>Collection and use of personal information We collect and use the personal information for purposes including to contact and communicate with you, for internal record keeping and for marketing. 3. Disclosure of personal information We may disclose personal information for purposes including to provide our products and services to you, and as required by law.  Where we disclose your personal information to third parties for these purposes, we will request that the third party follow this Privacy Policy regarding handling of your personal information.</li>
<li>Access to and correction of personal information Access: You may request details of personal information that we hold about you, in certain circumstances set out in the Privacy Act 1988 (Cth). An administrative fee may be payable for the provision of information. We may refuse to provide you with information that we hold about you, in certain circumstances set out in the Privacy Act. Correction: If you believe that any information we hold on you is inaccurate, out of date, incomplete, irrelevant or misleading, please contact us by email. We rely in part upon customers advising us when their personal information changes. We will respond to any request within a reasonable time. We will endeavor to promptly correct any information found to be inaccurate, incomplete or out of date.</li>
<li>Complaints about breach If you believe that we have breached the Australian Privacy Principles and wish to make a complaint about that breach, please contact us on the email address below.</li>
<li>Unsubscribe: To unsubscribe from our e-mail database, or opt out of communications, please contact us at the details below.</li>
<li>Storage and Security We are committed to ensuring that the information you provide is secure.<br>
<strong>For any questions or notice, please contact us at: Jack and Jill Catering ABN: 72505679195 Email: Orders@jackandjill.com.au
Last update: 26 January 2018 This policy is provided by legalvision.com.au</strong></li>
</ol>

        </div>              
    	 </div><!--container-->
    </section>
    
     
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
