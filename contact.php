<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - Contact Us";
	require_once("includes/globals.php");

	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		


?>
<!DOCTYPE html>
<html lang="en">
<head>s
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
  	<section class="contct_pg"> 
    		<div class="container">
            			<div class="row">
                        			<div class="col-lg-8 col-md-8 col-sm-8">
                                    		<div class="contct_form">
                                            <h2>Contact Us</h2>
                                            <p>Please fill out the form below to send us your query and we’ll surely be back to you within 24 hours! </p>
                                            			<form>
                                                                  <div class="form-group">
                                                                    <input type="email" class="form-control" id="" placeholder="Full Name*">
                                                                  </div>
                                                                  <div class="form-group">
                                                                    <input type="password" class="form-control" id="" placeholder="Email Address*">
                                                                  </div>
                                                                   <div class="form-group">
                                                                    <input type="password" class="form-control" id="" placeholder="Contact Number*">
                                                                  </div>
                                                                   <div class="form-group">
                                                                    <textarea class="form-control" rows="5" id="comment" placeholder="Message*"></textarea>
                                                                  </div>
                                                                  <button type="submit" class="btn btn-default">Submit</button>
                                                        </form>
                                            </div>	
                                    </div><!--col-lg-8 col-md-8 col-sm-8-->
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                    			<div class="contct_dtlss">
                                                    <div class="contct_info">
                                                                <div class="contct_icon"><img src="<?php echo($g_webRoot);?>images/cmail_icon.png"></div>
                                                                <div class="contct_dtl"><a href="mailto:info@pannumortgage.com">info@pannumortgage.com</a></div>
                                                    </div>
                                                    
                                                    <div class="contct_info">
                                                                <div class="contct_icon"><img src="<?php echo($g_webRoot);?>images/ccall_icon.png"></div>
                                                                <div class="contct_dtl"><p><a href="tel:0477125934">(0477) 125934</a></p></div>
                                                    </div>
                                                    
                                                    <div class="contct_info">
                                                                <div class="contct_icon"><img src="<?php echo($g_webRoot);?>images/cmap_icon.png"></div>
                                                                <div class="contct_dtl"><p>Jack & Jill Catering<br>
                                                                                        Po box 899<br>
                                                                                        Wangaratta<br>
                                                                                        Victoria 3676<br>
                                                                                        </div>
                                                    </div>
                                        
                                        
                                    </div><!--contct_dtlss-->
                                    
                        </div></div>
                                    
                                    </div>
                                    
                        </div>
            
            </div>
    
    </section>
  
  
  
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
