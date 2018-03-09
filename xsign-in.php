<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - Sign In";

	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/settings.php");

	
	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		

	if ($_POST) {
	    var_dump($_POST);
		exit;
		$email = $_POST["email"];
		$pwd = $_POST["pwd"];

		$row = $members->authenticate($email, getPwdHash($pwd));
		if ($row && $row["emailid"] == $email) {
		
			$_SESSION["user_id"] = $row["ID"];
			$_SESSION["email"] = $row["emailid"];
			$_SESSION["name"] = $row["fname"] . " " . $row["lname"];

			header("Location: dashboard");
			exit;
		} else {
			$error = "Login failed";
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
                                              <input type="email" class="form-control" name="email" id="email" placeholder="Email id" maxlength=50>
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
                                          <div class="form-group"> 
                                            <div class="submt-btn">
                                              <button type="submit" class="btn btn-default">Submit</button>
                                            </div>
                                          </div>
                                          <div class="not_mmbr">Not a member yet! , <a href="<?php echo($g_webRoot);?>sign-up">Sign Up</a></div>
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


   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>


<script>
<?php 
	if ($error != "") 
		echo("var error_message=\"" . $error . "\";"); 
	else
		echo("var error_message=\"" . "" . "\"; "); 


?>
</script>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/sign-in.js"></script>


</body>
</html>
