<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "dashboard";
	$pageTitle = "Jack & Jill Catering - Dashboard";
	
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/subscriptions.php");
	require_once($g_docRoot . "classes/members.php");

	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$subscriptions = new Subscriptions($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	if ($_SESSION["admin_id"] != "1") {
		header("Location: index.php");
		exit();
	}

	// get totals
	$mrowCount = $members->getCountForSearch();
	$orowCount = $orders->getAdminCount();
	$srowCount = $subscriptions->getAdminCount();

	$srowCount = $schools->getCount();

	// get date totals of orders
	$ordersTotal = $orowCount;
	$ordersYearTotal = 0;
	$ordersMonthTotal = 0;
	$ordersDayTotal = 0;

	$from = date("Y") . "-01-01 00:00:00";
	$till = date("Y") . "-12-31 23:59:59";
	$ordersYearTotal = $orders->getCountBetweenDates($from, $till);
	
	$from = date("Y-m") . "-01 00:00:00";
	$till = date("Y-m-j") . " 23:59:59";
	$ordersMonthTotal = $orders->getCountBetweenDates($from, $till);	
	
	$from = date("Y-m-d") . " 00:00:00";
	$till = date("Y-m-d") . " 23:59:59";
	$ordersDayTotal = $orders->getCountBetweenDates($from, $till);	
	
	// get date totals of subscriptions
	$subsTotal = $srowCount;
	$subsYearTotal = 0;
	$subsMonthTotal = 0;
	$subsDayTotal = 0;

	$from = date("Y") . "-01-01 00:00:00";
	$till = date("Y") . "-12-31 23:59:59";
	$subsYearTotal = $subscriptions->getCountBetweenDates($from, $till);
	
	$from = date("Y-m") . "-01 00:00:00";
	$till = date("Y-m-j") . " 23:59:59";
	$subsMonthTotal = $subscriptions->getCountBetweenDates($from, $till);	
	
	$from = date("Y-m-d") . " 00:00:00";
	$till = date("Y-m-d") . " 23:59:59";
	$subsDayTotal = $subscriptions->getCountBetweenDates($from, $till);	
	
	$dueDate = date("Y-m-d");
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
                    <h3 class="page-header">Admin Dashboard</h3>
	
					<div class="clearfix"></div><br>
					<div class="col-sm-3">
					<a href="orders.php"><div class="col-sm-12 well text-center" 
						style="margin-right:5px;">
						<div class="col-sm-12">
							<i class="fa fa-files-o fa-fw fa-4x"></i>
						</div>
						<div class="col-sm-12">
						  <b>Orders (<?php echo(number_format($orowCount,0)); ?>)</b>
						</div>
					</div></a>
					<div class="clearfix"></div>
					
					<a href="subscriptions.php"><div class="col-sm-12 well text-center" 
						style="margin-right:5px;">
						<div class="col-sm-12">
							<i class="fa fa-calendar fa-fw fa-4x"></i>
						</div>
						<div class="col-sm-12">
						  <b>Subscriptions (<?php echo(number_format($srowCount,0)); ?>)</b>
						</div>
					</div></a>
					<div class="clearfix"></div>
					
					<a href="users.php"><div class="col-sm-12 well text-center"
							style="margin-right:5px;">
						<div class="col-sm-12">
							<i class="fa fa-user fa-fw fa-4x"></i>
						</div>
						<div class="col-sm-12">
						  <b>Users (<?php echo(number_format($mrowCount,0)); ?>)</b>
						</div>
					</div></a>
					<div class="clearfix"></div>
					
					<a href="schools.php"><div class="col-sm-12 well text-center"
							style="margin-right:5px;">
						<div class="col-sm-12">
							<i class="fa fa-child fa-fw fa-4x"></i>
						</div>
						<div class="col-sm-12">
						  <b>Schools (<?php echo(number_format($srowCount,0)); ?>)</b>
						</div>
					</div></a>
					</div> <!--col-sm-3-->

					<div class="col-sm-9">


					<div class="panel panel-default">
					    <div class="panel-heading">
							<div class="panel-title">Order Status</div>
						</div>
						<div class="panel-body">
								<div class="col-sm-4 well" style="background-color:#ffffff;">
									<div class="col-sm-8">
										Total
									</div>
									<div class="col-sm-4 text-right"">
										<?php echo(number_format($ordersTotal,0)); ?>
									</div>
									
									<div class="col-sm-8">
										This Year
									</div>
									<div class="col-sm-4 text-right"">
										<?php echo(number_format($ordersYearTotal,0)); ?>
									</div>

									<div class="col-sm-8">
										This Month
									</div>
									<div class="col-sm-4 text-right"">
										<?php echo(number_format($ordersMonthTotal,0)); ?>
									</div>
									
									<div class="col-sm-8">
										Today
									</div>
									<div class="col-sm-4 text-right">
										<?php echo(number_format($ordersDayTotal,0)); ?>
									</div>

								</div> <!--col-sm-4-->


								<div class="col-sm-8 text-center">
									 <b><span id="divDelivHeading">Orders Due for Delivery (0)</span></b>
									 <div class="clearfix"></div><br>
									 <form id=frmDeliv name=frmDeliv>

									  <div class="col-sm-5">
									   	  	<input class="form-control datefrom" 
											id="deliv_date" name="deliv_date"
											data-provider="datepicker"
											maxlength=15 value="<?php echo($dueDate);?>"
											placeholder="Delivery Date">

									  </div>
									  <div class="col-sm-3">
									  	<button type="button" id="btnDueOrders" 
												class="btn btn-default">Go</button>
									  </div>
									    <div class="col-sm-3">
									  	<button style="display:none;" type="button" id="btnPrintDueOrders" 
												class="btn btn-default">Print Slips</button>
									  </div>
									  <div class="clearfix"></div><br>
									  <div class="col-sm-12 text-center">
									  	<a href="#" id="lnkPrint" 
										style="display:none;" target=_blank>Click here to View Slips</a>
									  </div>

									  </form>
									 <div class="clearfix"></div><br>
									 <div id="divDueOrders" style="max-height:400px; overflow-y:auto;">

									 	<table class="table table-bordered table-striped bg-white"
											id="tblDueOrders">
											<thead>
			    							<tr>
												<th class="col-sm-2 text-left">Order#</th>
												<th class="col-sm-6 text-left">Details</th>
												</th>
							   				</tr>
											</thead>
											<tbody>
			    							</tbody>
									    </table>

									 </div>
								</div><!--col-sm-8-->
							
						</div>
					</div> <!--panel-->


					<div class="panel panel-default">
					    <div class="panel-heading">
							<div class="panel-title">Subscriptions Status</div>
						</div>
						<div class="panel-body">
								<div class="col-sm-4 well" style="background-color:#ffffff;">
									<div class="col-sm-8">
										Total
									</div>
									<div class="col-sm-4 text-right"">
										<?php echo(number_format($subsTotal,0)); ?>
									</div>
									
									<div class="col-sm-8">
										This Year
									</div>
									<div class="col-sm-4 text-right"">
										<?php echo(number_format($subsYearTotal,0)); ?>
									</div>

									<div class="col-sm-8">
										This Month
									</div>
									<div class="col-sm-4 text-right"">
										<?php echo(number_format($subsMonthTotal,0)); ?>
									</div>
									
									<div class="col-sm-8">
										Today
									</div>
									<div class="col-sm-4 text-right">
										<?php echo(number_format($subsDayTotal,0)); ?>
									</div>

								</div> <!--col-sm-4-->


								<div class="col-sm-8 text-center">
									 <b><span id="divDelivHeading2">Subscriptions Due for Delivery (0)</span></b>
									 <div class="clearfix"></div><br>
									 <form id=frmDeliv2 name=frmDeliv2>

									  <div class="col-sm-5">
									   	  	<input class="form-control datefrom" 
											id="deliv_date2" name="deliv_date2"
											data-provider="datepicker"
											maxlength=15 value="<?php echo($dueDate);?>"
											placeholder="Delivery Date">

									  </div>
									  <div class="col-sm-3">
									  	<button type="button" id="btnDueSubs" 
												class="btn btn-default">Go</button>
									  </div>
									    <div class="col-sm-3">
									  	<button style="display:none;" type="button" id="btnPrintDueSubs" 
												class="btn btn-default">Print Slips</button>
									  </div>
									  <div class="clearfix"></div><br>
									  <div class="col-sm-12 text-center">
									  	<a href="#" id="lnkPrint2" 
										style="display:none;" target=_blank>Click here to View Slips</a>
									  </div>

									  </form>
									 <div class="clearfix"></div><br>
									 <div id="divDueSubs" style="max-height:400px; overflow-y:auto;">

									 	<table class="table table-bordered table-striped bg-white"
											id="tblDueSubs">
											<thead>
			    							<tr>
												<th class="col-sm-2 text-left">Subs#</th>
												<th class="col-sm-6 text-left">Details</th>
												</th>
							   				</tr>
											</thead>
											<tbody>
			    							</tbody>
									    </table>

									 </div>
								</div><!--col-sm-8-->
							
						</div>
					</div> <!--panel-->
					
					</div> <!--col-sm-9-->

                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->
		</div> <!--page wrapper-->


	</div> <!--wrapper-->
  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	 <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo($g_webroot);?>vendor/metisMenu/metisMenu.min.js"></script>
	<script src="<?php echo($g_webRoot);?>js/bootstrap-datepicker.min.js"></script>

	<?php
		echo("<script> dueDate ='" . $dueDate . "'; </script>");
	?>
	<script src="<?php echo($g_webRoot);?>includes/admin-dashboard.js"></script>
	
</body>

</html>
