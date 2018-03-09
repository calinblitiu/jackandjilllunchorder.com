<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "menu";
	$pageTitle = "Jack & Jill Admin - Menu Items";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/order-items.php");
	require_once($g_docRoot . "classes/school-items.php");
	require_once($g_docRoot . "classes/subs.php");
	require_once($g_docRoot . "classes/subscription-items.php");

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orderItems = new OrderItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subs = new Subs($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptionItems = new SubscriptionItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


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
		$checkCount = $cart->getCountForProduct($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("Item is in member cart(s). Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $subscriptionItems->getCountForProduct($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("Item is in subscription(s). Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $subs->getCountForProduct($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("Item is being added to subscription(s). Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $schoolItems->getCountForProduct($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("Item is in school disabled items. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $orderItems->getCountForProduct($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("Item is present in Order(s). Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

			 
		 
		 
		$nutrition->deleteByExpression("product_id=" . $delId);
		$allergies->deleteByExpression("product_id=" . $delId);
		$products->delete($delId);
		header("Location: menu.php");
		exit;
		
	}

	// get params
	$name = $_GET["xname"];
	$ftype = $_GET["ftype"];
	$recess = $_GET["recess"];
	$lunch = $_GET["lunch"];
	$global = $_GET["global"];
	$sort = $_GET["sort"];

	$rowCount = $products->getCount($name, $ftype, $recess, $lunch, $mealDeal, $global);
	if ($sort == null || $sort == "")
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

	$rows = $products->getList($name, $ftype, $recess, $lunch, $mealDeal, $global, $nStartRec, MAXROWSPERPAGE, $sort);
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
                    <h3 class="page-header">Menu Items (<?php echo($rowCount);?>)</h3>
                </div>
            </div> <!--row-->

	 <div class="row">
                <div class="col-lg-12">
			<form name=frmMenu id=frmMenu>
				<input type=hidden name=p id=p value="<?php echo($_GET["p"]); ?>">

					<div class="col-sm-3">
					  <input name=xname id=xname class="form-control" maxlength=50
						placeholder="Item Name" value="<?php echo($name);?>">
					</div>
					<div class="col-sm-2">
					  <select name=ftype id=ftype class="form-control">
						 <option value="">All Types</option>
						 <option value="HOT" <?php if ($ftype == "HOT") echo(" selected"); ?>>Hot</option>
						 <option value="COLD"  <?php if ($ftype == "COLD") echo(" selected"); ?>>Cold</option>
					  </select>
					</div>
					<div class="col-sm-1">
					  	<div class="checkbox">
							<label>
							<input id="recess" name="recess" value="1" type="checkbox" <?php if ($recess == "1") echo(" checked"); ?>>Recess
							</label>
						</div>
					</div>
					<div class="col-sm-1">
						<div class="checkbox">
							<label>
							<input id="lunch" name="lunch" value="1" type="checkbox" <?php if ($lunch == "1") echo(" checked"); ?>>Lunch
							</label>
						</div>
					</div>
					<div class="col-sm-1">
						<div class="checkbox">
							<label>
							<input id="global" name="global" value="1" type="checkbox" <?php if ($global == "1") echo(" checked"); ?>>Global
							</label>
						</div>
					</div>
					<div class="col-sm-1"></div>
					<div class="col-sm-3 text-right">
						<button class="btn btn-default">Submit</button>
					</div>

			</form>
					<div class="clearfix"></div><br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
  <?php
  	if (count($rows) == 0) { 
  ?>
  		<div class="col-sm-12 text-center"><h4>No records found</h4></div>
  <?php } else { ?>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <!--<th class="col-sm-1">
						<div class="checkbox">
							<label>
							<input id="ckAll" name="ckAll" type="checkbox"> 
							</label>
						</div>
					
					</th>-->
                                        <th class="col-sm-3">Item</th>
                                        <th class="col-sm-2 text-right">Price</th>
                                        <th class="col-sm-4">Status</th>
                                        <th class="col-sm-3">
					  	<a class="btn btn-default" id="lnkAdd">
							<i class="fa fa-plus"></i>
						</a>
						&nbsp;
						<!--<a id="lnkDelete" class="btn btn-default pull-right">
							<i class="fa fa-trash"></i> </a>&nbsp;-->
					</th>
                                    </tr>
                                </thead>
                                <tbody>
				<?php 
				   foreach($rows as $row) {
				     
				 ?>
                                    <tr>
				      <?php if (false) { ?>
                                        <td>
						<div class="checkbox">
							<label>
							<input id="ck<?php echo($row["ID"]);?>" class="checkall" type="checkbox" value=1> 
							</label>
						</div>

					</td>
				     <?php } ?>
                     <td>
					    <div class="col-sm-12">
						<img src="../items/files/<?php echo($row["image"]);?>"
						   class="img img-responsive">
					    </div>
					    <div class="col-sm-12 text-center">
						<b><?php echo($row["name"]);?></b>
					    </div>
					</td>
                                        <td class="text-right"><?php echo("$".  number_format($row["price"],2));?></td>
                                        <td>
						<?php 
						 echo("Food Type: <b>" . $row["food_type"] . "</b><br>");
						 if ($row["flag_recess"] == 1)
						    echo("Recess :<i class=\"fg-green fa fa-check-square\"></i><br>");
						 else
						    echo("Recess :<i class=\"fg-red fa fa-minus-square\"></i><br>");

						 if ($row["flag_lunch"] == 1)
						    echo("Lunch :<i class=\"fg-green fa fa-check-square\"></i><br>");
						 else
						    echo("Lunch :<i class=\"fg-red fa fa-minus-square\"></i><br>");
		 				
		 				if ($row["flag_global"] == 1)
						    echo("Global :<i class=\"fg-green fa fa-check-square\"></i><br>");
						 else
						    echo("Global :<i class=\"fg-red fa fa-minus-square\"></i><br>");



						?>
					</td>
                                        <td class="center">
						<a class="btn btn-sm btn-default" href="edit-product.php?id=<?php echo($row["ID"]);?>">
							<i class="fa fa-edit"></i>
						</a>&nbsp;
						<a class="btn btn-sm btn-default pull-right" href=# onclick="doDel(<?php echo($row["ID"]); ?>); return false;">
							<i class="fa fa-trash"></i> 
						</a>&nbsp;
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
  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	<script src="../includes/admin-menu.js"></script>
	

</body>

</html>
