<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "edit-category";
	$pageTitle = "Jack & Jill Admin - Edit Menu Category";
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/meal-deal.php");


	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	




	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
	    $arrData = ["name"=>$_POST["name"], "price"=>$_POST["price"], "image"=>$_POST["file_image"],
		 "description"=>$_POST["descrip"], "canteen_code"=>$_POST["ccode"]];
		$itemArr = array();
		
		foreach($_POST as $fld=>$value) {
		  if (stripos($fld, "cat") > -1) {
			 $catId = str_replace("cat", "", $fld);
			 $productId = $value;
			 $thisItemArr = ["cat_id"=>$catId, "product_id"=>$productId];
			 $itemArr[] = $thisItemArr;
		  }
		}
		$arrData["items"]= serialize($itemArr);
		
		$newId = $mealdeal->update($arrData, 1);
		$error = $mealdeal->mError;
		if ($error != null && $error != "")
				exit("Error:" . $error);
		else {
					header("Location: dashboard.php");
					exit();
		}
	}

	$row = $mealdeal->getRowById("ID", "1");

	$pList = "";
    $arr = explode(",", $row["items"]);

	// get all categories and generate dropdowns
	$crowCount = $cats->getCount();
    $crows = $cats->getList( 0, $crowCount, "name asc");
	
	$dropDowns = array();
	
	$mealDealItems = unserialize($row["items"]);
	
	foreach($crows as $crow) {
		$dropDown = array();
		$dropDown["category_id"]=$crow["ID"];
		$dropDown["category_name"]=$crow["name"];

		// locate the category entry in meal deal items
		$catEntry = array();
		foreach($mealDealItems as $mdItem) {
		  if ($mdItem["cat_id"] == $crow["ID"]) {
		    $catEntry = $mdItem;
			break;
		  }
		}

		// get products in each cat
		$items = $crow["items"];
		$itemsArr = explode(",", $items);

		$pList = "";
	    foreach($itemsArr as $arr) {
		  $thisPRow = $products->getRowById("ID", $arr);
	      $sel = "";

	      if ($catEntry["product_id"] == $arr)
		     $sel = " selected ";
		  $pList .= "<option value=" . $thisPRow["ID"] . " " . $sel . ">" . 
		  	$thisPRow["name"] . "</option>";
		}

		$dropDown["products"] = $pList;

		$dropDowns[] = $dropDown;

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


<link rel="stylesheet" href="../includes/jQuery-File-Upload-9.0.2/css/jquery.fileupload.css">


</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Setup Meal Deal</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
		
					<form name=frmMD id=frmMD method=post onsubmit="return xvalidate(this);">
						<input type=hidden name=xid id=xid value="<?php echo($id);?>">
						<input type=hidden name=file_image id=file_image value="<?php echo($row["image"]);?>">

						<div class="clearfix"></div><br>

						  <div class="col-sm-3">
						  <div class="input-group">
							<div class="input-group-addon">Name*</div>
							<input name=name id=name
								maxlength=50 class="form-control"
								 value="<?php echo($row["name"]);?>">
						  </div>
						  </div>

	 					 <div class="col-sm-3">
						  <div class="input-group">
							<div class="input-group-addon">Price*</div>
							<input name=price id=price
								maxlength=50 class="form-control" type="number"
								 value="<?php echo($row["price"]);?>">
						  </div>
						 </div>
						
						<div class="col-sm-6">
		 				 <div class="form-group" id="groupItem">
						  <div class="col-sm-4">
						  	<label for="flImage" class="control-label">Image* (Max 2 Mb)</label>
							<div class="clearfix"></div><br>
							<?php if ($row["image"] == null || $row["image"] == "") { ?>
								<img id="imgPreview" name=imgPreview style="display:none;" class="img img-responsive">
							<?php } else { ?>
								<img id="imgPreview" name=imgPreview src="../items/files/<?php echo($row["image"]); ?>" class="img img-responsive">
							<?php } ?>
						  </div>	
						  <div class="col-sm-8">
							   <br>
								  <span class="btn btn-success fileinput-button">
									  	<i class="glyphicon glyphicon-plus"></i>
									  <span>Select file...</span>
									  <!-- The file input field used as target for the file upload widget -->
							  <input id="flImage" type="file" name="files[]">
							  </span>
							  <br>
							  <br>
							  <!-- The global progress bar -->
								  <div id="progress" class="progress" style="display:none;">
							  <div class="progress-bar progress-bar-success"></div>
							  </div>
							  <!-- The container for the uploaded files -->
							  <div id="files" class="files"></div>

				  		</div>
			  			</div> <!--form group-->
						</div> <!--col-sm-6-->
							
						<div class="clearfix"></div><br>
					     <div class="col-sm-12">
						 <div class="input-group ">
							<div class="input-group-addon">Description</div>
							<textarea name=descrip id=descrip
								class="form-control" rows=10><?php echo($row["description"]);?></textarea>
						 </div>
						 </div>
						
						<div class="clearfix"></div><br>
						<div class="col-sm-5">
		 				 <div class="input-group">
							<div class="input-group-addon">Canteen Code*</div>
							<select name=ccode id=ccode
								class="form-control">
								<option value="">Select Code</option>
								<option value="GREEN" <?php if ($row["canteen_code"] == "GREEN") echo(" selected"); ?>>Green</option>
								<option value="AMBER" <?php if ($row["canteen_code"] == "AMBER") echo(" selected"); ?>>Amber</option>
								<option value="RED" <?php if ($row["canteen_code"] == "RED") echo(" selected"); ?>>Red</option>

							</select>
						 </div>
						 </div>
						
						
						  <div class="clearfix"></div><hr>
						  
						  <div class="col-sm-12">
						  	<div class="text-center"><b>Select one item from each category</b></div>
							<div class="clearfix"></div>
							<br>

							<?php
							   foreach($dropDowns as $dropdown) {
							?>
								<div class="col-sm-4 col-lg-3 col-md-3">
								   <b><?php echo($dropdown["category_name"]);?> </b><br>
								    <select name="cat<?php echo($dropdown["category_id"]);?>"
										id="cat<?php echo($dropdown["category_id"]);?>"
										class="form-control dropdown">
										   <option value="0">Select an Item</option>	
											<?php echo($dropdown["products"]);?>
									</select>
									<div class="clearfix"></div><br>
								</div>
							   
							 <?php } ?>
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
	<script src="../includes/jQuery-File-Upload-9.0.2/js/jquery.iframe-transport.js"></script>
	<script src="../includes/jQuery-File-Upload-9.0.2/js/jquery.fileupload.js"></script>	
		
	<script src="../includes/admin-meal-deal.js"></script>
	
</body>

</html>
