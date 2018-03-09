<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "edit-product";
	$pageTitle = "Jack & Jill Admin - Edit Product";
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/allergies-master.php");

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$srow = $settings->getRowById("ID", 1);
	
	// get enabled allergy list
	$arows = $amaster->getEnabledList(0,500);
	$allergyHTML = "";
	foreach($arows as $arow) {
		$allergyHTML .= "<option value=\"" . $arow["name"] . "\">" . $arow["name"] . "</option>";
	}

	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
		// validate for duplicate name
		$checkRow = $products->getRowByName($_POST["name"]);
		if ($checkRow && $checkRow["ID"] != $_POST["xid"]) {
			$dupError = true;
		} else {
		
			$arrData = ["name"=>$_POST["name"], "price"=>$_POST["price"], "description"=>$_POST["descrip"],
			"food_type"=>$_POST["ftype"], "image"=>$_POST["file_image"], "canteen_code"=>$_POST["ccode"]];
			if ($_POST["ckRecess"] == 1)
				$arrData["flag_recess"] = 1;
			else
				$arrData["flag_recess"]  = 0;
			if ($_POST["ckLunch"] == 1)
				$arrData["flag_lunch"] = 1;
			else
				$arrData["flag_lunch"]  = 0;
			if ($_POST["ckGlobal"] == 1)
				$arrData["flag_global"] = 1;
			else
				$arrData["flag_global"]  = 0;

			if ($_POST["xid"] == 0)
				$arrData["date_added"] = date("Y-m-d H:i:s");

			$newId = $products->update($arrData, $_POST["xid"]);
			$error = $products->mError;
			if ($error != null && $error != "")
				exit("Error:" . $error);
			else {	
				if ($_POST["xid"] > 0) {
					header("Location: menu.php");
					exit();
				}
				else {
					header("Location: edit-product.php?id=" . $newId);
					exit;
				}
			}
		}
	}

	$id = $_GET["id"];
	if (!$id)
	  $id = 0;
	
	$row = $products->getRowById("ID", $id);

	
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
<style>

</style>
</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><?php if ($id == 0) echo("Add"); else echo("Edit");?> Menu Item</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
				 <!-- Nav tabs -->
				  <ul class="nav nav-tabs" role="tablist">
				    <li role="presentation" class="active"><a href="#details" aria-controls="details" role="tab" data-toggle="tab">Details</a></li>
				    <li role="presentation"><a href="#nutrition" aria-controls="nutrition" role="tab" data-toggle="tab">Nutrition</a></li>
				    <li role="presentation"><a href="#allergies" aria-controls="allergies" role="tab" data-toggle="tab">Allergic Info</a></li>
				    <li role="presentation"><a href="#ingredients" aria-controls="ingredients" role="tab" data-toggle="tab">Ingredients</a></li>
  </ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="details"> 
	    <?php if ($dupError) { ?>
							   <script>
							   	  alert("This menu item already exists");
								  window.history.back();
							   </script>
		<?php exit(); } ?>
		<form name=frmProd id=frmProd method=post onsubmit="return xvalidate(this);">
						<input type=hidden name=xid id=xid value="<?php echo($id);?>">
						<input type=hidden name=file_image id=file_image value="<?php echo($row["image"]);?>">
						<div class="clearfix"></div><br>
						  <div class="col-sm-8">
						  <div class="input-group">
							<div class="input-group-addon">Item Name*</div>
							<input name=name id=name
								maxlength=100 class="form-control"
								 value="<?php echo($row["name"]);?>">
						  </div>
						  </div>

	 					 <div class="col-sm-4">
		 				 <div class="input-group">
							<div class="input-group-addon">Price*</div>
							<input name=price id=price
								maxlength=10 class="form-control"
								type="currency"
								 value="<?php echo($row["price"]);?>">
						 </div>
						 </div>

						<div class="clearfix"></div><br>

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
			  	</div>
				<div class="clearfix"></div><Br>
	
						 <div class="col-sm-12">
						 <div class="input-group ">
							<div class="input-group-addon">Description*</div>
							<textarea name=descrip id=descrip
								class="form-control" rows=10><?php echo($row["description"]);?></textarea>
						 </div>
						 </div>
				
					
						<div class="clearfix"></div><br>

	 					<div class="col-sm-3">
		 				 <div class="input-group">
							<div class="input-group-addon">Type*</div>
							<select name=ftype id=ftype
								class="form-control">
								<option value="">Select</option>
								<option value="HOT" <?php if ($row["food_type"] == "HOT") echo(" selected"); ?>>Hot</option>
								<option value="COLD" <?php if ($row["food_type"] == "COLD") echo(" selected"); ?>>Cold</option>
							</select>
						 </div>
						 </div>

						<div class="col-sm-2">
							<div class="checkbox">
								<label>
								<input id="ckRecess" name="ckRecess" value="1" type="checkbox" <?php if ($row["flag_recess"] == "1") echo(" checked"); ?>>Recess
								</label>
							</div>
						</div>
	
						<div class="col-sm-2">
							<div class="checkbox">
								<label>
								<input id="ckLunch" name="ckLunch" value="1" type="checkbox"  <?php if ($row["flag_lunch"] == "1") echo(" checked"); ?>>Lunch
								</label>
							</div>
						</div>

					
						<div class="col-sm-2">
							<div class="checkbox">
								<label>
								<input id="ckGlobal" name="ckGlobal" value="1" type="checkbox"  <?php if ($row["flag_global"] == "1") echo(" checked"); ?>>Global Menu
								</label>
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

						<div class="col-sm-8"></div>
						<div class="col-sm-4 text-right">
							<button type="button" class="btn btn-danger" id="btnCancel">Cancel</button>
							&nbsp;&nbsp;
							<button type="submit" class="btn btn-success" id="btnSubmit">Save</button>

						</div>
					</form>

    </div> <!--tab details -->

    <div role="tabpanel" class="tab-pane" id="nutrition"> 

	<?php if ($id == 0) { ?>
		<h4><center>This can be edited once the item is created</h4>
	<?php } else  { ?>
		<div class="col-sm-12">
		<br>
		  <b>Nutrition Items (<span id="ntotal">0</span>)</b>
		&nbsp;&nbsp;<a href="#" id="lnkNewNutrition" class="btn btn-default btn-small"><i class="fa fa-plus"></i></a>
		  <div class="clearfix"></div><br>
		  <table class="table table-bordered table-striped" id="tblNutrition">
			<thead>
			    <tr>
				<th class="col-sm-1">Crop Type</th>
				<th class="col-sm-1">Yield<small> (kb/ha-crop</small></th>
				<th class="col-sm-1">%</th>
				<th class="col-sm-1">Adjusted <small>(kb/ha-crop)</small></th>
				<th class="col-sm-1">Protein%</th>
				<th class="col-sm-1">Fat%</th>
				<th class="col-sm-1">Carbs%</th>
				<th class="col-sm-1">Fiber%</th>
				<th class="col-sm-1">Time%</th>
				<th class="col-sm-1">Protein<small> (kb/ha-year)</small></th>
			    </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>	
	<?php  } ?>
    </div> <!--tab nutrition -->


    <div role="tabpanel" class="tab-pane" id="allergies">
	<?php if ($id == 0) { ?>
		<h4><center>This can be edited once the item is created</h4>
	<?php } else  { ?>
		<div class="col-sm-8">
		<br>
		  <b>Allergies (<span id="atotal">0</span>)</b>
		&nbsp;&nbsp;<a href="#" id="lnkNewAllergy" class="btn btn-default btn-small"><i class="fa fa-plus"></i></a>
		  <div class="clearfix"></div><br>
		  <table class="table table-bordered table-striped" id="tblAllergies">
			<thead>
			    <tr>
				<th class="col-sm-8">Allergy</th>
				<th class="col-sm-4">Status</th>
			    </tr>
			</thead>
			<tbody>
			</tbody>
		  </table>
		</div>	
	<?php  } ?>

    </div> <!--tab allergies -->

    <div role="tabpanel" class="tab-pane" id="ingredients">
	<?php if ($id == 0) { ?>
		<h4><center>This can be edited once the item is created</h4>
	<?php } else  { ?>
	<form name=frmI id=frmI onsubmit="return false;">
		<br>
		Enter ingredient details below:
		<div class="clearfix"></div><br>
		<textarea class="form-control" rows="15" id="fingredients" name="fingredients"></textarea>
		<div class="clearfix"></div><hr>
		
		<div class="col-sm-8"></div>
		<div class="col-sm-4 text-right">
				<button type="button" class="btn btn-danger" id="btnCancel4">Cancel</button>
				&nbsp;&nbsp;
				<button type="button" class="btn btn-success" id="btnSubmit4">Save</button>
		</div>

	</form>
	<?php } ?>
    </div> <!--tab ingredients -->
  </div>

			
					</div> <!--panel-body-->

				</div> <!--panel-default-->

			</div> <!--row-->
		</div> <!--page wrapper-->




	</div> <!--wrapper-->
  

 <div class="modal bounceIn animated in" id="nutri-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white" >Nutrition Entry</div>
		  </div>
		  <div class="modal-body">
			<form name=frmNutri id=frmNutri onsubmit="return false;">
				<input type=hidden id="xxid" name="xxid" value=0>
				 <div class="col-sm-10">
				 <div class="input-group ">
					<div class="input-group-addon">Crop Type</div>
						<input name=crop_type id=crop_type										maxlength=50 class="form-control"
						 >
				 </div>
				 </div>
				 <div class="clearfix"></div><br>

		 		 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Yield</div>
						<input name=yield id=yield												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
				 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">%</div>
						<input name=percent id=percent												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
			
				 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Adjusted</div>
						<input name=adjusted id=adjusted												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
			
				 <div class="clearfix"></div><br>

		 		 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Protein %</div>
						<input name=protein id=protein												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
				 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Fat %</div>
						<input name=fat id=fat													maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
			
				 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Carbs %</div>
						<input name=carbs id=carbs												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
			
				 <div class="clearfix"></div><br>

			 	 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Fiber %</div>
						<input name=fiber id=fiber												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
				 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Time %</div>
						<input name=xtime id=xtime											maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
			
				 <div class="col-sm-4">
				 <div class="input-group ">
					<div class="input-group-addon">Protein</div>
						<input name=proteiny id=proteiny												maxlength=20 class="form-control"
						 >
				 </div>
				 </div>
			
				 <div class="clearfix"></div><br>

	
				
			</form>
		    <div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-sm" data-dismiss="modal">Cancel</button>
			<button type="button" id="btnNSave" class="btn btn-sm btn-success" >Save</button>

		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>




 <div class="modal bounceIn animated in" id="allergy-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white" >Allergy Entry</div>
		  </div>
		  <div class="modal-body">
			<form name=frmAllergy id=frmAllergy onsubmit="return false;">
				<input type=hidden id="xxxid" name="xxxid" value=0>
				 <div class="col-sm-8">
				 <div class="input-group ">
					<div class="input-group-addon">Allergy</div>
					  <select name="cboAllergy" id="cboAllergy" class="form-control">
					     <?php echo($allergyHTML); ?>
				         </select>
				 </div>
				 </div>

		 		 <div class="col-sm-4">
						<div class="checkbox">
							<label>
							<input id="flag" name="flag" value="1" type="checkbox">Yes
							</label>
						</div>
				 </div>
				
			</form>
		    <div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-sm" data-dismiss="modal">Cancel</button>
			<button type="button" id="btnASave" class="btn btn-sm btn-success" >Save</button>

		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>


<div id="loader" class="full-loading-bg" style="text-align:center; display:none;">
	<img src="../images/ajax-loader-large.gif">
</div>
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>
	<script src="../includes/jquery-ui/jquery-ui.js"></script>
	<script src="../includes/jQuery-File-Upload-9.0.2/js/jquery.iframe-transport.js"></script>
	<script src="../includes/jQuery-File-Upload-9.0.2/js/jquery.fileupload.js"></script>	
	
	<script src="../includes/admin-edit-product.js"></script>
	
</body>

</html>
