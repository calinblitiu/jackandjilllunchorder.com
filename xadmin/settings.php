<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "settings";
	$pageTitle = "Jack & Jill Admin - System Settings";
	require_once("../includes/globals.php");	
	
	require_once($g_docRoot . "classes/settings.php");
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	if ($_SESSION["admin_id"] != "1") {
		exit("logged out");
		header("Location: index.php");
		exit();
	}



	// check if form was submitted
	if ($_POST) {
		$arrData = ["from_emailid"=>$_POST["from_emailid"], "from_name"=>$_POST["from_name"],
				 "smtp_server"=>$_POST["smtp_server"],
			    "smtp_uid"=>$_POST["smtp_uid"], "smtp_pwd"=>$_POST["smtp_pwd"],
			    "sms_api_userid"=>$_POST["sms_uid"], "sms_api_pwd"=>$_POST["sms_pwd"],
			    "recess_deliv_time"=>$_POST["recess_deliv_time"], 
				"lunch_deliv_time"=>$_POST["lunch_deliv_time"],
				"eway_sandbox_flag"=>$_POST["eway_sandbox_flag"]];	
		$settings->update($arrData,1 );
		$error = $settings->mError;
		if ($error != null && $error != "")
				exit("Error:" . $error);
		else {
			header("Location: dashboard.php");
			exit();
		}
	}
	$row = $settings->getRowById("ID", 1);

	// time slots
	$rtimes = "";
	$ltimes = "";
	for($i = 0; $i <= 24; $i++) {
	   $hrs = "";
	   if ($i < 10)
		$hrs .= "0";
	   $hrs .= $i;

	   for($j=0; $j < 60; $j = $j + 5) {
	       $mins = ":";
	       if ($j < 10)
		 $mins .= "0";
	       $mins .= $j;
		
	       $rtimes .= "<option value= \"" . $hrs . $mins . "\"";
	       if ($row["recess_deliv_time"] == $hrs . $mins)
		  $rtimes .= " selected ";
	       $rtimes .=  ">" . $hrs . $mins . "</option>";

	       $ltimes .= "<option value= \"" . $hrs . $mins . "\"";
	       if ($row["lunch_deliv_time"] == $hrs . $mins)
		  $ltimes .= " selected ";
	       $ltimes .=  ">" . $hrs . $mins . "</option>";

	    }
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
</head>

<body>
   <div id="wrapper">
		<?php require_once($g_docRoot . "components/admin-header.php");?>
		
  		<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">System Settings</h3>
                </div>
                <!-- /.col-lg-12 -->
         	</div> <!--row-->

			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
					</div>
					<div class="panel-body">
					
					<form name=frmSettings id=frmSettings method=post onsubmit="return xvalidate(this);">
						<b>Mail</b>
						<div class="clearfix"></div><br>

	 					 <div class="col-sm-6">
						  <div class="input-group">
							<div class="input-group-addon">Sending Name</div>
							<input name=from_name id=from_name
								maxlength=70 class="form-control"
								 value="<?php echo($row["from_name"]);?>">
						  </div>
						  </div>
							
						 <div class="col-sm-6">
						 <div class="input-group ">
							<div class="input-group-addon">Sending Email Id</div>
							<input name=from_emailid id=from_emailid										maxlength=70 class="form-control"
								 value="<?php echo($row["from_emailid"]);?>">
						 </div>
						 </div>

						 <div class="clearfix"></div><br>
						  <div class="col-sm-4">
						  <div class="input-group">
							<div class="input-group-addon">SMTP Server</div>
							<input name=smtp_server id=smtp_server 
								maxlength=255 class="form-control"
								 value="<?php echo($row["smtp_server"]);?>">
						  </div>
						  </div>
							
						 <div class="col-sm-4">
						 <div class="input-group ">
							<div class="input-group-addon">SMTP UserId</div>
							<input name=smtp_uid id=smtp_uid
								maxlength=70 class="form-control"
								 value="<?php echo($row["smtp_uid"]);?>">
						 </div>
						 </div>

						 <div class="col-sm-4">
		 				 <div class="input-group">
							<div class="input-group-addon">SMTP Password</div>
							<input name=smtp_pwd id=smtp_pwd
								maxlength=50 class="form-control"
								 value="<?php echo($row["smtp_pwd"]);?>">
						 </div>
						 </div>

						<div class="clearfix"></div><br>

		 				<b>SMS API</b>
					        <div class="clearfix"></div><br>
	
						<div class="col-sm-6">
		 				 <div class="input-group">
							<div class="input-group-addon">UserId</div>
							<input name=sms_uid id=sms_uid
								maxlength=25 class="form-control"
								 value="<?php echo($row["sms_api_userid"]);?>">
						 </div>
						 </div>

						<div class="col-sm-6">
		 				 <div class="input-group">
							<div class="input-group-addon">Password</div>
							<input name=sms_pwd id=sms_pwd
								maxlength=25 class="form-control"
								 value="<?php echo($row["sms_api_pwd"]);?>">
						 </div>
						 </div>


						<div class="clearfix"></div><br>

		 				<b>Application Configuration</b>
					        <div class="clearfix"></div><br>

						<div class="col-sm-6">
		 				 <div class="input-group">
							<div class="input-group-addon">Recess Delivery Time</div>
							<select name=recess_deliv_time id=recess_deliv_time
								 class="form-control">
								<option value="">Select Time</option>
								<?php echo($rtimes); ?>
							</select>
						 </div>
						 </div>
				
						<div class="col-sm-6">
		 				 <div class="input-group">
							<div class="input-group-addon">Lunch Delivery Time</div>
							<select name=lunch_deliv_time id=lunch_deliv_time
								class="form-control">
								<option value="">Select Time</option>
								<?php echo($ltimes); ?>

							</select>
						 </div>
						 </div>

						<div class="clearfix"></div><br>

		 				<b>Eway Gateway</b>
					       <div class="clearfix"></div><br>

						<div class="col-sm-6">
		 				 <div class="input-group">
							<div class="input-group-addon">Gateway Mode</div>
							<select name=eway_sandbox_flag id=eway_sandbox_flag
								class="form-control">
								<option value="0" <?php if ($row["eway_sandbox_flag"] != 1) echo(" selected"); ?>>Use Live Gateway</option>
								<option value="1" <?php if ($row["eway_sandbox_flag"] == 1) echo(" selected"); ?>>Use Test Sandbox</option>

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
					</div> <!--panel-body-->

				</div> <!--panel-default-->

			</div> <!--row-->
		</div> <!--page wrapper-->




	</div> <!--wrapper-->
  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	<script src="../includes/admin-settings.js"></script>
	
</body>

</html>
