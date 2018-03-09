<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "subscriptions";
	$pageTitle = "Jack & Jill Admin - Subscriptions";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/subscription-items.php");
	require_once($g_docRoot . "classes/meal-deal.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/members.php");


	$subs = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subsItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	if ($_SESSION["admin_id"] != "1") {
		header("Location: index.php");
		exit();
	}

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	define("MAXROWSPERPAGE", 10);
	define("MAXPAGELINKS", 10);
	
		// check for deletion
	$delId = $_GET["del"];
	if (is_numeric($delId)) {
		header("Location: schools.php");
		exit;
		
	}

	// get params
	$subsId = $_GET["subs_id"];
	$dateFrom = $_GET["date_from"];
	$dateTill = $_GET["date_till"];
	$memberId = $_GET["member_id"];
	$cancel_flag = $_GET["status"];

	$rowCount = $subs->getAdminCount($subsId, $dateFrom, $dateTill, $memberId, $cancel_flag);
        	
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
			$sPageLinks .=  "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . ($startPoint - MAXPAGELINKS) . ");\">" . "<< Prev " . MAXPAGELINKS . " pages</button>&nbsp;";

		}
		for($i = $startPoint; $i <= $maxLinks; $i++) {
			if ($i == $nStartPage)
				$sPageLinks = $sPageLinks . "<button type='button' class='btn btn-primary' onclick=\"doPaging(" . $i . ");\">" . $i . "</button>&nbsp;";
			else
				$sPageLinks = $sPageLinks . "<button type='button' class='btn btn-default'  onclick=\"doPaging(" . $i . ");\">" . $i . "</button>&nbsp;";
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

 	$rows = $subs->getAdminRows($subsId, $dateFrom, $dateTill, $memberId, $cancel_flag, $nStartRec, MAXROWSPERPAGE);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title><?php echo($pageTitle); ?></title>


<?php require_once($g_docRoot . "components/admin-header.php"); ?>
<?php require_once($g_docRoot . "components/admin-styles.php"); ?>
<link href="<?php echo($g_webRoot);?>css/bootstrap-datepicker3.min.css" rel="stylesheet">

</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">List Of Subscriptions (<?php echo($rowCount);?>)</h3>
                </div>
            </div> <!--row-->

	 <div class="row">
                <div class="col-lg-12">
			<form name=frmMenu id=frmMenu onsubmit="return xvalidate(this);">
				<input type=hidden name=p id=p value="<?php echo($_GET["p"]); ?>">

					<div class="col-sm-2">
						<input class="form-control" id="subs_id" name="subs_id"
								type="number" maxlength=6 value="<?php echo($subsId);?>"
								placeholder="Subs. Id">
					</div>
					<div class="col-sm-2">
						<input class="form-control datefrom" id="date_from" name="date_from"
								data-provider="datepicker"
								 maxlength=15 value="<?php echo($dateFrom);?>"
								placeholder="Date From">
					</div>
					<div class="col-sm-2">
						<input class="form-control datetill" id="date_till" name="date_till"
								data-provider="datepicker"
								 maxlength=15 value="<?php echo($dateTill);?>"
								placeholder="Date Till">
					</div>
					<div class="col-sm-2">
						<select name="status" id="status" class="form-control">
							<option value="" <?php if (!$cancel_flag) echo(" selected");?>>All Status</option>
							<option value="1" <?php if ($cancel_flag == "1") echo(" selected");?>>Only Cancelled</option>
							<option value="0" <?php if ($cancel_flag == "0") echo(" selected");?>>Only Active</option>
						</select>

					</div>
					
					<div class="col-sm-3 text-right">
						<button class="btn btn-default">Submit</button>
					</div>
					<div class="col-sm-1">
						<button type="button" id="btnReset" class="btn btn-default">Reset</button>
					</div>

					<div class="clearfix"></div><br>
					<div class="col-sm-3">
					</div>
					<div class="col-sm-9 text-right">
					</div>
					
					

			</form>
					<div class="clearfix"></div><br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
	<?php 
		if (count($rows) == 0) { ?>
			  <div class="col-sm-12 text-center">
			  	<h4>No records found</h4>
			  </div>
	<?php } else { ?>	
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
									 <th class="col-sm-1">
									</th>
                                        <th class="col-sm-2">
										 Order
										</th>
										<th class="col-sm-3">Student</th>
										<th class="col-sm-4">Total</th>
										<th class="col-sm-2">Status</th>
										<th class="col-sm-1">
						  				</th>
                                    </tr>
                                </thead>
                                <tbody>
				<?php 
				   foreach($rows as $row) {
					   $niceDate = date("D, M d, Y", strtotime($row["date"]));		
									// get student 
					   $studentRow = $students->getRowById("ID", $row["student_id"]);

					   // get school
					   $schoolRow = $schools->getRowById("ID", $studentRow["school_id"]);

					   // get class
					   $classRow = $classes->getRowById("ID", $studentRow["class_id"]);

					   $notes = $row["notes"];
					   if ($notes  == null || $notes == "")
						   $notes = "None";

					   // get items
					   $itemCount = $subsItems->getCountForSubscription($row["ID"]);
					   $irows = $subsItems->getGroupedRowsForSubscription($row["ID"], 0, $itemCount);

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

				
					   // get user name
					   $mrow = $members->getRowById("ID", $row["member_id"]);
				   
				 ?>
                  <tr>
				  	<td>

					</td>
				    <td>
						<img src="<?php echo($g_webRoot);?>items/files/<?php echo($firstItem["image"]);?>"   class="img img-responsive">
						<div class="clearfix"></div><br>
						Subs. #: <b><?php echo($row["ID"]);?></b><br>
						Days: <b><?php echo($weekDays);?></b><br><br>
						User: <b><?php echo($mrow["fname"] . " " . $mrow["lname"]);?></b><br>
						Placed On: <b><?php echo($niceDate);?></b><br>
						Starting: <b><?php echo(getNiceDate($row["start_date"], DATE_EXCEL)); ?></b><br>
											
					</td>
					<td>
						<b><?php echo($studentRow["name"]);?></b><br>
						Class <?php echo($classRow["name"]);?></br>
						<?php echo($schoolRow["name"]);?>
					</td>
					<td>
						<div class="col-sm-7 text-left">
							<b>Total: $ <?php echo(number_format($row["net_total"],2)); ?></b>
						</div>
						<div class="col-sm-5 text-right">
							<?php echo($itemCount . " item(s)");?>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-12" style="background-color:#ffffff; padding:5px; margin-top:5px;">
						  
						  <?php
						     $currMT = "";
						     foreach($irows as $irow) {
							 	 if ($irow["product_id"] == MEAL_DEAL_ITEM_DISPLAY_ID) {
								 	$irow["item_price"] = $mealDealRow["price"];
									$irow["productname"] = $mealDealRow["name"];
									$irow["image"] = $mealDealRow["image"];
									
								 }

							     if ($irow["meal_type"] != $currMT)  {?>
						  		   <div class="col-sm-12">
								   <b><?php echo(mealTypeToString($irow["meal_type"]));?></b>
								   </div>
						  		<?php $currMt = $irow["meal_type"]; 
								
								} ?>
								
						  	   <div class="col-sm-2">
							     	<img src="<?php echo($g_webRoot);?>items/files/<?php echo($irow["image"]);?>" height=20px width=20px class="img" align=top>

							   </div>

							   <div class="col-sm-6">
							   	<small><?php echo($irow["productname"]);?></small><br>

								<small>
								  <?php echo($irow["item_qty"] . " @ $" . number_format($irow["item_price"],2));?>
								  </small>
							   </div>
							   <div class="col-sm-4 text-right">
							   	<b><small>$<?php echo(number_format($irow["item_qty"] * $irow["item_price"],2));?> </small></b>
							   </div>

							   <div class="clearfix"></div><hr>
						  <?php
						    }
						   ?>
						</div>
					</td>
					<td>
					   <?php if ($row["cancelled_flag"] == 1) 
					   	  		echo("<span class=\"fg-red\">Cancelled</span>");
							 else if ($row["cancel_flag"] == 1)
					   	  		echo("<span class=\"fg-red\">Marked For Cancellation</span>");
						     else
							 	echo("Active");
						?>
						
					</td>
                     <td class="center">
					</td>
                   </tr>
				<?php
					} 
				?>
				</tbody>
			  </table>

			 <div class="col-sm-12 text-right">
				<?php echo($sPageLinks); ?>
			 </div>


	<?php } ?>			 
			</div> <!--panel-body-->
		</div> <!--panel-->	
	</div> <!--row-->	
	</div> <!--page wrapper-->


	</div> <!--wrapper-->
  

<div id="loader" class="full-loading-bg" style="text-align:center; display:none;">
	<img src="../images/ajax-loader-large.gif">
</div>
  <?php
	echo("<script> var webRoot = '" . $g_webRoot . "'; </script>");
  ?>
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

 	<script src="<?php echo($g_webRoot);?>js/bootstrap-datepicker.min.js"></script>

	<script src="../includes/admin-subscriptions.js"></script>
	

</body>

</html>
