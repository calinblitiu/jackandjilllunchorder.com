<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - My Profile Notifications";
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

		$notify_neworder_email = 0;
		$notify_neworder_sms = 0;
		$notify_status_email = 0;
		$notify_status_sms = 0;
		$notify_newsletter_email = 0;
		$notify_newsletter_sms = 0;

		if ($_POST["notify_neworder_email"] == 1)
			$notify_neworder_email = 1;
			
		if ($_POST["notify_neworder_sms"] == 1)
			$notify_neworder_sms = 1;
		
		if ($_POST["notify_status_email"] == 1)
			$notify_status_email = 1;
		
		if ($_POST["notify_status_sms"] == 1)
			$notify_status_sms = 1;

		if ($_POST["notify_newsletter_email"] == 1)
			$notify_newsletter_email = 1;

		if ($_POST["notify_newsletter_sms"] == 1)
			$notify_newsletter_sms = 1;

		$arrData = ["notify_neworder_email"=>$notify_neworder_email,
				    "notify_neworder_sms"=>$notify_neworder_sms,
					"notify_status_email"=>$notify_status_email,
					"notify_status_sms"=>$notify_status_sms,
					"notify_newsletter_email"=>$notify_newsletter_email,
					"notify_newsletter_sms"=>$notify_newsletter_sms];
		
		$members->update($arrData, $userId);
		if ($members->mError != null && $members->mError != "") {
			$error = $members->mError;
		} else {
			$success = "Notifications updation was successful.<br><br>";
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
                                                                 <li><a href="<?php echo($g_webRoot);?>my-profile-security">Security</a></li>
                                                                  <li class="active"><a href="<?php echo($g_webRoot);?>my-profile-notifications">Notification</a></li>  
                                                                  </ul>       
                                                </div>
                                                
                                                
                                                <div class="notificatons_prt">
                                                <form name=frm id=frm method=POST onsubmit="return xvalidate(this);">
														<input type="hidden" name="xtemp" id="xtemp" value="0">
                                                		<div class="table-responsive">          
                                                                      <table class="table">
                                                                        <thead>
                                                                          <tr>                                                                         
                                                                            <th></th>
                                                                            <th>New Order </th>
                                                                            <th>Order Status </th>
                                                                            <th>Newsletter</th>
                                                                          </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                          <tr>
                                                                            <td>Email</td>
                                                                                <td>
                                                                                         <div class="checkbox">
                                                                                              <label>
                                                                                                <input type="checkbox" value="1" id="notify_neworder_email" name="notify_neworder_email" <?php if ($row["notify_neworder_email"] == 1) echo(" checked"); ?>>
                                                                                                <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                                              </label>
                                                                                        </div>
                                                                                </td>
                                                                                                                                                                                                                                           														   <td>
                                                                                         <div class="checkbox">
                                                                                              <label>
                                                                                                <input type="checkbox" value="1"  id="notify_status_email" name="notify_status_email" <?php if ($row["notify_status_email"] == 1) echo(" checked"); ?>>
                                                                                                <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                                              </label>
                                                                                        </div>
                                                                                </td>
                                                                                <td>
                                                                                         <div class="checkbox">
                                                                                              <label>
                                                                                                <input type="checkbox" value="1"  id="notify_newsletter_email" name="notify_newsletter_email" <?php if ($row["notify_newsletter_email"] == 1) echo(" checked"); ?>>
                                                                                                <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                                              </label>
                                                                                        </div>
                                                                                </td>

                                                                          </tr>
                                                                            <tr>
                                                                            <td>SMS</td>
  <td>
                                                                                         <div class="checkbox">
                                                                                              <label>
                                                                                                <input type="checkbox" value="1"  id="notify_neworder_sms" name="notify_neworder_sms" <?php if ($row["notify_neworder_sms"] == 1) echo(" checked"); ?>>
                                                                                                <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                                              </label>
                                                                                        </div>
                                                                                </td>
                                                                                                                                                                                                                                             													 <td>
                                                                                         <div class="checkbox">
                                                                                              <label>
                                                                                                <input type="checkbox" value="1" id="notify_status_sms" name="notify_status_sms" <?php if ($row["notify_status_sms"] == 1) echo(" checked"); ?>>
                                                                                                <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                                              </label>
                                                                                        </div>
                                                                                </td>
                                                                                <td>
                                                                                         <div class="checkbox">
                                                                                              <label>
                                                                                                <input type="checkbox" value="1" id="notify_newsletter_sms" name="notify_newsletter_sms" <?php if ($row["notify_newsletter_sms"] == 1) echo(" checked"); ?>>
                                                                                                <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                                              </label>
                                                                                        </div>
                                                                                </td>                                                                          </tr>
                                                                        </tbody>
                                                                      </table>
                                                      </div>
                                                       <div class="sav_cotinu">
                                            			<button type="submit">Save</button>
                                            </div>
                                            </form>
                                                
                                                </div><!--notificatons_prt-->
                                                
                                                
                                                
                                                
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
             <h4>Notifications Updated</h4>
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
             <h4>Notifications Error</h4>
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
<script src="<?php echo($g_webRoot);?>includes/my-profile-notifications.js"></script>


</body>
</html>
