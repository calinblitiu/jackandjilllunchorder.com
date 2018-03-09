<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "edit-container";
	$pageTitle = "Jack & Jill Admin - Edit Item Container";
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/containers.php");

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$containers = new Containers($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	
	$pCount = $products->getCount();
    $pRows = $products->getList(null,null,null,null,null,null, 0, $pCount, "name_asc");


	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
			$arrData = ["name"=>$_POST["name"], "ctype"=>$_POST["ctype"]];

			$newId = $containers->update($arrData, $_POST["xid"]);
			$error = $containers->mError;
			if ($error != null && $error != "")
				exit("Error:" . $error);
			else {
				if ($_POST["xid"] != 0) 
					header("Location: containers.php");
				else
					header("Location: edit-container.php?id=" . $newId);
				exit();
			}
	}

	$id = $_GET["id"];
	if (!$id)
	  $id = 0;
	
	$row = $containers->getRowById("ID", $id);

	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title><?php echo($pageTitle); ?></title>

<?php require_once($g_docRoot . "components/admin-header.php"); ?>
<?php require_once($g_docRoot . "components/admin-styles.php"); ?>

<link rel="stylesheet" href="../css/bootstrap-multiselect.css">

</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><?php if ($id == 0) echo("Add"); else echo("Edit");?> Item Container</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
		
					<form name=frmCont id=frmCont method=post onsubmit="return xvalidate(this);">
						<input type=hidden name=xid id=xid value="<?php echo($id);?>">
						<div class="clearfix"></div><br>
					    <?php if ($dupError) { ?>
							   <script>
							   	  alert("This container already exists");
								  window.history.back();
							   </script>
						<?php exit(); } ?>
						  <div class="col-sm-7">
						  <div class="input-group">
							<div class="input-group-addon">Name*</div>
							<input name=name id=name
								maxlength=50 class="form-control"
								 value="<?php echo($row["name"]);?>">
						  </div>
						  </div>
						  <div class="col-sm-4">
						  	   	<select id="ctype" class="form-control" name="ctype" onchange="changeAllItems(); return false;">
									<option value="HOT" <?php if ($row["ctype"] == "HOT") echo(" selected"); ?>>HOT Items</option>
									<option value="COLD" <?php if ($row["ctype"] == "COLD") echo(" selected"); ?>>COLD Items</option>

								</select>
						  </div>
					
					<?php if ($row["ID"] > 0) { ?>
						<div class="clearfix"></div><br>
						<div class="col-sm-4">
							 <div class="text-center">
						 	  <b><span id="spanAllItems">All Hot Items</span></b>
							 </div>
								<Br>

							<select class="form-control" name="allitems" id="allitems" size="10" >
							</select>
						</div>
						<div class="col-sm-2 text-center">
								<br><br>
								<button type="button" id="btnRight">
									<i class="fa fa-arrow-circle-right fa-2x"></i>
								</button>
								<div class="clearfix"></div><br>
								<button type="button" id="btnLeft">
									<i class="fa fa-arrow-circle-left fa-2x"></i>
								</button>
								
						</div>
						<div class="col-sm-4">
							<div class="text-center">
						 	<b><span id="spanSelItems">Selected Items</span></b>
							</div>
							<br>
							
							<select class="form-control" name="selitems" id="selitems" size="10">
						    </select>
						</div>

						<div class="clearfix"></div><br>
						<div class="col-sm-12">
							<ul>
							 <li>1.Selecting item in All Items and Clicking right arrow selects an item with qty =1.</li>
							 <li>2.Selecting the same item and clicking right arrow will increment qty in selected item.</li>
							 <li>3.Selecting an item in Selected Items and clicking left arrow will decrement qty. If qty is 1 and left arrow is clicked, then item is removed.</li>	
							</ul>
						</div>

					<?php }  else { ?>
							<div class="col-sm-12 text-center">
							<br><br>
							   <h4>Once container is saved, then items can be added here</h4>
							</div>
					<?php } ?>
	 					<div class="clearfix"></div><hr>

						<div class="col-sm-8"></div>
						<div class="col-sm-4 text-right">
							<button type="button" class="btn btn-danger" id="btnCancel">Cancel</button>
							&nbsp;&nbsp;
							<button type="submit" class="btn btn-success" id="btnSubmit">Save</button>

						</div>
					</form>

					</div> <!--panel-body-->

				</div> <!--panel-default-->

			</div> <!--row-->
		</div> <!--page wrapper-->




	</div> <!--wrapper-->
  

<div id="loader" class="full-loading-bg" style="text-align:center; display:none;">
	<img src="../images/ajax-loader-large.gif">
</div>
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>
	<script src="../includes/jquery-ui/jquery-ui.js"></script>
	<script src="../js/bootstrap-multiselect.js"></script>	
	
	<script src="../includes/admin-edit-container.js"></script>
	
</body>

</html>
