<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "edit-school";
	$pageTitle = "Jack & Jill Admin - Edit School";
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/school-items.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/products.php");
	

	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);



	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$srow = $settings->getRowById("ID", 1);
	

	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
		$arrData = ["name"=>$_POST["name"], "address"=>$_POST["address"], "city"=>$_POST["city"],
				    "description"=>$_POST["descrip"], "image"=>$_POST["file_image"]];

		if ($_POST["sun"] == 1)
			$arrData["work_sun"] = 1;
		else
			$arrData["work_sun"]  = 0;

		if ($_POST["mon"] == 1)
			$arrData["work_mon"] = 1;
		else
			$arrData["work_mon"]  = 0;

		if ($_POST["tue"] == 1)
			$arrData["work_tue"] = 1;
		else
			$arrData["work_tue"]  = 0;

		if ($_POST["wed"] == 1)
			$arrData["work_wed"] = 1;
		else
			$arrData["work_wed"]  = 0;


		if ($_POST["thu"] == 1)
			$arrData["work_thu"] = 1;
		else
			$arrData["work_thu"]  = 0;
	
		if ($_POST["fri"] == 1)
			$arrData["work_fri"] = 1;
		else
			$arrData["work_fri"]  = 0;

		if ($_POST["sat"] == 1)
			$arrData["work_sat"] = 1;
		else
			$arrData["work_sat"]  = 0;


		if ($_POST["xid"] == 0)
			$arrData["date_added"] = date("Y-m-d H:i:s");

		$newId = $schools->update($arrData, $_POST["xid"]);
		$error = $schools->mError;
		if ($error != null && $error != "")
				exit("Error:" . $error);
		else {
			if ($_POST["xid"] > 0) {
					header("Location: schools.php");
					exit();
			} else  {
				header("Location: edit-school.php?id=" . $newId);
				exit;
			}
		}
	}

	$id = $_GET["id"];
	if (!$id)
	  $id = 0;
	
	$row = $schools->getRowById("ID", $id);

	// get products list
	$productsCount = $products->getCount();
	$prows = $products->getList(null, null, null, null, null, null, 0, $productsCount, "name_asc");

	$pList = "<option value=0>Select Item</option>";
	foreach($prows as $prow) {
		$pList .= "<option value=" . $prow["ID"] . ">" . $prow["name"] . "</option>";
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
<link href="<?php echo($g_webRoot);?>css/bootstrap-datepicker3.min.css" rel="stylesheet">

</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Edit School</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
		
					<form name=frmSchool id=frmSchool method=post onsubmit="return xvalidate(this);">
						<input type=hidden name=xid id=xid value="<?php echo($id);?>">
						<input type=hidden name=file_image id=file_image value="<?php echo($row["image"]);?>">
						<div class="clearfix"></div><br>
						<div class="col-sm-6">
						  <div class="col-sm-12">
						  <div class="input-group">
							<div class="input-group-addon">School Name*</div>
							<input name=name id=name
								maxlength=100 class="form-control"
								 value="<?php echo($row["name"]);?>">
						  </div>
						  </div>
					
						<div class="clearfix"></div><br>
						
	 					 <div class="col-sm-12">
		 				 <div class="input-group">
							<div class="input-group-addon">Address*</div>
							<input name=address id=address
								maxlength=255 class="form-control"
								 value="<?php echo($row["address"]);?>">
						 </div>
						 </div>
						<div class="clearfix"></div><br>

	 					 <div class="col-sm-12">
		 				 <div class="input-group">
							<div class="input-group-addon">City*</div>
							<input name=city id=city
								maxlength=50 class="form-control"
								 value="<?php echo($row["city"]);?>">
						 </div>
						 </div>
						</div> <!--col-sm-6-->

						<div class="well col-sm-6" style="max-height:300px;overflow-y:auto;">
						<?php if ($row["ID"] > 0) { ?>
							<table class="table table-bordered table-striped bg-white" id="tblItems">
							<thead>
			    				<tr>
								<th class="col-sm-6">Items</th>
								<th class="col-sm-2">
							       Click to deactivate
								</th>
			    				</tr>
							</thead>
							<tbody>
			    			</tbody>
						    </table>
						  
						  <div class="clearfix"></div>
					      <?php } else { ?>
					         <div class="text-center">Items can be added once school has been saved</div>
						  <?php } ?>
						</div> <!--well-->
				

						<div class="clearfix"></div><br>

			 	 <div class="form-group" id="groupItem">
				  <div class="col-sm-4">
				  	<label for="flImage" class="control-label">Image* (Max 2 Mb)</label>
					<div class="clearfix"></div><br>
					<?php if ($row["image"] == null || $row["image"] == "") { ?>
						<img id="imgPreview" name=imgPreview style="display:none;" class="img img-responsive">
					<?php } else { ?>
						<img id="imgPreview" name=imgPreview src="../schools/files/<?php echo($row["image"]); ?>" class="img img-responsive">
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
	
						 <div class="col-sm-6">
						 <div class="input-group ">
							<div class="input-group-addon">Description*</div>
							<textarea name=descrip id=descrip
								class="form-control" rows=15><?php echo($row["description"]);?></textarea>
						 </div>
						 </div>
				
						<div class="well col-sm-6" style="max-height:300px;overflow-y:auto;">
						<?php if ($row["ID"] > 0) { ?>
							<table class="table table-bordered table-striped bg-white" id="tblClasses">
							<thead>
			    				<tr>
								<th class="col-sm-6">Class</th>
								<th class="col-sm-4">Enabled</small></th>
								<th class="col-sm-2">
										<button type="button" id="btnAddClass" class="btn btn-default">
										<i class="fa fa-plus"></i>
									</button>

								</th>
			    				</tr>
							</thead>
							<tbody>
			    			</tbody>
						    </table>
						  
						  <div class="clearfix"></div>
					      <?php } else { ?>
					         <div class="text-center">Classes can be added once school has been saved</div>
						  <?php } ?>
						</div> <!--well-->

					
						<div class="clearfix"></div><br>

	 					<div class="col-sm-6">
						  <b>Choose Working Days </b>

						    <div class="clearfix"></div><br>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="mon" name="mon" value="1" type="checkbox" <?php if ($row["work_mon"] == "1") echo(" checked"); ?>>Mon
								</label>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="tue" name="tue" value="1" type="checkbox" <?php if ($row["work_tue"] == "1") echo(" checked"); ?>>Tue
								</label>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="wed" name="wed" value="1" type="checkbox" <?php if ($row["work_wed"] == "1") echo(" checked"); ?>>Wed
								</label>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="thu" name="thu" value="1" type="checkbox" <?php if ($row["work_thu"] == "1") echo(" checked"); ?>>Thu
								</label>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="fri" name="fri" value="1" type="checkbox" <?php if ($row["work_fri"] == "1") echo(" checked"); ?>>Fri
								</label>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="sat" name="sat" value="1" type="checkbox" <?php if ($row["work_sat"] == "1") echo(" checked"); ?>>Sat
								</label>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="checkbox">
								<label>
								<input id="sun" name="sun" value="1" type="checkbox" <?php if ($row["work_sun"] == "1") echo(" checked"); ?>>Sun
								</label>
							</div>
						</div>
						
						</div> <!--col-sm-6-->

						<div class="well col-sm-6" style="max-height:300px;overflow-y:auto;">
						<?php if ($row["ID"] > 0) { ?>
							<table class="table table-bordered table-striped bg-white" id="tblOffDays">
							<thead>
			    				<tr>
								<th class="col-sm-6">Reason</th>
								<th class="col-sm-4">Date</small></th>
								<th class="col-sm-2">
										<button type="button" id="btnAddOffDays" class="btn btn-default">
										<i class="fa fa-plus"></i>
									</button>

								</th>
			    				</tr>
							</thead>
							<tbody>
			    			</tbody>
						    </table>
						  
						  <div class="clearfix"></div>
					      <?php } else { ?>
					         <div class="text-center">Off days can be added once school has been saved</div>
						  <?php } ?>
						</div> <!--well-->
				
						
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
  
   <div class="modal bounceIn animated in" id="class-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white" >Class Entry</div>
		  </div>
		  <div class="modal-body">
			<form name=frmClass id=frmClass onsubmit="return false;">
				<input type=hidden id="xxid" name="xxid" value=0>
				 <div class="col-sm-8">
				 <div class="input-group ">
					<div class="input-group-addon">Class</div>
					  <input name="sclass" id="sclass" class="form-control" maxlength=25>
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
			<button type="button" id="btnCSave" class="btn btn-sm btn-success" >Save</button>

		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>


  <div class="modal bounceIn animated in" id="offdays-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white" >Off Day Entry</div>
		  </div>
		  <div class="modal-body">
			<form name=frmOD id=frmOD onsubmit="return false;">
				<input type=hidden id="xxxid" name="xxxid" value=0>
				 <div class="col-sm-8">
				 <div class="input-group ">
					<div class="input-group-addon">Reason</div>
					  <input name="reason" id="reason" class="form-control" maxlength=50>
				 </div>
				 </div>

		 		 <div class="col-sm-4">
				   <div class="form-group datef" id="divDate">
                      <input data-provide="datepicker" class="datepicker1 form-control"
					  placeholder="Select Date " id="date" name="date">
                   </div>
                                                                  

				 </div>
				
			</form>
		    <div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-sm" data-dismiss="modal">Cancel</button>
			<button type="button" id="btnODSave" class="btn btn-sm btn-success" >Save</button>

		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>

  <div class="modal bounceIn animated in" id="items-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header bg-black">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title fg-white" >Unavailable Item</div>
		  </div>
		  <div class="modal-body">
			<form name=frmI id=frmI onsubmit="return false;">
				<input type=hidden id="xxxxid" name="xxxxid" value=0>
				 <div class="col-sm-8">
				 <div class="input-group ">
					<div class="input-group-addon">Menu Item</div>
						<select class="form-control" id="item" name="item">
							<?php echo($pList); ?>	
						</select>
				 </div>
				 </div>

				 <div class="clearfix"></div>
			</form>
		    <div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-sm" data-dismiss="modal">Cancel</button>
			<button type="button" id="btnISave" class="btn btn-sm btn-success" >Save</button>

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
 	<script src="../js/bootstrap-datepicker.min.js"></script>	
	<script src="../includes/admin-edit-school.js"></script>
	
</body>

</html>
