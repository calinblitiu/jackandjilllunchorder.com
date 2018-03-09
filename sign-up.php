<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - Sign Up";
	require_once("includes/globals.php");

	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/settings.php");


	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$userId = $_SESSION["user_id"];
	if ($userId > 0) {
		header("Location:" . $g_webRoot . "dashboard");
		exit;
	}
		
	
	// check for submission
	if ($_POST) {

		$error = "";
		$success = "";
		// check if this email already exists
		$checkRow = $members->emailExists($_POST["email"]);
		if ($checkRow && $checkRow["emailid"] == $_POST["email"]) {
		  $_SESSION["login_prefill_emailid"] = $_POST["email"];
		  
		  $error .= "This email id already exists!<br><a href='" . $g_webRoot . "sign-in'>Click here to login</a>";
		}
		// check if mobile already exists
		$checkRow = $members->mobileExists($_POST["mobile"]);
		if ($checkRow && $checkRow["mobile"] == $_POST["mobile"]) {
			 $_SESSION["login_prefill_emailid"] = $_POST["email"];
		  $error .= "This mobile already exists!<br><br><a href='" . $g_webRoot . "sign-in'>Click here to login</a>";
		}
		
		
		if ($error == "") {
			// generate sms otp
		    $otp = get_random_string(null, 4);

			$arrData = ["emailid"=>$_POST["email"], "pwd"=>getPwdHash($_POST["pwd"]),
					    "is_blocked"=>0, "signup_date"=>date("Y-m-d H:i:s"), 
						"mobile"=>"614" . $_POST["mobile"], "fname"=>$_POST["fname"],
						"lname"=>$_POST["lname"], "verify_code"=>$otp
						];

			$members->update($arrData, 0);
			if ($members->mError != null && $members->mError != "") {
				$error = $members->mError;
			} else {

			
				// send sms to mobile
				$msg = urlencode("Your Verification code for Jack and Jill account is " . $otp);
				$srow = $settings->getRowById("ID", 1);
				$smsURL = "https://1s2u.com/sms/sendsms/sendsms.asp?username=" . $srow["sms_api_userid"] . "&password=" . $srow["sms_api_pwd"] . "&mt=0&fl=0&sid=JACKANDJILL&msg=" . $msg . "&mno=" . "614" . $_POST["mobile"]. "&ipcl=127.0.0.1";

			
				 $ch = curl_init();
				 curl_setopt($ch, CURLOPT_URL, $smsURL);
				 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				 $return = curl_exec ($ch);
				 curl_close ($ch);
				 //echo($smsURL . "<br>");
				 //var_dump($return);
				
				$success = "<b>Congratulations</b>. Your signup was successful. We have sent a one time verification code to your mobile. Please enter that code below to confirm your membership.<br><br>";
				
			}
		} // 	if ($error == "") 

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
    
    
 
  
    <section class="sign_inbg">
        <div class="container">
        			<div class="sign_inform sign-up">
                    			<div class="inn_titl whtcolr">
                             <h3>Sign up</h3>
                      </div>
                    		
					  
                    			<form method=POST class="form-horizontal" name="frm" id="frm" onsubmit="return xvalidate(this);">
                                          <div class="form-group">
                                            <label class="control-label col-sm-2  col-xs-2" for="email"><img src="<?php echo($g_webRoot);?>images/user_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10">
                                              <input class="form-control" id="fname" name="fname" placeholder="First Name*" maxlength=30 value="<?php echo($_POST["fname"]);?>">
                                            </div>
                                          </div>
                                           <div class="form-group">
                                            <label class="control-label col-sm-2  col-xs-2" for="lname"><img src="<?php echo($g_webRoot);?>images/userl_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10">
                                              <input class="form-control" id="lname" name="lname" placeholder="Last Name*" maxlength=50 value="<?php echo($_POST["lname"]);?>">
                                            </div>
                                          </div>
                                           <div class="form-group">
                                            <label class="control-label col-sm-2  col-xs-2" for="mobile"><img src="<?php echo($g_webRoot);?>images/phn_icon.png"></label>
  											  <div class="col-sm-10 col-xs-10">
                                            		<div class="promobile_code">
                                                        <div class="row">
                                                                <div class="col-sm-3 col-xs-3">
                                                                        <input type="email" class="form-control" id="pprefix" disabled=true value="04">
                                                                </div>
                                                                <div class="col-sm-9 col-xs-9">
																 <input  class="form-control" id="mobile" name="mobile" placeholder="Mobile*" maxlength=20 value="<?php echo($_POST["mobile"]);?>">

                                                                </div>
                                                           </div>
                                                       </div>
                                            </div>

                                          </div>
                                           <div class="form-group">
                                            <label class="control-label col-sm-2  col-xs-2" for="email"><img src="<?php echo($g_webRoot);?>images/mail_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10">
                                              <input type="email" class="form-control" name="email" id="email" placeholder="Email*" maxlength=100 value="<?php echo($_POST["email"]);?>">
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="control-label col-sm-2  col-xs-2" for="pwd"><img src="<?php echo($g_webRoot);?>images/pass_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10"> 
                                              <input type="password" class="form-control" name="pwd" id="pwd" placeholder="Password*" maxlength=20>
                                            </div>
                                          </div>
    									  <div class="form-group">
                                            <label class="control-label col-sm-2  col-xs-2" for="pwd2"><img src="<?php echo($g_webRoot);?>images/pass_icon.png"></label>
                                            <div class="col-sm-10 col-xs-10"> 
                                              <input type="password" class="form-control" name="pwd2" id="pwd2" placeholder="Confirm Password*" maxlength=20>
                                            </div>
                                          </div>
                                        
                                          <div class="form-group"> 
                                            <div class="submt-btn">
                                              <button type="submit" class="btn btn-default">Submit</button>
                                            </div>
                                          </div>
                                          <div class="not_mmbr">If already have account! , <a href="<?php echo($g_webRoot);?>sign-in">Sign in</a></div>
                                </form>
                    </div>
         </div><!--container-->
    </section>

 

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
          		<h4>Membership Verification</h4><br>
				<?php echo($success); ?>

				<form name=frmV id=frmV onsubmit="return false;">
				   <input  type=hidden name=vemail id=vemail value="<?php echo($_POST["email"]);?>">
				<div class="col-sm-3"></div>
				<div class="col-sm-6 text-center">
			        <input class="form-control" name=otp id=otp maxlength=10
						placeholder="Enter code here" value="<?php //echo($otp);?>">
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


  <!-- error Modal -->
<div id="error-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Signup Error</h4>
			 <div class="col-sm-12 bg-danger">
			 	<b><?php echo($error); ?>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>

 
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script>
<?php 
	if ($error != "") 
		echo("var error_message=\"" . $error . "\";"); 
	else
		echo("var error_message=\"" . "" . "\"; "); 
	
	if ($success != "") 
		echo(" var success_message=\"" . $success . "\"; "); 
	else
		echo(" var success_message=\"" . "" . "\"; "); 
	

?>
</script>
<script src="<?php echo($g_webRoot);?>includes/sign-up.js"></script>

</body>
</html>
