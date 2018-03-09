<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "edit-allergy";
	$pageTitle = "Jack & Jill Admin - Edit Allergy";
	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/settings.php");

	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);

	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$srow = $settings->getRowById("ID", 1);
	

	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
		$arrData = ["name"=>$_POST["name"]];

		if ($_POST["enabled"] == 1)
			$arrData["enabled"] = 1;
		else
			$arrData["enabled"]  = 0;


		$newId = $amaster->update($arrData, $_POST["xid"]);
		$error = $amaster->mError;
		if ($error != null && $error != "")
				exit("Error:" . $error);
		else {
				header("Location: allergy-master.php");
				exit();
		}
	}

	$id = $_GET["id"];
	if (!$id)
	  $id = 0;
	
	$row = $amaster->getRowById("ID", $id);

	
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
                    <h3 class="page-header">Edit Allergy</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
		
					<form name=frmAllergy id=frmAllergy method=post onsubmit="return xvalidate(this);">
						<input type=hidden name=xid id=xid value="<?php echo($id);?>">
						<div class="clearfix"></div><br>
						  <div class="col-sm-6">
						  <div class="input-group">
							<div class="input-group-addon">Allergy Name*</div>
							<input name=name id=name
								maxlength=100 class="form-control"
								 value="<?php echo($row["name"]);?>">
						  </div>
						  </div>
					
	 					 <div class="col-sm-6">
								<div class="checkbox">
								<label>
								<input id="enabled" name="enabled" value="1" type="checkbox" <?php if ($row["enabled"] == "1") echo(" checked"); ?>>Enabled
								</label>
							</div>

						 </div>

						<div class="clearfix"></div><br>

	
				
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
	
	<script src="../includes/admin-edit-allergy.js"></script>
	
</body>

</html>
