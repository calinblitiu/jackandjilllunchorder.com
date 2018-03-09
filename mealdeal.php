<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "meal-deal";
	$pageTitle = "Jack & Jill - Meal Deal Details";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}
		

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	


	// get params
	$id = MEAL_DEAL_ITEM_DISPLAY_ID;
	$mealType = $_GET["selector"];

	if ($id < 1) {
		header("Location: " . $g_webRoot . "products-list");
		exit;
	}


	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	// check if he is already in the process of ordering for a student
	$checkCart = $cart->getListForAUser($userId, 0, 1, "date_desc");
	if ($checkCart && $checkCart[0]["user_id"] == $userId) {
			$studentRow = $students->getRowById("ID" , $checkCart[0]["student_id"]);

			$schoolRow = $schools->getRowbyId("ID", $studentRow["school_id"]);
			$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		
			$orderMessage = "Ordering for <b>" . $studentRow["name"] . "</b> Class: " . 
					$classRow["name"] . " of "  . $schoolRow["name"];

	}

	// get data saved in session which will be updated to the cart
	if ($_SESSION["cart_student"] != null && $_SESSION["cart_date"] != null &&
			    $_SESSION["cart_meal_type"] != null) {
		$cartStudent = $_SESSION["cart_student"];
		$cartDate = $_SESSION["cart_date"];
		$cartMealType = $_SESSION["cart_meal_type"];
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
			else if ($studentRow["ID"] == $cartStudent)
				$sel = " selected";
			else
			   $sel = "";
			$studentList .= "<option value=" . $studentRow["ID"] . " " . $sel . ">" . $studentRow["name"] . ", Class " . $studentRow["class_name"] . ", " . $studentRow["school_name"];
			
		}
	}


	// fix css canteen code
	if ($mealDealRow["canteen_code"] == "GREEN")
		$cCode = "code_green";

	else if ($mealDealRow["canteen_code"] == "RED")
		$cCode = "code_red";

	if ($mealDealRow["canteen_code"] == "AMBER")
		$cCode = "code_amber";

	// get meal deal  items
	$itemIds = "";
	$countOfItems = 0;
	$mealDealItems = unserialize($mealDealRow["items"]);
	foreach($mealDealItems as $mdItem) {
	   if ($itemIds != "")
	   		$itemIds .= ",";
	   $itemIds .= $mdItem["product_id"];
	   $countOfItems ++;
	}
	$itemRows = $products->getListInRange($itemIds, 0, $countOfItems, "name_asc");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>
<link href="<?php echo($g_webRoot);?>css/bootstrap-datepicker3.min.css" rel="stylesheet">

<style>

.code_red {
  background: #E71E28 !important;
  width: 30px;
  height: 30px;
  display: block;
  border-radius: 3px;
  position: absolute;
  right: 0;
  top: 10px;
}
.code_green {
  background: green !important;
  width: 30px;
  height: 30px;
  display: block;
  border-radius: 3px;
  position: absolute;
  right: 0;
  top: 10px;
}

.code_amber {
  background: #ffc200 !important;
  width: 30px;
  height: 30px;
  display: block;
  border-radius: 3px;
  position: absolute;
  right: 0;
  top: 10px;
}


</style>

</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
  
<section class="product_dtlpg">
		<div class="container">
				<div class="back_btn"><a href="<?php echo($g_webRoot);?>products-list">Back</a></div>
                <div class="pro_dtlinfo">
                <div class="row">
                			<div class="col-lg-4 col-md-4 col-sm-5">
                            		<div class="pro_dtlimg">
                            		<img src="<?php echo($g_webRoot); ?>items/files/<?php echo($mealDealRow["image"]);?>">
                            		</div>
                            </div>
                            <div class="col-lg-8  col-md-8 col-sm-7">
                            			<div class="pro_dtlcontt">
                                                    <div  class="pro_ctitl">
                                                            <h4><?php echo($mealDealRow["name"]); ?></h4>
															 <span class="<?php echo($cCode);?>">

                                                    </div>
                                                    
                                                    <div class="pro_dtltxt">
                                                            <p>
															  <?php echo($mealDealRow["description"]);?>
															</p>
                                                    </div>
                                                    
                                                    <div class="pro_pric">
                                                                <div class="pro_pricinfo">
                                                                           <h3><span><img src="<?php echo($g_webRoot); ?>images/price_grnmrk.png"></span> Price: <span>$ <?php echo(number_format($mealDealRow["price"], 2)); ?></span></h3>
                                                                </div>
                                                                
                                                                <div class="pro_quntity">
                                                                        <label>Quantity <input type="number" value="1" name="qty" id="qty" ></label>
                                                                </div>
                                                                
                                                                <div class="pro_cart"> <a id="lnkAddToCart" href="#" onclick="addToCart(); return false;">Add to Cart</a>
																<img src="<?php echo($g_webRoot);?>images/ajax-loader.gif" class="img" style="display:none;" id="imgLoader" >
																</div>
                                                    
                                                    </div><!--pro_pric-->
                                        
                           		</div>
                            
                            </div>
                </div><!--row-->
                
			   <?php if ($orderMessage != null) { ?>
			   			 <div class="clearfix"></div><Br>
						  <div class="col-sm-12 text-center">
						    <?php //echo($orderMessage); ?>
						  </div>
			  <?php } ?>

                
                </div><!--pro_dtlinfo-->
		</div>
</section>    
     
<section class="reltd_pros">
	<div class="container">    
        		<div class="inn_titl">
                             <h3>Items In Meal Deal</h3>
                </div>
        <div class="pro_list">
                      				<div class="row">
									 <?php
									   foreach($itemRows as $rrow) { 
									     $link = $g_webRoot . "product-details/" . $rrow["ID"] . "/"
										    . $mealType;
									   ?>

                                    
                                    			<div class="col-lg-3 col-md-3 col-sm-4">
                                                    <div class="pro_info"> <div class="pro_img"><a href="<?php echo($link);?>"><img src="<?php echo($g_webRoot . "items/files/" . $rrow["image"]); ?>"></a>
                                                                    <div class="pro_dtl">
                                                                            <div class="pro_name"><a href="<?php echo($link);?>"><?php echo($rrow["name"]);?></a></div>
                                                                            <div class="pro_prize"><h5>$<?php echo(number_format($rrow["price"],2)); ?></h5></div>
                                                                    </div></div>
                                                    </div><!--pro_info-->
                                                </div><!--col-lg-3 col-md-3 col-sm-4-->
                                             <?php
											    }
										?>
                                                                                             
                 			 </div><!--row-->
        
   </div>
</section>    

   

 <!-- orderingslct Modal -->
<div id="orderingslct_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="ordringinfo">
                    		<h4>Ordering For</h4>
                         	<form name="frmO" id="frmO" onsubmit="return ovalidate(this);">
						 		<input type=hidden name=pid id=pid value="<?php echo($id);?>">
								<input type=hidden name=price id=price value="<?php echo($pRow["price"]);?>">
								<input type=hidden name=mtype id=mtype value="<?php echo($mealType);?>">
                         			<div class="form-group" id="divStudent">
                                                  <select class="wide" id="student" name="student" onchange="getSchoolDays(); return false;">
                                                    <option data-display="Select Name of Student">Select Name of Student</option>
													<?php echo($studentList); ?>
                                                  </select>
                                    </div><!--form-group-->
                                            
                                             <div class="form-group datef" id="divDate">
                                                                    <input data-provide="datepicker" class="datepicker1 form-control" placeholder="Select  Date of Delivery" id="date" name="date"
													value ="<?php echo($checkCart[0]["order_date"]);?>">
                                                               </div>
                                                                  
                         
                         
                         
                         <div class="ordr-btn">
                         		<button type="submit"> Order Now</button>                         
                         </div>
						 </form>
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>
</div>

<!-- orderingslct Modal -->


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
                                                                    <form>
                                                                    
                                                                        <ul>
                                                              <li>
                                                                <input type="radio" id="f-option" name="selector">
                                                                <label for="f-option">Recess</label><span>$5</span>
                                                                
                                                                <div class="check"></div>
                                                              </li>
                                                              
                                                              <li>
                                                                <input type="radio" id="t-option" name="selector">
                                                                <label for="t-option">Lunch</label><span>$5</span>
                                                                
                                                                <div class="check"><div class="inside"></div></div>
                                                              </li>
                                                              
                                                              <li>
                                                                <input type="radio" id="rl-option" name="selector">
                                                                <label for="rl-option">Both (Recess+Lunch)</label><span>$8</span>
                                                                
                                                                <div class="check"><div class="inside"></div></div>
                                                              </li>
                                                              
                                                                    </ul>
                                                         <div class="ordr-btn">
                         		<button type="submit" id="btnOrder" name="btnOrder" >Order Now</button>                         
                         </div>           
                                                                   
                                                                    </form>
       										 </div>
                                                                  
                         
                         
                  
                         
                         
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>
</div>

 <!-- orderingslctradio Modal -->
     

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


  <!-- error Modal -->
<div id="error-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Cart Error</h4>
			 <div class="col-sm-12 bg-danger" id="errorMessage">
			 	
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
	
	if ($checkCart && $checkCart[0]["user_id"] == $userId) 
		echo(" var orderInProcess = 1;");
	else
		echo(" var orderInProcess = 0;");

	if ($cartStudent != null && $cartStudent != "") 
		echo(" var orderInSession = 1;");
	else
		echo(" var orderInSession = 0;");

?>
</script>


<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>


<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/mealdeal.js"></script> 

       
        

</body>
</html>
