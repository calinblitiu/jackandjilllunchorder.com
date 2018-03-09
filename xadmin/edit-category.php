<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "edit-category";
	$pageTitle = "Jack & Jill Admin - Edit Menu Category";
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/categories.php");


	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	

	$pCount = $products->getCount();
    $pRows = $products->getList(null,null,null,null,null,null, 0, $pCount, "name_asc");


	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
		// validate for duplicate name
		$checkRow = $cats->getRowByName($_POST["name"]);
		if ($checkRow && $checkRow["ID"] != $_POST["xid"]) {
			$dupError = true;
		} else {
			$products = $_POST["products"];
			$pList = implode(",", $products);
			$arrData = ["name"=>$_POST["name"], "items"=>$pList, "meal_type"=>$_POST["meal_type"]];

			$newId = $cats->update($arrData, $_POST["xid"]);
			$error = $cats->mError;
			if ($error != null && $error != "")
				exit("Error:" . $error);
			else {
				header("Location: categories.php");
				exit();
			}
		}
	}

	$id = $_GET["id"];
	if (!$id)
	  $id = 0;
	
	$row = $cats->getRowById("ID", $id);

	$pList = "";
    $arr = explode(",", $row["items"]);

	foreach($pRows as $pRow) {
	   $sel = "";
	   if (in_array($pRow["ID"], $arr))
	     $sel = " selected ";
	   $pList .= "<option value=" . $pRow["ID"] . " " . $sel . ">" . $pRow["name"] . "</option>";
	}

	
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
                    <h3 class="page-header"><?php if ($id == 0) echo("Add"); else echo("Edit");?> Menu Category</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
		
					<form name=frmCat id=frmCat method=post onsubmit="return xvalidate(this);">
						<input type=hidden name=xid id=xid value="<?php echo($id);?>">
						<div class="clearfix"></div><br>
					    <?php if ($dupError) { ?>
							   <script>
							   	  alert("This category already exists");
								  window.history.back();
							   </script>
						<?php exit(); } ?>
						  <div class="col-sm-4">
						  <div class="input-group">
							<div class="input-group-addon">Name*</div>
							<input name=name id=name
								maxlength=50 class="form-control"
								 value="<?php echo($row["name"]);?>">
						  </div>
						  </div>
						  <div class="col-sm-5">
						  		<select id="products" class="form-control" name="products[]"
										multiple="multiple">
									<?php echo($pList); ?>
								</select>
						  </div>
					
						  <div class="col-sm-3">
						    Show in Offline Menu For&nbsp;
						  	<select id="meal_type" name="meal_type" class="form-control">
						      <option value="RECESS" <?php if ($row["meal_type"] == "RECESS") echo(" selected"); ?>>RECESS</option>
							  <option value="LUNCH" <?php if ($row["meal_type"] == "LUNCH") echo(" selected"); ?>>LUNCH</option>
							</select>
						  </div>
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
	
	<script src="../includes/admin-edit-category.js"></script>
	
</body>

</html>
