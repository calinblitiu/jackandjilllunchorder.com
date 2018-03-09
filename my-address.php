<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "my-address";
	$pageTitle = "Jack & Jill - My Address";
	require_once("includes/globals.php");

	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: sign-in");
		exit;
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
    
    
     <section class="my_profilepg">
        <div class="container">
        			<div id="horizontalTab">
						<?php require_once($g_docRoot . "components/account-menu.php"); ?>
                            <div class="resp-tabs-container">
                                    
                                     <div>
 										 <div class="tab_tittle">
                                                		<h2>My Address</h2>                                                 
                                          </div>
										 <div>
                                    <p>Suspendisse blandit velit Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravid urna gravid eget erat suscipit in malesuada odio venenatis.</p>
                                    </div>
                                     <div>
                                    <p>Suspendisse blandit velit Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravid urna gravid eget erat suscipit in malesuada odio venenatis.</p>
                                    </div>
		                            
                                    </div><!--my address-->
                                    
                            </div>
                    </div>
         </div><!--container-->
    </section>

   
   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
