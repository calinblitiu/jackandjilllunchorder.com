<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "offline-ordering";
	$pageTitle = "Jack & Jill - Offline Ordering";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/school-items.php");


	$userId = $_SESSION["user_id"];	
	if ($userId == null)
		$userId = 0;
		
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$selSchoolId = "0";		// this will contain the id of the school of selected student

	// get params
	$studentId = $_GET["student"];


	$validated = false;
	if ($userId > 0) {
		// check if he has any students
		$studentCount = $students->getCountForAUser($userId);
		if ($studentCount == 0) {
			$showNoStudentPopup = true;
		} else {
		
			$studentCount = $students->getCountForAUser($userId);
			$studentRows = $students->getListForAUser($userId, 0, $studentCount, "name_asc");

			$studentList = "";
			for($i = 0; $i < count($studentRows); $i++) {
				$studentRow = $studentRows[$i];

				$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
				$classRow = $classes->getRowById("ID", $studentRow["class_id"]);

				$studentRow["school_name"] = $schoolRow["name"];
				$studentRow["class_name"] = $classRow["name"];

				if ($studentId == $studentRow["ID"]) {
					$sel = " selected ";
					$validated = true;
					$xschoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
					$xclassRow = $classes->getRowById("ID", $studentRow["class_id"]);
				} else
					$sel = "";
				$studentList .= "<option value=" . $studentRow["ID"] . " " . $sel . ">" . $studentRow["name"] . ", Class " . $studentRow["class_name"] . ", " . $studentRow["school_name"];
			}
		}

		// validate student
		
			
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

.offlinebuttons ul {
  list-style: none;
  width: 100%;
}

.offlinebuttons ul li a {
  background: #EE0F88;
  display: block;
  color: #fff;
  border-radius: 3px;
  line-height: 22px;
  padding: 11px 10px;
  border-bottom: solid 4px #C60A70;
  box-shadow: 0 2px 9px 1px rgba(0,0,0,0.2);
  text-decoration:none;
  transition:all 0.4s ease;
}

.onlinebuttons ul {
  list-style: none;
  width: 100%;
}

.onlinebuttons ul li a {
  background: #5EBE00;
  display: block;
  color: #fff;
  border-radius: 3px;
  line-height: 22px;
  padding: 11px 10px;
  border-bottom: solid 4px #51A400;
  box-shadow: 0 2px 9px 1px rgba(0,0,0,0.2);
  text-decoration:none;
  transition:all 0.4s ease;
}


</style>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
     <section class="products-headbg">
    	<div class="container">
    				<div class="inner_title">
                    		<h2>Offline Order</h2>
                    </div>


					
    	 </div><!--container-->	
    </section>
    
     <section class="subscription_pg">
    	<div class="container">
    				<div class="inn_titl">
                             <h3>Offline Order</h3>
                      </div>
                      
					  	<div class="ordringinfo">
                    		<h4>Ordering For</h4>
                         	<form name="frmO" id="frmO" onsubmit="return ovalidate(this);">
                         			<div class="form-group" id="divStudent">
                                                  <select class="wide" id="student" name="student" onchange="getStudent(); return false;">
                                                    <option data-display="Select Name of Student">Select Name of Student</option>
													<?php echo($studentList); ?>

                                                  </select>
                                    </div><!--form-group-->
						 </form>
					
                    </div> <!--ordringinfo-->
      

			<?php
				if ($validated) { 
					$pdfLink =  $g_webRoot . "output/school-" . $xschoolRow["ID"]  . ".pdf";
			?>
			  <div class="col-sm-3"></div>
			  <div class="offlinebuttons col-sm-6 text-center">
				<ul>
                	<li><a href="<?php echo($pdfLink);?>" target="_blkank"><i class="fa fa-cloud-download" aria-hidden="true"></i> Download Order form for "<?php echo($xschoolRow["name"]);?>" school</a></li>
                </ul>
			   </div>
			  <div class="col-sm-3"></div>
			  <div class="clearfix"></div><Br>
			  
	  		 <div class="col-sm-3"></div>
			  <div class="onlinebuttons col-sm-6 text-center">
				<ul>
                	<li><a href="<?php echo($g_webRoot);?>products-list"><i class="fa fa-tasks" aria-hidden="true"></i> OR Place an Order Online</a></li>
                </ul>
			   </div>
			  <div class="col-sm-3"></div>
			  
			<?php } ?>


                                          
    	 </div><!--container-->
    </section>
    


  <!-- notLoggedInPopup Modal -->
<div id="notLoggedInPopup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="window.location='<?php echo($g_webroot);?>sign-in';">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="ordringinfo">
                    		<h4>You are not logged In</h4>
							<div class="clearfix"></div>
							<div class="col-sm-12 text-center">
						        <a href="<?php echo($g_webRoot);?>sign-in">Sign In</a>
								<div class="clearfix"></div><br>
								<b>OR</b>
								<div class="clearfix"></div><br>
								<a href="<?php echo($g_webRoot);?>sign-up">Sign Up as a Member</a>
							</div>
                     		
							<div class="clearfix"></div><br>
                    </div>
      </div>
      
    </div>

  </div>
</div>

 <!-- notLoggedIn Modal -->


  <!-- noStudents Modal -->
<div id="noStudentsPopup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="window.location='<?php echo($g_webroot);?>add-student';">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="ordringinfo">
                    		<h4>You have no Students</h4>
							<div class="clearfix"></div>
							<div class="col-sm-12 text-center">
						        <a href="<?php echo($g_webRoot);?>add-student">Add A Student</a>
								<div class="clearfix"></div><br>
							</div>
                     		
							<div class="clearfix"></div><br>
                    </div>
      </div>
      
    </div>

  </div>
</div>

 <!-- noStudents Modal -->

   <!-- print  Modal -->
<div id="print-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Offline Menu Printing</h4>
			 <div class="col-sm-12 ">
			 	<a href="#" id="lnkPrint" target=_blank">Click For PDF Preview</a>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>


<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
<?php 
	if ($userId == 0) 
		echo("<script> var loginPopupShow = 1; </script>");
	else
		echo("<script> var loginPopupShow = 0; </script>");
	if ($showNoStudentPopup)
		echo("<script> var showNoStudentPopup = 1; </script>");
	else
		echo("<script> var showNoStudentPopup = 0; </script>");

	if ($validated)
		echo("<script> var selStudent = " . $studentId ."; </script>");
	else
		echo("<script> var selStudent = 0; </script>");
		

?>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
   <script src="<?php echo($g_webRoot);?>includes/offline-ordering.js"></script> 
</body>
</html>
