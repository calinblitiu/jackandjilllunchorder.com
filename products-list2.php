<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "products-list";
	$pageTitle = "Jack & Jill - Products List";
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

	if ($userId == 0)
	   $popupLink = "notLoggedInPopup";
	else
	    $popupLink = "ordringslct_radiopopup";
		
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

	// check if current selection should be cleared
	if ($_GET["clear"] == 1) {
		$_SESSION["cart_student"] = null;
		$_SESSION["cart_date"]	= null;
		header("Location:" . $g_webRoot . "products-list");
		exit;
	}

	// update meal type if reqd
	if ($_GET["mt"] != null && $_GET["mt"] != "") {
		if ($_GET["mt"] == "R")
			$_SESSION["cart_meal_type"] = "R";
		else if ($_GET["mt"] == "L")
			$_SESSION["cart_meal_type"] = "L";
		else if ($_GET["mt"] == "RL")
			$_SESSION["cart_meal_type"] = "RL";

	}

	if ($userId > 0) {
		// check if he has any students
		$studentCount = $students->getCountForAUser($userId);
		if ($studentCount == 0) {
			$popupLink = "noStudentsPopup";
		}

		// check if he is already in the process of ordering for a student
		$checkCart = $cart->getListForAUser($userId, 0, 1, "date_desc");
		if ($checkCart && $checkCart[0]["user_id"] == $userId) {
			$studentRow = $students->getRowById("ID" , $checkCart[0]["student_id"]);
			$cartStudent = $studentRow["ID"];
			
			$schoolRow = $schools->getRowbyId("ID", $studentRow["school_id"]);
			$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		
			// get disabled items in school
				$dCount = $schoolItems->getCountForASchool($schoolRow["ID"]);
				$dRows = $schoolItems->getListForASchool($schoolRow["ID"], 0, $dCount);
				$disabledItems = array();
				foreach($dRows as $dRow) {
					$disabledItems [] = $dRow["product_id"];
				}
		
			$orderMessage = "Ordering for <b>" . $studentRow["name"] . "</b> Class: " . 
					$classRow["name"] . " of "  . $schoolRow["name"];

			if ($_SESSION["cart_meal_type"] == null || $_SESSION["cart_meal_type"] == "")
				$_SESSION["cart_meal_type"] = $checkCart[0]["meal_type"];

			$mealTypeMessage = "Meal Type:<b>" . mealTypeToString($_SESSION["cart_meal_type"]) . 
					"</b>&nbsp;<a href=# onclick=\"doMealType(); return false;\">Change</a>";

			$cartDate = $checkCart[0]["order_date"];

		} else {
			// if student has already been selected then do not ask for student again
			if ($_SESSION["cart_student"] != null && $_SESSION["cart_date"] != null &&
			    $_SESSION["cart_meal_type"] != null) {

				$studentRow = $students->getRowById("ID", $_SESSION["cart_student"]);
				$schoolRow = $schools->getRowbyId("ID", $studentRow["school_id"]);
			    $classRow = $classes->getRowById("ID", $studentRow["class_id"]);
				
				$selSchoolId = $studentRow["school_id"];
				$cartStudent = $studentRow["ID"];


				// get disabled items in school
				$dCount = $schoolItems->getCountForASchool($selSchoolId);
				$dRows = $schoolItems->getListForASchool($selSchoolId, 0, $dCount);
				$disabledItems = array();
				foreach($dRows as $dRow) {
					$disabledItems [] = $dRow["product_id"];
				}

			    $orderMessage = "Ordering for <b>" . $studentRow["name"] . "</b> Class: " . 
					$classRow["name"] . " of "  . $schoolRow["name"] . "&nbsp;<a href=\"" . $g_webRoot . "products-list/clear\">Change Student</a>";

				// pre-select mealtype popup
				$mealType = $_SESSION["cart_meal_type"];
				$popupLink = "";
					$cartDate = $_SESSION["cart_date"];

			} else 
				$showStudentPopup = true;
		}
	}
	
	// get list of students and make dropdown
	if ($userId > 0) {
		$studentCount = $students->getCountForAUser($userId);
		$studentRows = $students->getListForAUser($userId, 0, $studentCount, "name_asc");
	
		$studentList = "";
		for($i = 0; $i < count($studentRows); $i++) {
			$studentRow = $studentRows[$i];
			
			$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);
			$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		
			$studentRow["school_name"] = $schoolRow["name"];
			$studentRow["class_name"] = $classRow["name"];

			// if an order is already in process , auto select this student in dropdown
			if ($checkCart && $checkCart[0]["student_id"] == $studentRow["ID"])
			   $sel = " selected ";
			else
			   $sel = "";
			$studentList .= "<option value=" . $studentRow["ID"] . " " . $sel . ">" . $studentRow["name"] . ", Class " . $studentRow["class_name"] . ", " . $studentRow["school_name"];
			
		}
		if (count($studentRows) == 0)
			$showNoStudentPopup = 1;
			
	}
	
	// get params
	$name = $_GET["xname"];
	if ($name == "none")
	 $name = "";
	$name = str_replace("-", " ", $name);
	$sort = $_GET["sort"];
	if ($sort == null || $sort == "" || $sort == "none")
		$sort = "name_asc";
	//get categories
	$catCount = $cats->getCount();
	$catRows = $cats->getList( 0, $catCount, "name asc");

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	// get menu items
	$rowCount = $products->getCount($name, null, null, null, null, null);
	$rows = $products->getList($name, null, null, null, null, null, 0, $rowCount, $sort);

	// remove items which are in school  disabled list
	if ($disabledItems != null && $disabledItems != "") {
		$rows2 = array();
		foreach($rows as $row) {
			if (!in_array($row["ID"], $disabledItems)) {
				$rows2 [] = $row;
			}
		}	
		$rows = $rows2;
	}

	// remove items which are not for the selected meal type
	if ($_SESSION["cart_meal_type"] != null && $_SESSION["cart_meal_type"] != "") {

		$selMealType = $_SESSION["cart_meal_type"];
		$rows2 = array();
		foreach($rows as $row) {
			if ($selMealType == "R" && $row["flag_recess"] == 1 ||
			    $selMealType == "L" && $row["flag_lunch"] == 1 ||
				$selMealType == "RL") {
				
				$rows2 [] = $row;
			}
		}	
		$rows = $rows2;
		
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<link href="<?php echo($g_webRoot);?>css/index.css" rel="stylesheet">

<?php require_once($g_docRoot . "components/styles.php"); ?>
<link href="<?php echo($g_webRoot);?>css/bootstrap-datepicker3.min.css" rel="stylesheet">

<style>
  .dropdown_font { font-size: 1.0em !important;}
  .pro_img img {min-height:155px; max-height:155px;}
  .nice-select .list { height:180px;}
  .pro_name a{  font-size: 16px !important;}
</style>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    

    <section class="products-headbg products_bg">
    	<div class="container">
    				<div class="inner_title">
                    		<h2>Shop</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
                    </div>
    	 </div><!--container-->
    </section>
    
     <section class="products-list">
    	<div class="container">
    				<div class="show-result">
                            <div class="row">
                            			<div class="col-lg-3 col-md-3 col-sm-6">
                                        			<div class="inn_titl">
                                                    			<h3>Shop</h3>
                                                                <!--<h5>Showing 1-12 of 41 results</h5>-->
                                                    </div>
                                        </div>
                                         <form name=frmMenu id=frmMenu onsubmit="return xvalidate(this);">

                                        <div class="col-lg-7 col-md-6 col-sm-6">
                                        			<div class="student_search pro_listsrch">
                                                                                                                                   						<input type="text" placeholder="search" id="xname" name="xname" maxlength=50 value="<?php echo($name);?>">
                                                                        <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>                    
                                                    </div>
                                        </div>
                                        
                                        <div class="col-lg-2 col-md-3 col-sm-12">
                                        			<div class="deflt_sorting">
                                                    		  <select class="wide dropdown_font" id="sort" name="sort" onchange="doSort();">
                                                                        <option data-display="Default Sorting">Options</option>
                                                                        <option value="name_asc" <?php if ($sort =="name_asc") echo(" selected"); ?>>Name (A-Z)</option>
																		 <option value="name_desc" <?php if ($sort =="name_desc") echo(" selected"); ?>>Name (Z-A)</option>
																		 <option value="price_asc" <?php if ($sort =="price_asc") echo(" selected"); ?>>Price (Low - High)</option>
																		 <option value="price_desc" <?php if ($sort =="price_desc") echo(" selected"); ?>>Price (High - Low)</option>


                                                              </select>
                                                    </div>
                                        </div>

										</form>
                            </div>
                      </div>  <!--show-result-->
                      
					  <?php if ($orderMessage != null) { ?>
						  <div class="col-sm-12 text-center">
						  	<br>
						    <?php echo($orderMessage); ?>
						  </div>
						  <div class="clearfix"></div><Br>
					  <?php } ?>
	  				  <?php if ($mealTypeMessage != null) { ?>
						  <div class="col-sm-12 text-center">
						    <?php echo($mealTypeMessage); ?>
						  </div>
						  <div class="clearfix"></div><Br>
					  <?php } ?>
					  
                      <div class="pro_list">
                      
                      				<ul class="simplefilter">

                                        <li class="active" data-filter="all">All</li>
										<?php
										 if ($name == "") { 
									      foreach($catRows as $catRow) { 

										?>
                                        <li data-filter="<?php echo($catRow["ID"]);?>"><?php echo($catRow["name"]);?></li>
										<?php 
										   }
										 } ?>
									   <?php
									     if ($mealDealRow) { ?>
										   <li data-filter="<?php echo(MEAL_DEAL_ITEM_DISPLAY_ID);?>"><?php echo($mealDealRow["name"]);?></li>
										<?php } ?>   


                                    </ul>
                                    
                                    
                      <div class="filtr-container">
                      				<div class="row">
                                    <?php
									   foreach($rows as $row) { 
									      $catIdRows = $cats->getCatsForItem($row["ID"], 0, 1000);
										  $catIds = "";
										  foreach($catIdRows as $catIdRow) {
										    if ($catIds != "")
												$catIds .= ", ";
											$catIds .= $catIdRow["ID"];
										  }

										  // do not show items which are not categorised
										  if ($catIds == "")
										  	continue;

										  $link = $g_webRoot . "product-details/" . $row["ID"] . "/" . $_SESSION["cart_meal_type"];
									   ?>

                                    			<div class="col-lg-3 col-md-3 col-sm-4 filtr-item" data-category="<?php echo($catIds);?>" data-sort="Busy streets">
                                                    <div class="pro_info"> <div class="pro_img"><a href="<?php echo($link);?>"><img src="<?php echo($g_webRoot);?>items/files/<?php echo($row["image"]);?>"></a>
                                                                    <div class="pro_dtl">
                                                                            <div class="pro_name"><a href="<?php echo($link);?>"><?php echo($row["name"]);?></a></div>
                                                                            <div class="pro_prize">
																			<h5>$ <?php echo(number_format($row["price"],2)); ?></h5></div>
                                                                    </div></div>
													 <div class="pro_addordrbtn">
													 	<button type="button" onclick="addToCart(<?php echo($row["ID"]);?>, '<?php echo($_SESSION["cart_meal_type"]);?>','<?php echo($cartDate);?>', '<?php echo($row["price"]);?>', '<?php echo($cartStudent);?>'); return false;">Add to Order</button>
													</div>

                                                    </div><!--pro_info-->
                                                </div><!--col-lg-3 col-md-3 col-sm-4-->
												<div class="clearfix visible-xs"></div>
                                                
                                       <?php } ?>
									   
									   <?php
									      if ($mealDealRow) {
										    $link = $g_webRoot . "meal-deal/" . $row["ID"] . "/" . $_SESSION["cart_meal_type"];

										  ?>

												<div class="col-lg-3 col-md-3 col-sm-4 filtr-item" data-category="<?php echo(MEAL_DEAL_ITEM_DISPLAY_ID);?>" data-sort="Busy streets">
                                                    <div class="pro_info"> <div class="pro_img"><a href="<?php echo($link);?>" ><img src="<?php echo($g_webRoot);?>items/files/<?php echo($mealDealRow["image"]);?>"></a>
                                                                    <div class="pro_dtl">
                                                                            <div class="pro_name"><a href="<?php echo($link);?>"><?php echo($mealDealRow["name"]);?></a></div>
                                                                            <div class="pro_prize">
																			<h5>$ <?php echo(number_format($mealDealRow["price"],2)); ?></h5></div>
                                                                    </div></div>
													<div class="pro_addordrbtn">
													 	<button type="button"  onclick="addToCart(<?php echo($row["ID"]);?>, '<?php echo($_SESSION["cart_meal_type"]);?>','<?php echo($cartDate);?>', '<?php echo($row["price"]);?>', '<?php echo($cartStudent);?>'); return false;">Add to Order</button>
													</div>

																	
                                                    </div><!--pro_info-->
                                                </div><!--col-lg-3 col-md-3 col-sm-4-->
												<div class="clearfix visible-xs"></div>

										<?php } ?>
                                    </div><!--row-->
                                    </div><!--filtr-container-->
                                    
                      </div><!--pro_list-->
                      
                      
                      <div class="prod-pagination">
                      <div class="row">
                      <div class="col-md-12">
                      <nav aria-label="Page navigation">
  <!--<ul class="pagination">
    <li>
      <a href="#" aria-label="Previous">
        <span aria-hidden="true">Prev</span>
      </a>
    </li>
    <li class="active"><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li>
      <a href="#" aria-label="Next">
        <span aria-hidden="true">Next</span>
      </a>
    </li>
  </ul>-->
</nav>
                      
                      
                      </div>
                      </div>
                      </div>
                      
                      
                      
    	 </div><!--container-->
    </section>
    
 <!-- orderingslctradio Modal -->
<div id="ordringslct_radiopopup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="ordringinfo">
                    		<h4>Ordering For</h4>
                     
                         			<div class="s_myradio"> 
                                           <form id="frmPopup" name="frmPopup" action="<?php echo($g_webRoot);?>product-details" onsubmit="return pvalidate(this);">
										   	<input type=hidden id=pid name=pid value="0">
                                                                    
                                                                        <ul>
                                                              <li>
                                                                <input type="radio" id="f-option" name="selector" value="R" <?php if ($mealType == "R") echo(" checked"); ?>>
                                                                <label for="f-option">Recess</label>
                                                                
                                                                <div class="check"></div>
                                                              </li>
                                                              
                                                              <li>
                                                                <input type="radio" id="t-option" name="selector" value="L" <?php if ($mealType == "L") echo(" checked"); ?>>
                                                                <label for="t-option">Lunch</label>
                                                                
                                                                <div class="check"><div class="inside"></div></div>
                                                              </li>
                                                              
                                                              <li>
                                                                <input type="radio" id="rl-option" name="selector" value="RL" <?php if ($mealType == "RL") echo(" checked"); ?>>
                                                                <label for="rl-option">Both (Recess+Lunch)</label> 
                                                                
                                                                <div class="check"><div class="inside"></div></div>
                                                              </li>
                                                              
                                                                    </ul>
                                                         <div class="ordr-btn">
                         							<button type="submit" >Update</button>                         
                         </div>           
                                                                   
                                         </form>
       										 </div>                   
                    </div>
      </div>
      
    </div>

  </div>
</div>

 <!-- orderingslctradio Modal -->

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



  <!-- student Modal -->
<div id="student_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="window.location.reload();">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="ordringinfo">
                    		<h4>Ordering For</h4>
                         	<form name="frmO" id="frmO" onsubmit="return ovalidate(this);">
                         			<div class="form-group" id="divStudent">
                                                  <select class="wide" id="student" name="student" onchange="getSchoolDays(); return false;">
                                                    <option data-display="Select Name of Student">Select Name of Student</option>
													<?php echo($studentList); ?>

                                                  </select>
                                    </div><!--form-group-->
                                            
                                     <div class="form-group datef" id="divDate">
                                                                    <input data-provider="datepicker" class="datepicker1 form-control" placeholder="Select  Date of Delivery" id="date" name="date"
													value ="<?php echo($checkCart[0]["order_date"]);?>">
                                     </div>
                                                                  
                         			<div class="form-group" id="divMT">
										<select class="wide" id="meal_type" name="meal_type">
											 <option data-display="Select Meal Type">Select Meal Type</option>
											<option value="R">Recess</option>
											<option value="L">Lunch</option>
											<option value="RL">Recess+Lunch</option>

									   </select>
									</div>
                         			
									<div class="clearfix"></div><br>
                         
                         <div class="ordr-btn">
                         		<button type="submit">Select</button>                         
                         </div>
						 </form>
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>
</div>



  <!-- success  Modal -->
<div id="success-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="back();">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Cart Updated</h4>
			 <div class="col-sm-12 ">
			 	<b>Item successfully added to Cart</b>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>

 <!-- increment  Modal -->
<div id="increment-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="back();">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Cart Updated</h4>
			 <div class="col-sm-12 ">
			 	<b>Item qty has been increased in Cart</b>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>


<?php
  echo("<script> var mealDealItemDisplayId = " . MEAL_DEAL_ITEM_DISPLAY_ID . "; </script>");
 ?>
 	
<?php 
   echo("<script> var webRoot = \"" . $g_webRoot . "\"; </script>");
  ?>
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
<script>
$(document).ready(function () {
$('#horizontalTab').easyResponsiveTabs({
type: 'default', //Types: default, vertical, accordion           
width: 'auto', //auto or any width like 600px
fit: true,   // 100% fit in a container
closed: 'accordion', // Start closed if in accordion view
activate: function(event) { // Callback function if tab is switched
var $tab = $(this);
var $info = $('#tabInfo');
var $name = $('span', $info);
$name.text($tab.text());
$info.show();
}
});
});

</script> 

<script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script> 
<script src="<?php echo($g_webRoot);?>js/custom-file-input.js"></script>

   <script src="<?php echo($g_webRoot);?>js/jquery.filterizr.js"></script>
    <script src="<?php echo($g_webRoot);?>js/controls.js"></script>

    <!-- Kick off Filterizr -->
    <script type="text/javascript">
        $(function() {
            //Initialize filterizr with default options
            $('.filtr-container').filterizr();
        });
    </script>
<?php
	if ($userId == 0) 
		echo("<script> var loginPopupShow = 1; </script>");
	else
		echo("<script> var loginPopupShow = 0; </script>");
	if ($showNoStudentPopup)
		echo("<script> var showNoStudentPopup = 1; </script>");
	else
		echo("<script> var showNoStudentPopup = 0; </script>");
		
	if ($showStudentPopup)
		echo("<script> var showStudentPopup = 1; </script>");
	else
		echo("<script> var showStudentPopup = 0; </script>");
	echo("<script> var cartDate = '" . $cartDate . "';</script>");
?>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>

   <script src="<?php echo($g_webRoot);?>includes/products-list.js"></script> 



</body>
</html>
