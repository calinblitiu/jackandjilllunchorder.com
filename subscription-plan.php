<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "subscription-plan";
	$pageTitle = "Jack & Jill - Subscription Plan";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/school-items.php");
	require_once($g_docRoot . "classes/subs.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");
	
	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;

	if ($userId == 0)
	   $popupLink = "notLoggedInPopup";
		

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
	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$selSchoolId = "0";		// this will contain the id of the school of selected student

	// check if a subscription is already in process
	$subsRow = $subs->getListForAUser($userId, 0, 1, "id_asc");
	if ($subsRow[0] && $subsRow[0]["user_id"] == $userId) {
		$studentId = $subsRow[0]["student_id"];
	}
	
	$srow = $settings->getRowById("ID", 1);


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

			// if a subscription is already in process , auto select this student in dropdown
			if ($studentId == $studentRow["ID"])
			   $sel = " selected ";
			else
			   $sel = "";
			$studentList .= "<option value=" . $studentRow["ID"] . " " . $sel . ">" . $studentRow["name"] . ", Class " . $studentRow["class_name"] . ", " . $studentRow["school_name"];
			
		}
		if (count($studentRows) == 0)
			$showNoStudentPopup = 1;

	}
	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");
		// fix css canteen code
	if ($mealDealRow["canteen_code"] == "GREEN")
		$mdcCode = "code_green";

	else if ($mealDealRow["canteen_code"] == "RED")
		$mdcCode = "code_red";

	if ($mealDealRow["canteen_code"] == "AMBER")
		$mdcCode = "code_amber";

	//get categories
	$catCount = $cats->getCount();
	$catRows = $cats->getList( 0, $catCount, "name asc");

	// check if user has already got eway token
	if ($userId > 0) {
		$mrow = $members->getRowById("ID", $userId);
		$tokenId = $mrow["eway_token_id"];

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
  .dropdown_font { font-size: 0.8em !important;}

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
.pro_quntity label input {
	font-size: 12px;
}
.pro_quntity label {
  font-size: 12px;
  color: #595959;
  font-family: 'Bubblegum Sans', cursive;
}

.btnCatSelected { border:1px solid #000000;}

 .nice-select .list { width:400px;}

</style>

</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
   
    <section class="products-headbg">
    	<div class="container">
    				<div class="inner_title subscription_pgtitl">
                    		<h2>Subscription Plan</h2>
                            <div class="subtitl_info">
                            <p>Subscription Plan ordering is perfect for busy parents. Use our 'set and forget' plan so you can say goodbye to manually placing
orders for your child  and never forget a lunch order again. It's as simple as 1, 2 & 3!</p>
<ol>
<li>Select the Recess & Lunch items for your child (you can change/update the order at any time too)</li>
<li>Select the days and frequency of the order</li>
<li>Pay weekly for your order by selecting your payment method </li>
<li>Cancel subscription at any time</li>
<li>Receive a text message to remind you the order is coming so you don't pack a lunch</li>
</ol>
<h4>We've got it all covered so you can relax and enjoy a day of lunch box duty.</h4>
</div>
                    </div>
    	 </div><!--container-->	
    </section>
    
     <section class="subscription_pg">
    	<div class="container">
    				<div class="inn_titl">
                             <h3>Subscription Plan</h3>
                      </div>
                      
                     <div class="subscription-form">
                     			<form name=frmSubs id=frmSubs onsubmit="return ovalidate(this);">
                                			<div class="form-group" id="divStudent">
                                                  <select class="wide dropdown_font" id="student" name="student" onchange="getSchoolWeekDays(); return false;" >
												    <option data-display="Select Name of Student">Select Name of Student</option>
													<?php echo($studentList); ?>

                                                  </select>
                                            </div><!--form-group-->
                                            
                                              <div class="chkbox_flds">
                                                    <div class="form-group flt_flds" id="divWeekDays">
                                                    </div><!--form-group-->
                                             </div>                        
                                              <h3>Would you like to add meal deal or create your own order</h3>
                                             <div class="chkbox_viewflds">
                                                     <div class="form-group fltview_flds">
                                                           
                                                            <div class="checkbox">
																<div style="display:none;">
                                                                  <label>
                                                                    <input type="checkbox" value="1" name="ckMealDeal" id="ckMealDeal">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                    Meal Deal
                                                                  </label>
																  </div>
                                                            </div>
                                                            <div class="view_mnu">
                                                                    <a href="#" id="lnkMealDeal" style="display:none;">View Meal Deal</a>
                                                            </div>
                                                            
                                                             <div class="checkbox">
                                                                  <label>
                                                                    <input type="checkbox" value="1" id="ckSelItems" name="ckSelItems">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                    Create Your Own Order
                                                                  </label>
                                                            </div>
                                                            <div class="view_mnu">
                                                                    <a href="#" id="lnkSelItems" style="display:none;">Select Items</a>
                                                            </div>
                                                    </div><!--form-group-->
                                            </div>
											<div style="display:none;">
                                            <h3>Select the frequency you would like this order to be delivered</h3>
                                            <div class="form-group">
                                                  <select class="wide" id="freq" name="freq">
                                                    <option data-display="Select School">Select Options</option>
                                                    <option value="1" selected>Weekly</option>
                                                    <option value="2">Fortnighly</option>
                                                    <option value="3" >Monthly</option>
                                                  </select>
                                            </div><!--form-group-->
											</div>
                                             <h3>Reminder Type</h3>
                                             <h5>Would you like a SMS reminder</h5>
                                             
                                                     <div class="form-group fltview_flds">
                                                           
                                                            <div class="checkbox">
                                                                  <label>
                                                                    <input type="checkbox" value="1"
																	id="ckReminder7am">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                    Yes, 7pm the night before the lunch order
                                                                  </label>
                                                            </div>
                                                            <div class="checkbox">
                                                                  <label>
                                                                    <input type="checkbox" value="1"
																	id="ckReminder7pm">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                   Yes, 7am the morning of the lunch order
                                                                  </label>
                                                            </div>
                                                          
                                                    </div><!--form-group-->
                                                    
                                            
                                            <div class="chkbox_flds">
                                                    <div class="form-group flt_flds">
                                                         <h4>Select Payment Method</h4>
														 <!--<div class="col-sm-4">
                                                            <div class="radio-inline">
                                                                  <label>
                                                                    <input type="radio" 
																	value="1" name="payType" 
																	id="payCash">
                                                                    Cash
																  </label>
															  </div>
														</div>-->
														<div class="col-sm-6">
															  <div class="radio-inline">
                                                                  <label>
                                                                    <input type="radio" 
																	value="1" name="payType" 
																	id="payEway" checked=1>
                                                                    Eway
																  </label>
	                                                           </div>
														</div>
														<div class="col-sm-6">
															   <div class="radio-inline">
                                                                  <label>
                                                                    <input type="radio" 
																	value="1" name="payType" 
																	id="payWallet">
                                                                    Wallet
																  </label>
																</div>
														</div> 

                                                                                                           													  </div><!--form-group-->
                                            </div>
                                            <div style="display:none;">
                                            <div class="chkbox_flds">
                                                    <div class="form-group flt_flds">
                                                         <h4>Payment Type</h4>
                                                            <div class="checkbox">
                                                                  <label>
                                                                    <input type="checkbox" value="">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                    Weekly
                                                                  </label>
                                                            </div>
                                                            <div class="checkbox">
                                                                  <label>
                                                                    <input type="checkbox" value="">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                    Fornightly
                                                                  </label>
                                                            </div>
                                                            <div class="checkbox">
                                                                  <label>
                                                                    <input type="checkbox" value="">
                                                                    <span class="cr"><i class="cr-icon glyphicon  glyphicon-ok"></i></span>
                                                                    Monthly
                                                                  </label>
                                                            </div>
                                                    </div><!--form-group-->
                                            </div>
                                            </div>
                                            <div class="sav_cotinu">
                                            			<button type="button" id="btnSubmit">Save & Continue</button>
                                            </div>
											<div class="clearfix"></div><br>
											 <div id="imgLoader" 
											 	class="progress progress-striped active" style="display:none;">
				                                 <div class="progress-bar progress-bar-red animate-progress-bar" role="progressbar" data-percentage="100%" style="width: 100%"></div>
                            </div>

                                            
                                            
                                </form>
                     
                     </div>
                      
                    
                      
                      
                      
                      
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
        <button type="button" class="close" data-dismiss="modal"  onclick="window.location='<?php echo($g_webroot);?>add-student';">&times;</button>
      
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


	  
 <!-- mealdeal Modal -->
<div id="mealDealPopup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
                    		<h4><?php echo($mealDealRow["name"]); ?></h4>
							<div class="clearfix"></div>
             			     <div class="row">
                			  <div class="col-lg-4 col-md-4 col-sm-5">
                            		<div class="pro_dtlimg">
                            		<img src="<?php echo($g_webRoot); ?>items/files/<?php echo($mealDealRow["image"]);?>">
                            		</div>
                              </div>
                              <div class="col-lg-8  col-md-8 col-sm-7">
							  				<form name=frmMealDeal id=frmMealDeal>
												<input type=hidden name=mealDealPrice id=mealDealPrice
														value="<?php echo($mealDealRow["price"]);?>">
                                                    <div  class="col-sm-1">
															 <span class="<?php echo($mdcCode);?>"></span>
                                                    </div>
                                                    
                                                    <div class="col-sm-11 text-left">
                                                            <p>
															  <?php echo(nl2br($mealDealRow["description"]));?>
															</p>
                                                    </div>
                                                    <div class="clearfix"></div><br><br><br>
                                                    <div class="col-sm-5">
                                                                           <b>Price: $ <?php echo(number_format($mealDealRow["price"], 2)); ?></b>
                                                    </div>

                                                    <div class="col-xs-12 visible-xs">
														<div class="clearfix"></div><br>
													</div>
													
                                                    <div class="col-sm-4 pro_quntity">
                                                                        <label>Quantity <input type="number" value="1" min="0" name="mdqty" id="mdqty" ></label>
                                                    </div>

													<div class="col-xs-12 visible-xs">
														<div class="clearfix"></div><br>
													</div>
													
    
                                                    <div class="col-sm-3"> <a id="lnkUpdateMD" href="#" class="btn btn-default">Update</a>
													</div>
                            
												</form>
                            </div>
                </div><!--row-->
                
	                     		
							<div class="clearfix"></div><br>
      </div> <!--modal-body-->
      
    </div>

  </div>
</div>

 <!-- mealdeal Modal -->
   
  <!-- item Modal -->
<div id="itemPopup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
	  			<form name=frmItem id=frmItem>
					<input type=hidden id="itemcat" name="itemcat" value="0">
                    		<h3>Select Item</h3>
							<div class="clearfix"></div>
							<div class="col-sm-12 well">
								<div class="col-sm-4">
								 <div class="radio-inline">
								  <label>
							       <input type="radio" name="mealTypes" id="mealTypeR" value="R" checked
								     onchange="getSchoolItems();return false;">
    							   Recess
								  </label>
								 </div>
								</div>

								<div class="col-sm-4">
									<div class="radio-inline">
									  <label>
									    <input type="radio" name="mealTypes" id="mealTypeL" value="L"
										onchange="getSchoolItems();return false;">
  									     Lunch
									  </label>
									</div>
								</div>

								<div class="col-sm-4">
									<div class="radio-inline">
									  <label>
									    <input type="radio" name="mealTypes" id="mealTypeRL" value="RL"
										onchange="getSchoolItems();return false;">
  									     Recess+Lunch
									  </label>
									</div>
								</div>
								
							</div>
							<div class="clearfix"></div><br>
							
							<div class="col-sm-4 text-left">
							 	<button type=button class="btn btn-sm btn-default btn-block catbtn" id="btnCat0" onclick="setCategory(0); return false;">All Categories</button>
								<div class="clearfix"></div><br>

								<?php
								 foreach($catRows as $catRow) { 
								?>
							      <button type=button class="btn btn-sm btn-default btn-block catbtn" onclick="setCategory(<?php echo($catRow["ID"]);?>); return false;" id="btnCat<?php echo($catRow["ID"]);?>">
								  	<?php echo($catRow["name"]);?>
								  </button>
								  <div class="clearfix"></div><br>
								<?php
								 }
								 ?>

							</div>
							<div class="col-sm-8" id="divSelItems">
								<table class="table table-bordered table-striped bg-white" 
									id="tblSelItems">
							<thead>
			    				<tr>
								<th class="col-sm-3 text-left">Image</th>
								<th class="col-sm-5 text-left">Name</th>
								<th class="col-sm-4 text-right">Price</small></th>
								</th>
			    				</tr>
							</thead>
							<tbody>
			    			</tbody>
						    </table>
	
							</div>
                     		
							<div class="clearfix"></div><br>
			</form>
      </div> <!--modal-body-->
      
    </div>

  </div>
</div>

 <!-- item Modal -->


 <!-- subs Modal -->
<div id="subsPopup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
	  			<form name=frmSubs id=frmSubs>
					<input type=hidden id="itemcat" name="itemcat" value="0">
                    		<h3 id="subsTotal">Subscription Items</h3>
							<div class="clearfix"></div>
							<table class="table table-bordered table-striped bg-white" id="tblSubs">
							<thead>
			    				<tr>
								<th class="col-sm-2">Item</th>
								<th class="col-sm-2">Meal Type</th>
								<th class="col-sm-2 text-right">Qty</small></th>
								<th class="col-sm-2 text-right">Price</small></th>
								<th class="col-sm-2 text-right">Total</small></th>
								<th class="col-sm-2">
										<button type="button" id="btnAddSubs" class="btn btn-default">
										<i class="fa fa-plus"></i>&nbsp;Add Item
									</button>

								</th>
			    				</tr>
							</thead>
							<tbody>
			    			</tbody>
						    </table>
						                     		
							<div class="clearfix"></div><br>
			</form>
      </div> <!--modal-body-->
      
    </div>

  </div>
</div>

 <!-- subs Modal -->

   

	<!-- dummy gateway POPUP -->
	 <div class="modal bounceIn animated in" id="dummy-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title" id="errorLabel">
				<?php if ($srow["eway_sandbox_flag"] == 1)  echo("Dummy Payment Gateway"); else echo("Our Secure Eway Payment Gateway");?>
			</div>
		  </div>
		  <div class="modal-body">
			
					<div class="panel-body">
						<form action="<?php echo($g_webRoot);?>ajax/add-subscription.php" method=post name=frmCredit id=frmCredit onsubmit="return cvalidate(this);" 
						<?php if ($srow["eway_sandbox_flag"] != 1) echo(" data-eway-encrypt-key=\"" .EWAY_CLIENT_API_KEY ."\"");?> action="<?php echo($g_webRoot);?>ajax/add-to-wallet.php">
							<input type=hidden name="paytype" id="paytype" value="WALLET">
							<input type=hidden name="student" id="student" value="0">

							<br>
							<div class="col-sm-12 text-center" id="divError" style="color:red;">
							</div>
							<br>
							 <div class="col-sm-4">Amount*</div>
                            <div class="col-sm-5">
                                    <input class="form-control" 
										maxlength=4 type="number" id="amount" name="amount"
										placeholder="Enter Amount"/>
				            </div>
							
							<br>
    	                     <div class="clearfix"></div>
							<br>
                            <div class="col-sm-4">Name On Card*</div>
                            <div class="col-sm-8">
                                    <input class="form-control" 
										maxlength=60 type="text" id="namec" name="namec"
										value="<?php echo($_SESSION["name"]);?>"
										placeholder="Name on the card"/>
				            </div>

                            <br>
							<div class="clearfix"></div>
							
							<br>
                            <div class="clearfix"></div>
							<br>
                            <div class="col-sm-4">Credit Card Number*</div>
                            <div class="col-sm-8">
                                    <input class="form-control" 
										maxlength=16 type="text" id="EWAY_CARDNUMBER" name="EWAY_CARDNUMBER"

										value="<?php if ($srow["eway_sandbox_flag"] == 1)
										   					echo ("4444333322221111");
										   			  else 
													       echo("");
											    ?>"
										placeholder="Card number without hyphens and spaces"/>
				            </div>

                            <br>
							<div class="clearfix"></div>
							<br>
                            <div class="col-sm-4">Card Expiry (MM/YYYY)*</div>
                            <div class="col-sm-4">
                                    <input class="form-control" maxlength=2 id="mm" name="mm"
									value=" <?php if ($srow["eway_sandbox_flag"] == 1) echo("09"); ?>"
									type="text" placeholder="MM" >&nbsp;&nbsp;
                            </div>
                            <div class="col-sm-4">
                                    <input class="form-control" maxlength=4 type="text" id="yyyy" 
									name="yyyy"
									value=" <?php if ($srow["eway_sandbox_flag"] == 1) echo("2018");?>"
									placeholder="YYYY" />&nbsp;&nbsp;
                            </div>
							<br>
                            <div class="clearfix"></div>
                            <div class="col-sm-4">CVC*</div>
                            <div class="col-sm-3">
                                    <input class="form-control"  value="<?php if ($srow["eway_sandbox_flag"] == 1) echo("123");?>"
										data-eway-encrypt-name="EWAY_CARDCVN"
									id="EWAY_CARDCVN" name="EWAY_CARDCVN" maxlength=5 type="text"  />&nbsp;&nbsp;
                            </div>
							<br>
                            <div class="clearfix"></div>
                            <div class="col-sm-7 ">
                                <div id="divError" class="color-red  col-sm-12">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                     <button class="btn btn-default btn-lg pull-right" id="btnPay">Make Payment</button>
                             </div>
							 <br>
                            <div class="clearfix"></div><br>
                            <div id="imgLoader" class="progress progress-striped active" style="display:none;">
                                 <div class="progress-bar progress-bar-red animate-progress-bar" role="progressbar" data-percentage="100%" style="width: 100%"></div>
                            </div>

                    </div> <!--panel-body-->

			</form>
					<?php if ($srow["eway_sandbox_flag"] != 1) { ?>
						<script src="https://secure.ewaypayments.com/scripts/eCrypt.min.js"></script>
					<?php } ?>


		  	
			<div class="clearfix"></div>
			<div class="col-sm-2"></div>
			<div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" id="btnModalClose" class="btn btn-sm" data-dismiss="modal">Close</button>
		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>
   



	<!-- token usage POPUP -->
	 <div class="modal bounceIn animated in" id="token-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title" id="errorLabel">Automatic Card Subscription</div>
		  </div>
		  <div class="modal-body">
			
				<div class="panel-body">
						<form name=frmToken id=frmToken onsubmit="return tvalidate(this);">
							<br>
							<div class="col-sm-12 text-center" id="divError2" style="color:red;">
							</div>
							<br>
							 <div class="col-sm-4">Amount*</div>
                            <div class="col-sm-5">
                                    <input class="form-control" 
										maxlength=4  id="amount2" name="amount2"
										placeholder="Enter Amount" disabled=1/>
				            </div>
							<?php if (false) { ?>
							 <div class="clearfix"></div><br>
                            <div class="col-sm-4">
							Card CVC*
							<br><small>(Reqd.for security)</small>
							</div>
                            <div class="col-sm-3">
                                    <input class="form-control"  value="123"
									id="cvv2" name="cvv2" maxlength=5 type="text"  />&nbsp;&nbsp;
                            </div>
							<br>
                            <div class="clearfix"></div>
							
							<br>
							<?php } ?>
    	                     <div class="clearfix"></div>
							<br><br>
                            <div class="col-sm-7 ">
							    <small>Your card details are stored in our Secure Payment Gateway. It will be automatically charged the subscription amount each week. OR </small><br>
								<a href="#" onclick="useCard(); return false;">Pay via Manual Card Payment</a>
                            </div>
                            <div class="col-sm-5">
                                     <button class="btn btn-default btn-lg pull-right" id="btnPay2">Charge Card</button>
                             </div>
                            <div class="clearfix"></div><br>
                            <div id="imgLoader2" class="progress progress-striped active" style="display:none;">
                                 <div class="progress-bar progress-bar-red animate-progress-bar" role="progressbar" data-percentage="100%" style="width: 100%"></div>
                            </div>

                    </div> <!--panel-body-->
					</form>

		  	
			<div class="clearfix"></div>
			<div class="col-sm-2"></div>
			<div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" id="btnModalClose2" class="btn btn-sm" data-dismiss="modal">Close</button>
		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>

   

 <!-- success  Modal -->
<div id="success-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Subscription Success</h4>
			 <div class="col-sm-12 ">
			 	<b>Your subscription is active. <br>
				<a href="#">Click here to see your active subscriptions</a></b>
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
             <h4>Subscription Error</h4>
			 <div class="col-sm-12 bg-danger" id="errorMessage">
			 	
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
		

	if ($showStudentPopup)
		echo("<script> var showStudentPopup = 1; </script>");
	else
		echo("<script> var showStudentPopup = 0; </script>");

	if ($tokenId == null || $tokenId == "") 
		echo("<script> var tokenId=null; </script>");
	else
		echo("<script>var tokenId = '" . $tokenId . "'; </script>");
	
  if ($_GET["success"] == 1)
  	echo("<script> var success = 1; </script>");
  else
    echo("<script> var success = 0; </script>");

?>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>

   <script src="<?php echo($g_webRoot);?>includes/subscription-plan.js"></script> 

</body>
</html>
