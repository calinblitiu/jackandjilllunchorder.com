<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "student-listing";
	$pageTitle = "Jack & Jill - Student Listing";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");
	require_once($g_docRoot . "classes/subscriptions.php");

	define("MAXROWSPERPAGE", 20);
	define("MAXPAGELINKS", 10);

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}
		

		// check for deletion
	$delId = $_GET["del"];
	if (is_numeric($delId)) {

		$orderCount = $orders->getCountForStudent($delId);
		if ($orderCount > 0) {
		  ?>
		  <script>
		  	alert("This student has previous Orders and cannot be deleted");
			window.history.back();
		  </script>
		  <?php
		    exit;
			
		}

		$subsCount = $subscriptions->getCountForStudent($delId);
		if ($subsCount > 0) {
		  ?>
		  <script>
		  	alert("This student has subscriptions and cannot be deleted");
			window.history.back();
		  </script>
		  <?php
		    exit;
			
		}

		$subsPCount = $spayments->getCountForStudent($delId);
		if ($subsPCount > 0) {
		  ?>
		  <script>
		  	alert("This student has subscription wallet payments and cannot be deleted");
			window.history.back();
		  </script>
		  <?php
		    exit;
			
		}

		$checkRow = $students->getRowById("ID", $delId);
		if (!$checkRow || $checkRow["user_id"] != $userId) {
			exit("This is an invalid student entry");
		}
		$students->delete($delId);
		if ($students->mError != null && $students->mError != "")
			exit("err=" . $students->mError);
		$_SESSION["studentdelmessage"] = 1;
		header("Location: " . $g_webRoot . "student-listing.php");
		exit;
		
	}
		
	// get params
	$name = $_GET["xname"];
	$sort = $_GET["sort"];
	if ($name == "none")
	   $name = null;
    
    if ($name == null || $name == "" || $name == "none")
		$rowCount = $students->getCountForAUser($userId);
	else
		$rowCount = $students->getCountForAUserWithSearch($userId, $name);

	if ($sort == null || $sort == "" || $sort == "none")
		$sort = "date_desc";
        	
	// do paging logic
	$nStartPage = $_GET["p"];
	if (!$nStartPage || $nStartPage == 0)
		$nStartPage = 1;
		
	$nPages = 0;
	$nPageCount = intval($rowCount) / intval(MAXROWSPERPAGE);
	$nPageCount = intval($nPageCount);
	if ($nPageCount * intval(MAXROWSPERPAGE) < $rowCount)
		$nPageCount++;

	$sPageLinks = "";
	if ($nPageCount > 1) {
		if ($nPageCount < MAXPAGELINKS) {
		  $maxLinks= $nPageCount;
		  $startPoint = 1;
	    } else {
		  $startPoint = ((int)($nStartPage / MAXPAGELINKS) * MAXPAGELINKS)+1;
		  if ($startPoint < 1)
		  	$startPoint = 1;
		  $maxLinks = ($startPoint + MAXPAGELINKS);
		  if ($maxLinks > $nPageCount) {
		  	$maxLinks = $nPageCount;
			$nextSetFrom = null;
		  } else {
			  $nextSetFrom = $maxLinks;
		  }
		
		}
		if ($nStartPage >= MAXPAGELINKS) {
			$sPageLinks .=  "<li><a href=# onclick=\"doPaging(" . ($startPoint - MAXPAGELINKS) . ");\">" . "<< Prev " . MAXPAGELINKS . " pages</a></li>";

		}
		for($i = $startPoint; $i <= $maxLinks; $i++) {
			if ($i == $nStartPage)
				$sPageLinks = $sPageLinks . "<li class=\"active\"><a href=# onclick=\"doPaging(" . $i . ");\">" . $i . "</a></li>";
			else
				$sPageLinks = $sPageLinks . "<li><a href=# onclick=\"doPaging(" . $i . ");\">" . $i . "</a></li>";
		}
		if ($nextSetFrom != null) {
			$sPageLinks .=  "<li><a href=# onclick=\"doPaging(" . $nextSetFrom . ");\">" . "Next " . MAXPAGELINKS . " pages >></a></li>";
		}
	}

	$nStartRec = 0;
	if ($nStartPage == 0)
		$nStartRec = 0;
	else
		$nStartRec = (intval(MAXROWSPERPAGE) * ($nStartPage-1));
	
	if ($name == null || $name == "" || $name == "none")
		$rows = $students->getListForAUser($userId, $nStartRec, MAXROWSPERPAGE, $sort);
	else
		$rows = $students->getListForAUserWithSearch($userId, $name, $nStartRec, MAXROWSPERPAGE, $sort);
		

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
            <h2>Students Listing (<?php echo($rowCount);?>)</h2>
            <span><a href="<?php echo($g_webRoot);?>add-student">Add Student/Teacher name</a></span> </div>
			

            <div class="student_search">			
			<a href="#" class="add_stud_button">Add Student</a>
         
            		<form name=frmList id=frmList onsubmit="return xvalidate(this);">
						<input type=hidden name=p id=p value="<?php echo($_GET["p"]); ?>">
						<input type=hidden name=sort id=sort value="<?php echo($_GET["sort"]); ?>">


                    			<input name=xname id=xname maxlength=100 type="text" 
								placeholder="Search Student Name" value="<?php echo($name);?>">
                                <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>                    
                    </form>
            </div>
			<?php if ($_SESSION["studentdelmessage"] == 1) { 
					$_SESSION["studentdelmessage"] = null;
			?>
			   <div class="text-center"><h4>Deletion was successful</h4></div>
			<?php } ?>
          <div class="students_list">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>
						Student Name <a href=# <a href=# onclick="setSort('name_asc'); return false;"><i class="fa fa-arrow-up"></i></a>
						&nbsp;<a href=# onclick="setSort('name_desc'); return false;"><i class="fa fa-arrow-down"></i></a>
					</th>
                    <th>
					School Name
					<a href=# onclick="setSort('schoolname_asc'); return false;"><i class="fa fa-arrow-up"></i></a>
						&nbsp;<a href=# onclick="setSort('schoolname_desc'); return false;"><i class="fa fa-arrow-down"></i></a>
					</th>
                    <th>Class</th>
                    <th>Allergies</th>
                    <th>Action</th>
					<th>&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
				<?php
					foreach($rows as $row) { 
					   $schoolRow = $schools->getRowbyId("ID", $row["school_id"]);
					   $classRow = $classes->getRowById("ID", $row["class_id"]);
					   
					   $allergyIds = explode(",", $row["allergies"]);
					   $allergyNames = "";
					   foreach($allergyIds as $allergy) {
					   	   if ($allergy != "") {
						   	 $allergyRow = $allergies->getRowById("ID", $allergy);
						     if ($allergyNames != "")
							 	$allergyNames .= ",";
							 $allergyNames .= $allergyRow["name"];
						   }
					   }
					   if ($row["other_allergies"] != null && $row["other_allergies"] != "") {
					   	  if ($allergyNames != "")
						    $allergyNames .= ",";
					   	  $allergyNames .=  $row["other_allergies"];
					   }
				?>
                 <tr>
                    <td><?php echo($row["name"]);?></td>
                    <td><?php echo($schoolRow["name"]);?></td>
                    <td><?php echo($classRow["name"]);?></td>
                    <td><?php echo($allergyNames);?></td>
					<td>
						<a href="<?php echo($g_webRoot);?>add-student/<?php echo($row["ID"]);?>"><i class="fa fa-edit"></i></a> 
						<a href="#" onclick="doDel(<?php echo($row["ID"]);?>); return false;"><i class="fa fa-trash"></i></a> 

					</td>
                    <td>
					<a href="<?php echo($g_webRoot);?>orders">View Past  Orders</a>, <a href="<?php echo($g_webRoot);?>order-for-student/<?php echo($row["ID"]);?>">Order Now</a>
					</td>
                  </tr>
				 <?php
				    }
				  ?>
                 </tbody>
              </table>
            </div>
          </div>
          <div class="prod-pagination">
                      <div class="row">
                      <div class="col-md-12">
                      <nav aria-label="Page navigation">

					   <ul class="pagination">
					   		<?php echo($sPageLinks); ?>
					   </ul>
 					  </nav>
                      
                      
                      </div>
                      </div>
                      </div>
          <!--students_list--> 
          
        </div><!--students-->
                                    
    </div>
  </div>
  <!--container--> 
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
             <h4>Add Student Updated</h4>
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
             <h4>Add Student Error</h4>
			 <div class="col-sm-12 bg-danger">
			 	<b><?php echo($error); ?>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>



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



   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
<script src="<?php echo($g_webRoot);?>includes/student-listing.js"></script>

</body>
</html>
