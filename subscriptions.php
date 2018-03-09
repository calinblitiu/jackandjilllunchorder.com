<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "subscriptions";
	$pageTitle = "Jack & Jill - Subscriptions";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/meal-deal.php");


	define("MAXROWSPERPAGE", 5);
	define("MAXPAGELINKS", 10);
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: " . $g_webRoot . "sign-in");
		exit;
	}
		
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subsItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

		
	$rowCount = $subscriptions->getCountForMember($userId);
        	
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

	    if ($nStartPage > 1) {
			$sPageLinks .=  "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . ($nStartPage - 1) . ");\">" . "<< Prev </button>&nbsp;";

		}

		if ($nStartPage >= MAXPAGELINKS) {
			$sPageLinks .=  "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . ($startPoint - MAXPAGELINKS) . ");\">" . "<< Prev " . MAXPAGELINKS . " pages</button>&nbsp;";

		}

		
		for($i = $startPoint; $i <= $maxLinks; $i++) {
			if ($i == $nStartPage)
				$sPageLinks = $sPageLinks . "<button type='button' class='btn btn-primary' onclick=\"doPaging(" . $i . ");\">" . $i . "</button>&nbsp;";
			else
				$sPageLinks = $sPageLinks . "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . $i . ");\">" . $i . "</button>&nbsp;";
		}

	   if ($nStartPage < $nPageCount) {
			$sPageLinks .=  "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . ($nStartPage + 1) . ");\">" . "Next >> </button>&nbsp;";

		}

		if ($nextSetFrom != null) {
			$sPageLinks .=  "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . $nextSetFrom . ");\">" . "Next " . MAXPAGELINKS . " pages >></button>&nbsp;";
		}
	}

	$nStartRec = 0;
	if ($nStartPage == 0)
		$nStartRec = 0;
	else
		$nStartRec = (intval(MAXROWSPERPAGE) * ($nStartPage-1));

	$rows = $subscriptions->getRowsForMember($userId, $nStartRec, MAXROWSPERPAGE);

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

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

			$studentList .= "<option value=" . $studentRow["ID"] . " >" . $studentRow["name"] . ", Class " . $studentRow["class_name"] . ", " . $studentRow["school_name"];
			
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
<link href="<?php echo($g_webRoot);?>css/bootstrap-datepicker3.min.css" rel="stylesheet">

</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
  
    <section class="my_profilepg">
        <div class="container">
        
        
        			<div id="horizontalTab">
						<?php require_once($g_docRoot . "components/account-menu.php"); ?>

                            <div class="resp-tabs-container">
                                    <form name=frmList id=frmList onsubmit="return false;">
									  <input type=hidden name=p id=p value="<?php echo($nStartPage);?>">
                                    </form>
                                                  <div>
												   <div>
												   <br>
												   <div class="back_btn"><a href="<?php echo($g_webRoot . "dashboard");?>">Back</a></div>

                                            				     <div class="tab_tittle">
                                                                			<h2>My Subscriptions (<?php echo($rowCount);?>)</h2>                                          
                                               						</div>
                                                                    
                                                                    <div class="orders_info">
                                                                    			<ul>
   		                         <?php foreach($rows as $row) { 

								    $startDate = $row["start_date"];
									$niceStartDate = date("D, M d, Y", strtotime($startDate));
									$nextDate = $row["next_date"];
									$niceNextDate = date("D, M d, Y", strtotime($nextDate));

									// get student 
									$studentRow = $students->getRowById("ID", $row["student_id"]);
	
									// get school
									$schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);

									// get class
									$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
									
									  
								 	// get items
							         $itemCount = $subsItems->getCountForSubscription($row["ID"]);
									 $irows = $subsItems->getRowsForSubscription($row["ID"], 0, $itemCount);

									 // get names of all items
									 $itemNames = "";
									 foreach($irows as $irow) {
									    if ($irow["product_id"] ==  MEAL_DEAL_ITEM_DISPLAY_ID) {
										   $irow["productname"] = $mealDealRow["name"];
										}
									 	if ($itemNames != "")
											$itemNames .= ", ";
										$itemNames .= $irow["productname"];
									 }
									 // take first item
									 $firstItem = $irows[0];
									 if ($firstItem["product_id"] == MEAL_DEAL_ITEM_DISPLAY_ID) {
									 	$firstItem["item_price"] = $mealDealRow["price"];
										$firstItem["image"] = $mealDealRow["image"];
										
									 }

									 $weekDays = "";
									 if ($row["day_sun"] == 1)
									 	$weekDays .= "Sun &nbsp;";
	 								 if ($row["day_mon"] == 1)
									 	$weekDays .= "Mon &nbsp;";
									 if ($row["day_tue"] == 1)
									 	$weekDays .= "Tue &nbsp;";
	 								 if ($row["day_wed"] == 1)
									 	$weekDays .= "Wed &nbsp;";
									 if ($row["day_thu"] == 1)
									 	$weekDays .= "Thu &nbsp;";
	 								 if ($row["day_fri"] == 1)
									 	$weekDays .= "Fri &nbsp;";
	 								 if ($row["day_sat"] == 1)
									 	$weekDays .= "Sat &nbsp;";
										

							?>
																					<li class="<?php echo($divCSS); ?>">
                                                                                        		
                                                                                                <div class="ordr_img"><img src="<?php echo($g_webRoot);?>items/files/<?php echo($firstItem["image"]);?>"></div>     
                                                                                                <div class="orders-dtls">
                                                                                                		<div class="ordr-status"><h4><?php echo($row["status"]);?></h4></div>
                                                                                                        <h3>Subscription No. <?php echo($row["ID"]);?><small> (Active Week: <span><?php echo($niceNextDate);?></span>) 
																										
</small></h3>
  <h4><?php echo($itemNames);?> 

  <small>Ordered For <?php echo("<b>" . $studentRow["name"] . "</b>, Class " . $classRow["name"] . ", " . $schoolRow["name"]); ?></small>
  </h4>


                                                                                                        
                                                                                                        <div class="price_iem">
                                                                                                        		<span>$ <b><?php echo(number_format($row["net_total"],2));?></b></span>    <span>Items: <b><?php echo($itemCount);?></b></span>                                                                                                        
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="View_dtls">
																											<a href="#" onclick="viewDetails(<?php echo($row["ID"]);?>); return false;">View Details</a>
   &nbsp;
   <?php if (false) { ?>
   <a href="#" onclick="doPrint(<?php echo($row["ID"]);?>, '<?php echo($row["invoice"]);?>'); return false;"><i class="fa fa-print"></i></a>
   <?php } ?>
   <?php if ($row["cancel_flag"] != 1) { ?>
   	<a id="lnkCancel<?php echo($row["ID"]);?>" href="#" onclick="cancel(<?php echo($row["ID"]);?>); return false;">Cancel Subscription</a>
	<span id="spanCancel<?php echo($row["ID"]);?>" style="display:none;">Please wait...</span>
   <?php } else { ?>
	   Marked for Cancellation
   <?php } ?>
</div>
                                                                                               
					<h4>Weekdays
					</h4>
					<p>
					   <?php echo($weekDays); ?>
					</p>
                                                                                                </div>
                                                                                        
                                                                                        </li> <!--dtlinfo-->
								<?php
								  }
								 ?>
                                                                                        
                                                                                                                                                                
                                                                                
                                                                                </ul>
                                                                    </div>
                                                                    
                                                                   
                                                </div> <!--order-->
                                    
                                    <div>
			 			<div class="col-sm-12 text-center">
							<?php echo($sPageLinks); ?>
						 </div>
					    <div class="clearfix"></div><br>
																				
                </div>
									
                                    <p></p>
                                    </div>
                                     <div>
                                 </div>
                                    
                                    
                            </div>
                    </div>
         </div><!--container-->
    </section>



       <!-- Modal -->
<div id="ordr_vdtels" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        		<div class="popordr-tbl">
                			 <div class="table-responsive">          
                                          <table class="table">
                                            <thead>
                                              <tr>
                                                <th>Subscription For</th>
                                                <th>Status</th>
                                                <th>Items</th>
                                                <th>Subscription No.: <span id="divXOrderNo">>1442381</span></th>
                                              </tr>
									        </thead>
                                            <tbody>
                                              <tr>
                                                <td id="divXStudent">Tinku</td>
                                                <td><span id="divXStatus">Active</span></td>
                                                <td id="divXItemCount">5</td>
                                                <td><b>Weekdays:</b> <span id="divXWeekdays"></span></td>
                                              </tr>
                                            </tbody>
                                          </table>
                              </div>
                </div>
                
                <div class="popordr-dtls">
						<ul id="ul_items">
                                                                                		<li>
                                                                                        		
                                                                                                <div class="popordr_img"><img src="images/pro5.jpg"></div>     
                                                                                                <div class="poporders-dtls">
                                                                                                        <h3>Burger<br><small>Ordered For Tinku</small> </h3>
                                                                                                        <h5>For Recess</h5>
                                                                                                        
                                                                                                        <div class="pop_price">
                                                                                                        		 	<h4>$20</h4>                                                                                                  
                                                                                                        </div>                                                                                                
                                                                                                </div>
                                                                                        
                                                                                        </li> <!--dtlinfo-->
                                                                                        
                                                                                        
                                                                                        <li>
                                                                                        		
                                                                                                <div class="popordr_img"><img src="images/pro3.jpg"></div>     
                                                                                                <div class="poporders-dtls">
                                                                                                        <h3>Cheese Sticks<br><small>Ordered For Tinku</small> </h3>
                                                                                                        <h5>For Recess</h5>
                                                                                                        
                                                                                                        <div class="pop_price">
                                                                                                        		 	<h4>$20</h4>                                                                                                  
                                                                                                        </div>                                                                                                
                                                                                                </div>
                                                                                        
                                                                                        </li> <!--dtlinfo-->
                                                                                        
                                                                                      
                                                                                
                                                                                
                                                                                </ul>    
	                 
                
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
        <button type="button" class="close" data-dismiss="modal" onclick="window.location.reload();">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Order Updated</h4>
			 <div class="col-sm-12 ">
			 	<b>Order was successfully updated</b>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>

  <!-- print  Modal -->
<div id="print-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Order Printing</h4>
			 <div class="col-sm-12 ">
			 	<a href="#" id="lnkPrint" target=_blank">Click For Print Preview</a>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>


<?php
   echo("<script> var mealDealItemDisplayId = " . MEAL_DEAL_ITEM_DISPLAY_ID . "; </script>");
?>


<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>

<script src="<?php echo($g_webRoot);?>includes/subscriptions.js"></script>

</body>
</html>
