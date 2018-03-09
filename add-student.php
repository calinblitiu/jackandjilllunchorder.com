<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "add-student";
	$pageTitle = "Jack & Jill - Add a Student";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/orders.php");

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);



	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}
	
	$id = $_GET["id"];

	// if this student has orders then his school and class cannot be edited
	$orderCount = $orders->getCountForStudent($id);

	// check for form submission
	if ($_POST) {
		$id = $_POST["xid"];
		$name = $_POST["xname"];
		$school = $_POST["schools"];
		$class = $_POST["classes"];
		$dob = date("Y-m-d", strtotime($_POST["dob"]));
		$allergiesString = $_POST["allergies"];
	    $allergyIds = implode("," , $allergiesString);
		$otherAllergies = $_POST["other_allergies"];
	
		$arrData = ["dob"=>$dob, "allergies"=>$allergyIds,
				"other_allergies"=>$otherAllergies, "name"=>$name];
		if ($orderCount == 0) {
			$arrData["school_id"] = $school;
			$arrData["class_id"] =$class;
		}
		
		if ($id == 0) {
			$arrData["date_added"] = date("Y-m-d H:i:s");
			$arrData["user_id"] = $userId;
		}
	    $students->update($arrData, $id);
		if ($students->mError != null && $students->mError != "") {
				$error = $students->mError;
		} else {
				$success = "Student updation was successful.<br><br>";
				
		}
	
	}

	if ($id > 0) {
	
		$row = $students->getRowById("ID", $id);
		if (!$row || $row["user_id"] != $userId) {
			exit("This is an invalid student entry");
		}
		$allergyArr = explode(",", $row["allergies"]);

	}

	$schoolList = $schools->getListForDropDown($row["school_id"], "name", "ID", "name");
	
	$allergiesList = $allergies->getEnabledList(0, 100);
	


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo($g_webRoot);?>css/bootstrap-select.css">
<link rel="stylesheet" type="text/css" href="<?php echo($g_webRoot);?>css/bootstrap-datepicker3.min.css">
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
            <h2><?php if ($row["ID"] > 0) echo("Update Student"); else echo("Add Student");?></h2>
            <span><a href="<?php echo($g_webRoot);?>student-listing">View List</a></span> </div>
          
          				<div class="addstu_form">
                                            			<form name=frm id=frm method=post onsubmit="return xvalidate(this);">
															  <input type=hidden name=xid id=xid value=<?php echo($row["ID"]);?>>
															  <input type=hidden name=classid id=classid value=<?php echo($row["class_id"]);?>>

                                                                  <div class="form-group">
                                                                    <input type="text" class="form-control" id="xname" name="xname" placeholder="Student/Staff Name" maxlength=100 value="<?php echo($row["name"]);?>">
                                                                  </div>
													  <?php if ($orderCount > 0) { ?>
														<div class="col-sm-12 text-center">
												          This student has previous Orders. School cannot be edited or changed.
																																								</div>
														<div class="clearfix"></div><br>	
													  <?php } ?>
                                                                <div class="form-group" id="schoolsdiv">
                                                                  <select class="wide" id="schools" name="schools" <?php if ($orderCount > 0) echo(" disabled");?>	>
                                                                        <option data-display="School Name">Name</option>
																		<?php echo($schoolList); ?>
                                                              </select>
                                                              </div>
                                                              
                                                              <div class="form-group" id="classesdiv">
                                                              <select class="wide" id="classes" name="classes" <?php if ($orderCount > 0) echo(" disabled");?>	>
                                                                        <option data-display="Class Name">Class</option>
                                                              </select>
                                                              </div>
                                                              
                                                              <div class="form-group datef">
                                                                    <input data-provide="datepicker" class="datepicker form-control" placeholder="DOB" id="dob" name="dob" data-date-end-date="0d" value="<?php echo($row["dob"]);?>">
                                                               </div>
                                                                <div class="form-group slct_checkboc">
                                                                    <label>Select Allergies</label>
                                                              <select id="allergies[]" name="allergies[]" class="selectpicker form-control" multiple data-done-button="true">
															   <?php
													              foreach($allergiesList as $allergy) {
																   $sel = "";
																   if (in_array($allergy["ID"], $allergyArr))
																   $sel = " selected ";
																   echo("<option value=" . $allergy["ID"] . " " . $sel . ">" . $allergy["name"] . "</option>");
																  }
															   ?>
                                                                       
                                                              </select>
                                                              </div>
    														   <div class="form-group">
                                                                    <input type="text" class="form-control" id="other_allergies" name="other_allergies" maxlength=255  placeholder="Other Allergies"
																value="<?php echo($row["other_allergies"]);?>">
                                                                  </div>

                                                              
                                                                  <button type="submit" class="btn btn-default"><?php if ($row["ID"] > 0) echo("Update Student"); else echo("Add Student");?></button>
                                                        </form>
                                          
          </div>	<!--addstu_form-->
          
          <!--students_list--> 
          
        </div>
      
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
             <h4>Student Entry Updated</h4>
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
             <h4>Student Entry Error</h4>
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
<script src="<?php echo($g_webRoot);?>js/bootstrap-select.js"></script> 
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/add-student.js"></script>

</body>
</html>
