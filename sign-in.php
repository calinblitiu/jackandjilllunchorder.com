<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - Sign In";

	require_once("includes/globals.php");
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


	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		

	if ($_POST) {
		$email = $_POST["email"];
		$pwd = $_POST["pwd"];

		$row = $members->authenticate($email, getPwdHash($pwd));
		if ($row && $row["emailid"] == $email) {
		
			// check if account is blocked
			if ($row["is_blocked"] == 1) {
				$error = "Your account has been deactivated. Kindly contact administrator to activate your account.";
				$_SESSION["signin_error"] = $error;
				header("Location:" . $g_webroot . "sign-in");
				exit;
			}
			if ($row["verify_code"] != "1") {
				$_SESSION["signin_error_verify_modal"] = 1;
				$_SESSION["signin_error_emailid"] = $row["emailid"];
				header("Location:" . $g_webroot . "sign-in");
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
			
			header("Location: dashboard");
			exit;
		} else {
			$error = "Login credentials were invalid";
		}
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
<style>
.error_color { color:red; background-color:#000000;}
</style>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
 
    <section class="sign_inbg signin">
        <div class="container">
        			<div class="sign_inform">
                    			<div class="inn_titl whtcolr">
                             <h3>Sign in</h3>
                      </div>
                    		
                    			<form method="POST" class="form-horizontal" name="frm" id="frm" onsubmit="return xvalidate(this);">
                                          <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-2" for="email"><img src="<?php echo($g_webRoot);?>images/user_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10">
                                              <input type="email" class="form-control" name="email" id="email" placeholder="Email id" maxlength=50 value="<?php echo($_SESSION["login_prefill_emailid"]);?>">
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="control-label col-sm-2 col-xs-2" for="pwd"><img src="<?php echo($g_webRoot);?>images/pass_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10"> 
                                              <input type="password" class="form-control" name="pwd" id="pwd" placeholder="Password" maxlength=20>
                                            </div>
                                          </div>
                                          <div class="form-group"> 
                                            <div class="col-sm-offset-2  col-xs-offset-2 col-sm-10 col-xs-10">
                                              <div class="checkbox">
                                                  <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                    Remember
                                                  </label>
                                            </div>
                                            </div>
                                          </div>
										  <div class="clearfix"></div><br>
										  	<?php
												if ($_SESSION["signin_error"]) {
											?>
											<div class="col-sm-12 text-center error_color" id="divVMessage">
												<?php
													echo($_SESSION["signin_error"]);
													$_SESSION["signin_error"] = null;
													?>
											</div>
											<?php } ?>
										  <div class="clearfix"></div><br>

                                          <div class="form-group"> 
                                            <div class="submt-btn">
                                              <button type="submit" class="btn btn-default">Submit</button>
                                            </div>
                                          </div>
                                          <div class="not_mmbr">
										  	Not a member yet! , 
												<a href="<?php echo($g_webRoot);?>sign-up">Sign Up</a>
											&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" id="lnkForgot">Forgot Password?</a>

										  </div>
										  <div class="clearfix"></div><br>
										                                  </form>
                    </div>
         </div><!--container-->
    </section>

  
    <!-- error Modal -->
<div id="error-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Login Error</h4>
			 <div class="col-sm-12 bg-danger">
			 	<b><?php echo($error); ?></b>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>

 <!-- forgot pwd modal -->
<div id="forgot-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
	  	 <div class="col-sm-12 text-center">
          		<h4>Forgot Password</h4><br>
				Enter your registered email id and a new password will be sent to your mailbox.<br><br>
				<form name=frmF id=frmF onsubmit="return false;">
				<div class="col-sm-3"></div>
				<div class="col-sm-6 text-center">
			        <input class="form-control" name=femail id=femail maxlength=50
						placeholder="Enter your email id here" value="">
				</div> <!--col-sm-6-->
				<div class="col-sm-3"></div>
				
				<div class="clearfix"></div><br>
				<div class="col-sm-12 text-center">
				    <img id="imgLoaderF" src="<?php echo($g_webRoot);?>images/ajax-loader-bar.gif" align=center class="img" style="display:none;">
				</div>
				
				<div class="clearfix"></div><br>


				<div class="col-sm-3"></div>
				<div class="col-sm-6 text-center">
					<button type="button" class="btn btn-success" id="btnForgot">Reset</button>
					<br><br>
				</div>
				<div class="col-sm-3"></div>

				</form>
         </div>   
		 <div class="clearfix"></div>
      </div> <!--modal-body-->
      
    </div> <!--modal-content-->

  </div> <!--modal-dialog-->
</div>

 <!-- forgot password modal -->


 <!-- verification Modal -->
<div id="verify-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
	  	 <div class="col-sm-12 text-center">
          		<h4>Please Complete your Membership Verification</h4><br>
				<?php echo($success); ?>

				<form name=frmV id=frmV onsubmit="return false;">
				   <input  type=hidden name=vemail id=vemail value="<?php echo($_SESSION["signin_error_emailid"] );?>">
				<div class="col-sm-3"></div>
				<div class="col-sm-6 text-center">
			        <input class="form-control" name=otp id=otp maxlength=10
						placeholder="Enter OTP code here" value="<?php //echo($otp);?>">
				</div> <!--col-sm-6-->
				<div class="col-sm-3"></div>
				
				<div class="clearfix"></div><br>
				<div class="col-sm-12 text-center">
				    <img id="imgLoaderV" src="<?php echo($g_webRoot);?>images/ajax-loader-bar.gif" align=center class="img" style="display:none;">
				</div>
				
				<div class="clearfix"></div><br>

				<div class="col-sm-12 text-center" id="divVMessage">
					
				</div>
				<div class="clearfix"></div><br>


				<div class="col-sm-3"></div>
				<div class="col-sm-6 text-center">
					<button type="button" class="btn btn-success" id="btnVerify">Verify</button>
					<br><br>
					<a href="#" id="lnkSendAgain">Not received code? Send another one</a>
				</div>
				<div class="col-sm-3"></div>

				</form>
         </div>   
		 <div class="clearfix"></div>
      </div> <!--modal-body-->
      
    </div> <!--modal-content-->

  </div> <!--modal-dialog-->
</div>

 <!-- verification modal -->


   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

<?php
 $_SESSION["login_prefill_emailid"]  = null;
?>

<script>
<?php 
	if ($error != "") 
		echo("var error_message=\"" . $error . "\";"); 
	else
		echo("var error_message=\"" . "" . "\"; "); 

	if ($_SESSION["signin_error_verify_modal"] == 1) {
		echo("var verify_modal = 1;");
		$_SESSION["signin_error_verify_modal"] = null;
	} else
		echo("var verify_modal = 0;");
		


?>
</script>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/sign-in.js"></script>


</body>
</html>
