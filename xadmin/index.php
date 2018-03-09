<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack&Jill Catering - Admin Login ";
	
	require_once("../includes/globals.php");

	if ($_SESSION["admin_id"] == "1") {
		header("Location: dashboard.php");
		exit();
	}

	// check if form was posted
	if ($_POST) {
	  $username = $_POST["userid"];
	  $pwd = $_POST["pwd"];
	  if ($username != "admin" || $pwd != "272131598I2953!") {
		 $error = "Login failed";
      } else {
		$_SESSION["admin_id"] = 1;
		header("Location: dashboard.php");
		exit();
	  }
	}

?>
<?php require_once($g_docRoot . "components/admin-header.php"); ?>
<?php require_once($g_docRoot . "components/admin-styles.php"); ?>

</head>

<body>
   <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
 			<?php 
			if ($error != null) {
			   echo("<div class=\"text-center\"><b>" . $error . "</b></div>");
		        }
		 	 ?>

                        <form role="form" name=frmLogin id=frmLogin onsubmit="return xvalidate(this);" method=post>
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Admin Username" name="userid" id="userid"
						 autofocus maxlength=20>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="pwd" id="pwd"
						type="password" value="">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!--container-->

  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>
	<script src="/includes/admin-index.js"></script>
	
</body>

</html>
