<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "users";
	$pageTitle = "Jack & Jill Admin - List Of Users";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/subs.php");
	require_once($g_docRoot . "classes/subscriptions.php");



	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	if ($_SESSION["admin_id"] != "1") {
		header("Location: index.php");
		exit();
	}

	define("MAXROWSPERPAGE", 20);
	define("MAXPAGELINKS", 10);
	
		// check for deletion
	$delId = $_GET["del"];
	if (is_numeric($delId)) {

		 // check for usage

		$checkCount = $subscriptions->getCountForMember($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("User has subscription(s). Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $subs->getCountForAUser($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("User is creating a subscription. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $orders->getCountForMember($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("User has order(s). Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }


		$checkCount = $credits->getCountForMember($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("User has wallet credits. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }


		$checkCount = $cart->getCountForAUser($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("User has items in cart. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }


	
		$members->delete($delId);
		header("Location: users.php");
		exit;
		
	}

	// get params
	$fname = $_GET["fname"];
    $lname = $_GET["lname"];
	$email = $_GET["email"];
	
	$sort = $_GET["sort"];

	$rowCount = $members->getCountForSearch($fname, $lname, $email);
	if ($sort == null || $sort == "")
		$sort = "signupdesc";
        	
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

	$rows = $members->getListForSearch($fname, $lname, $email, $sort, $nStartRec, MAXROWSPERPAGE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title><?php echo($pageTitle); ?></title>


<?php require_once($g_docRoot . "components/admin-header.php"); ?>
<?php require_once($g_docRoot . "components/admin-styles.php"); ?>

</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">List Of Users (<?php echo($rowCount);?>)</h3>
                </div>
            </div> <!--row-->

	 <div class="row">
                <div class="col-lg-12">
			<form name=frmMenu id=frmMenu onsubmit="return xvalidate(this);">
				<input type=hidden name=p id=p value="<?php echo($_GET["p"]); ?>">

					<div class="col-sm-2">
					  <input name=fname id=fname class="form-control" maxlength=30
						placeholder="F.Name" value="<?php echo($fname);?>">
					</div>
					<div class="col-sm-2">
					  <input name=lname id=lname class="form-control" maxlength=30
						placeholder="L.Name" value="<?php echo($lname);?>">
					</div>
					<div class="col-sm-2">
					  <input name=email id=email class="form-control" maxlength=100
						placeholder="Email" value="<?php echo($email);?>">
					</div>
					
					<div class="col-sm-2 text-right">
						<button class="btn btn-default">Submit</button>
					</div>
					
					<div class="col-sm-3 text-right">
						<select name=sort id=sort onchange="sortchange();" class="form-control">
							<option value="">Sort On</option>

							<option value="signupdesc" <?php if ($sort == "signupdesc") echo(" selected "); ?>>Signup Desc</option>
							<option value="signupasc" <?php if ($sort == "signupasc") echo(" selected "); ?>>Signup Asc</option>
							<option value="fnamedesc" <?php if ($sort == "fnamedesc") echo(" selected "); ?>>F.Name Desc</option>
							<option value="fnameasc" <?php if ($sort == "fnameasc") echo(" selected "); ?>>F.Name Asc</option>
							<option value="lnamedesc" <?php if ($sort == "lnamedesc") echo(" selected "); ?>>L.Name Desc</option>
							<option value="lnameasc" <?php if ($sort == "lnameasc") echo(" selected "); ?>>L.Name Asc</option>

						</select>
					</div>
					

			</form>
					<div class="clearfix"></div><br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
 <?php if (count($rows) == 0) { ?>
	<div class="col-sm-12 text-center"><h4>No records found</h4></div>
 <?php } else { ?>
 
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
										<th class="col-sm-2">Image</th>
                                        <th class="col-sm-3">Name</th>
										<th class="col-sm-3">Contact</th>
										<th class="col-sm-3">
										</th>
                                    </tr>
                                </thead>
                                <tbody>
				<?php 
				   foreach($rows as $row) {
				     if ($row["image"] == null || $row["image"] == "")
					 	$image = $g_webRoot . "images/up_img.png";
					 else
					 	$image = $g_webRoot . "profiles/files/" . $row["image"];

					 $studentCount = $students->getCountForAUser($row["ID"]);
				 ?>
                  <tr>
				    <td>
						<img src="<?php echo($image); ?>"
						   class="img img-responsive">
					</td>
					<td>
							<?php 
								echo($row["fname"] . " " . $row["lname"]); 
								echo("<br><small>Signup: " . getNiceDate($row["signup_date"], DATE_NOTIME) . "</small>");
							
							?>
					</td>
					<td>
							<?php echo("<small>Email: " . $row["emailid"] . "<br>Ph.:" . $row["mobile"] . "</small>"); ?>
					</td>
                     <td class="center">
						<a class="btn btn-sm btn-default" href="#" onclick="showStudents(<?php echo($row["ID"]);?>); return false;">
							<i class="fa fa-child"></i>&nbsp;(<?php echo($studentCount);?>)
						</a>&nbsp;
						<a class="btn btn-sm btn-default" href="#" onclick="showNotifications(<?php echo($row["ID"]);?>); return false;">
							<i class="fa fa-bell"></i>
						</a>&nbsp;
						<a class="btn btn-sm btn-default" href="#" onclick="doLogin(<?php echo($row["ID"]);?>); return false;">
							<i class="fa fa-key"></i>
						</a>&nbsp;
						
						<a class="btn btn-sm btn-default pull-right" href=# onclick="doDel(<?php echo($row["ID"]); ?>); return false;">
							<i class="fa fa-trash"></i> 
						</a>&nbsp;
						<hr>
						<?php if ($row["is_blocked"] == 1) { ?>
							<button class="btn btn-default" id="btnBlock<?php echo($row["ID"]);?>" onclick="block(<?php echo($row["ID"]);?>, 0); return false;"><i id="blockicon<?php echo($row["ID"]);?>" class="fg-green fa fa-unlock"></i>&nbsp;<span id="blocklabel<?php echo($row["ID"]);?>">Activate</span></button>
						<?php } else { ?>
							<button id="block<?php echo($row["ID"]);?>" class="btn btn-default" onclick="block(<?php echo($row["ID"]);?>, 1); return false;"><i id="blockicon<?php echo($row["ID"]);?>" class="fg-red fa fa-lock"></i>&nbsp;<span id="blocklabel<?php echo($row["ID"]);?>">Deactivate</span></button>

						<?php } ?>

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
<?php
 }
?>
			</div> <!--panel-body-->
		</div> <!--panel-->	
	</div> <!--row-->	
	</div> <!--page wrapper-->


	</div> <!--wrapper-->
  

   <div class="modal bounceIn animated in" id="students-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:75%;">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white" id="studentTitle">Students (0)</div>
		  </div>
		  <div class="modal-body">
		    <div class="clearfix"></div>
			<div class="col-sm-12" id="divStudents">
			</div>
			<div class"clearfix"></div>
			<br>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-sm" data-dismiss="modal">Close</button>

		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>


  <div class="modal bounceIn animated in" id="notifications-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog" style="width:55%;">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white">Member Notifications</div>
		  </div>
		  <div class="modal-body">
				<div class="table-responsive">          
				<table class="table">
					<thead>
					<tr>                                                                         
						<th></th>
						<th>New Order </th>
						<th>Order Status </th>
						<th>Newsletter</th>
					</tr>
				</thead>
				<tbody>
					<tr>
					<td>Email</td>
					
					<td id="email_neworder">
						
					</td>
				
					<td id="email_orderstatus">
					</td>
				
					<td id="email_newsletter">
					</td>

					</tr>

					<tr>
					<td>SMS</td>
					
					<td id="sms_neworder">
					</td>
				
					<td id="sms_orderstatus">
					</td>
				
					<td id="sms_newsletter">
					</td>

					</tr>

		         </tr>
				</tbody>
				</table>
		  	 </div> <!--table-responsive-->
		  </div> <!--modal-body-->
		  <div class="modal-footer">
			<button type="button" class="btn btn-sm" data-dismiss="modal">Close</button>

		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>

  <div id="loader" class="full-loading-bg" style="text-align:center; display:none;">
	<img src="../images/ajax-loader-large.gif">
</div>
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	<script src="../includes/admin-users.js"></script>
	

</body>

</html>
