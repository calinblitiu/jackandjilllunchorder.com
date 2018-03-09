<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "my-profile";
	$pageTitle = "Jack & Jill - My Profile";
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
		
		// check if this email belongs to someone else
		$checkRow = $members->emailExists($_POST["email"]);
		if ($checkRow && $checkRow["ID"] != $userId ) {
		  $error .= "This email id already belongs to another member!<br>";
		}
		// check if mobile already exists
		$checkRow = $members->mobileExists($_POST["mobile"]);
		if ($checkRow && $checkRow["ID"] != $userId) {
		  $error .= "This mobile already belongs to another member!<br>";
		}
		
		
		if ($error == "") {
			// generate sms otp
		    $otp = get_random_string(null, 4);

			$arrData = ["emailid"=>$_POST["email"], 
						"mobile"=>$_POST["mobile"], "fname"=>$_POST["fname"],
						"lname"=>$_POST["lname"], "image"=>$_POST["file_image"]
						];

			$members->update($arrData, $userId);
			if ($members->mError != null && $members->mError != "") {
				$error = $members->mError;
			} else {

							
				$success = "Profile updation was successful.<br><br>";
				
			}
		} // 	if ($error == "") 
		else {
		
		}

	}

	
	$row = $members->getRowbyId("ID", $userId);
	if ($row["image"] == null || $row["image"] == "")
		$image = $g_webRoot . "images/up_img.png";
	else
		$image = $g_webRoot . "profiles/files/" . $row["image"];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>
<link rel="stylesheet" href="<?php echo($g_webRoot);?>includes/jQuery-File-Upload-9.0.2/css/jquery.fileupload.css">
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
                                                		     <ul><li class="active"><a href="<?php echo($g_webRoot);?>my-profile">My Profile</a></li>
                                                                 <li><a href="<?php echo($g_webRoot);?>my-profile-security">Security</a></li>
                                                                  <li><a href="<?php echo($g_webRoot);?>my-profile-notifications">Notification</a></li>  
                                                                  </ul>                                              
                                                </div>
                                                
                                                
                                                <div class="profile_info">
                                                	<form name=frm id=frm method=POST onsubmit="return xvalidate(this);">
													 <input type=hidden name=file_image id=file_image value="<?php echo($row["image"]);?>">
                                                
                                                				<div class="box">
																<img id="imgPreview" name="img" src="<?php echo($image);?>" class="img" width="300" height="300">
																
																<span class="btn btn-info fileinput-button">
																<i class="glyphicon glyphicon-plus"></i>
																<span>Browse Image</span>
																<!-- The file input field used as target for the file upload widget -->
																<input id="flImage" type="file" name="files[]">
																</span>
																<br>
																<br>
																<!-- The global progress bar -->
																<div id="progress" class="progress" style="display:none;">
																<div class="progress-bar progress-bar-success"></div>
																</div>
																<!-- The container for the uploaded files -->
																<div id="files" class="files"></div>
                                                                         
                                                                </div>
                                                                
                                                                <div class="personal_info">
                                                                <h4>Personal Information</h4>
                                                                		
                                                                		<div class="row">
                                                                                     <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="fname" name="fname" maxlength=30 placeholder="First Name*" value="<?php echo($row["fname"]);?>">
                                                                                      </div>
                                                                                       <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="lname" name="lname" maxlength=50  placeholder="Last Name*" value="<?php echo($row["lname"]);?>">
                                                                                      </div>
                                                                                      <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="mobile" name="mobile" maxlength=20 placeholder="Mobile No.*" value="<?php echo($row["mobile"]);?>" >
                                                                                      </div>
                                                                                      <div class="form-group col-sm-6">
                                                                                          <input type="email" class="form-control" name="email" id="email" maxlength="100" placeholder="Email ID*" value="<?php echo($row["emailid"]); ?>" readonly=1>
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
	<script src="<?php echo($g_webRoot);?>includes/jquery-ui/jquery-ui.js"></script>
<script src="<?php echo($g_webRoot);?>includes/jQuery-File-Upload-9.0.2/js/jquery.iframe-transport.js"></script>
<script src="<?php echo($g_webRoot);?>includes/jQuery-File-Upload-9.0.2/js/jquery.fileupload.js"></script>	
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/my-profile.js"></script>


</body>
</html>
