<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "allergies";
	$pageTitle = "Jack & Jill Admin - List Of Allergies";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/allergies-master.php");

	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	if ($_SESSION["admin_id"] != "1") {
		header("Location: index.php");
		exit();
	}

	define("MAXROWSPERPAGE", 20);
	define("MAXPAGELINKS", 10);
	
		// check for deletion
	$delId = $_GET["del"];
	if (is_numeric($delId)) {
		$amaster->delete($delId);
		header("Location: allergy-master.php");
		exit;
		
	}

	// get params

	$rowCount = $amaster->getCount();
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
		for($i = $startPoint; $i < $maxLinks; $i++) {
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

	$rows = $amaster->getList($nStartRec, MAXROWSPERPAGE, $sort);
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
                    <h3 class="page-header">List Of Allergies (<?php echo($rowCount);?>)</h3>
                </div>
            </div> <!--row-->

	 <div class="row">
                <div class="col-lg-12">
			<form name=frmMenu id=frmMenu>
				<input type=hidden name=p id=p value="<?php echo($_GET["p"]); ?>">

			</form>
					<div class="clearfix"></div><br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th class="col-sm-4">Allergies</th>
										<th class="col-sm-4">Enabled</th>
										<th class="col-sm-4">
						  					<a class="btn btn-default" id="lnkAdd">
											<i class="fa fa-plus"></i>
											</a>
										</th>
                                    </tr>
                                </thead>
                                <tbody>
				<?php 
				   foreach($rows as $row) {
				 ?>
                  <tr>
					<td>
							<?php echo($row["name"]); ?>
					</td>
					<td>
							<?php 
						       if ($row["enabled"] == 1) {
							?>
								<a id="toggle<?php echo($row["ID"]);?>" href="#" onclick="toggle(<?php echo($row["ID"]);?>, 0); return false;"><i class="fa fa-check-square fg-green"></i></a>
							<?php } else { ?>
							   	<a id="toggle<?php echo($row["ID"]);?>" href="#" onclick="toggle(<?php echo($row["ID"]);?>, 1); return false;"><i class="fa fa-minus-square fg-red"></i></a>

							<?php } ?>
					</td>

                     <td class="center">
						<a class="btn btn-sm btn-default" href="edit-allergy.php?id=<?php echo($row["ID"]);?>">
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
			</div> <!--panel-body-->
		</div> <!--panel-->	
	</div> <!--row-->	
	</div> <!--page wrapper-->


<div id="loader" class="full-loading-bg" style="text-align:center; display:none;">
	<img src="../images/ajax-loader-large.gif">
</div>

	</div> <!--wrapper-->
  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	<script src="../includes/admin-allergy-master.js"></script>
	

</body>

</html>
