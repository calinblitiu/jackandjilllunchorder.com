<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - My Profile Security";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	// check for submission
	if ($_POST) {
		$error = "";
		$success = "";
		
		// check password
		$checkRow = $members->authenticate($_SESSION["email"], getPwdHash($_POST["oldpwd"])); 
		if (!$checkRow || $checkRow["ID"] != $userId ) {
		  $error .= "The old password you entered is not correct!<br>";
		}
		
		if ($error == "") {
			// check if new pwd is same as old pwd
			$checkRow = $members->authenticate($_SESSION["email"], getPwdHash($_POST["pwd"])); 
			if ($checkRow && $checkRow["ID"] == $userId ) {
			  $error .= "The new password and old password cannot be the same!<br>";
			} else {

				$arrData = ["pwd"=>getPwdHash($_POST["pwd"]) 
				];

				$members->update($arrData, $userId);
				if ($members->mError != null && $members->mError != "") {
					$error = $members->mError;
				} else {


					$success = "Password change was successful.<br><br> It will be applicable the next time you log in";

				}
			}
		} // 	if ($error == "") 
		else {
		
		}

	}
	
	$row = $members->getRowbyId("ID", $userId);



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
                                                		<h2>My Profile</h2> <span></span>                                                
                                                </div>
                                                
                                                <div class="profile_links">
                                                	 <ul><li ><a href="<?php echo($g_webRoot);?>my-profile">My Profile</a></li>
                                                                 <li class="active"><a href="<?php echo($g_webRoot);?>my-profile-security">Security</a></li>
                                                                  <li ><a href="<?php echo($g_webRoot);?>my-profile-notifications">Notification</a></li>  
                                                                  </ul>       
														</div>
                                                
                                                
                                                <div class="profile_info">
                                                	<form id="frm" name="frm" method=post onsubmit="return xvalidate(this);">
                                                                
                                                                <div class="personal_info securty">
                                                                <h4>Change Password</h4>
                                                                		
                                                                		<div class="row">
                                                                                     <div class="form-group col-sm-12">
                                                                                          <input type="password" class="form-control" id="oldpwd" name="oldpwd" maxlength=20 placeholder="Old Password*">
                                                                                      </div>
                                                                                       <div class="form-group col-sm-12">
                                                                                          <input type="password" class="form-control" id="pwd" name="pwd" maxlength=20 placeholder="New Password*">
                                                                                      </div>
                                                                                      <div class="form-group col-sm-12">
                                                                                          <input type="password" class="form-control" id="pwd2" name="pwd2" maxlength=20 placeholder="Confirm New Password*">
                                                                                      </div>
                                                                           </div>
                                                                              
                                                                              <div class="sav_btn"><button type="submit">Save</button></div>
                                                                              
                                                                </div>
                                                                
                                                         </form>
                                                </div><!--profile_info-->
                                                
                                    </div><!--profile-->
                                    
                            </div>
                    </div>
         </div><!--container-->
    </section>


  <!-- success  Modal -->
<div id="success-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Profile Updated</h4>
			 <div class="col-sm-12 ">
			 	<b><?php echo($success); ?>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>


  <!-- error Modal -->
<div id="error-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Profile Error</h4>
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
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/my-profile-security.js"></script>



</body>
</html>
