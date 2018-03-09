<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "schools";
	$pageTitle = "Jack & Jill Admin - List Of Schools";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/offdays.php");
	require_once($g_docRoot . "classes/school-items.php");
	require_once($g_docRoot . "classes/students.php");

	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$offdays = new OffDays($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schoolItems = new SchoolItems($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


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

		$checkCount = $schoolItems->getCountForASchool($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("School has disabled items. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }


		$checkCount = $offdays->getCountForASchool($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("School has off-days. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }

		$checkCount = $classes->getCountForASchool($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("School has classes. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }


		$checkCount = $students->getCountForASchool($delId);
		if ($checkCount > 0){ ?>
			<script>
				alert("School has students. Cannot be deleted");
				window.history.back();
			</script>
		<?php
		  exit;
		 }
	 
	
		$schools->delete($delId);
		header("Location: schools.php");
		exit;
		
	}

	// get params
	$name = $_GET["xname"];
	$sort = $_GET["sort"];

	$rowCount = $schools->getCount($name);
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

	$rows = $schools->getList($name, $nStartRec, MAXROWSPERPAGE, $sort);
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
                    <h3 class="page-header">List Of Schools (<?php echo($rowCount);?>)</h3>
                </div>
            </div> <!--row-->

	 <div class="row">
                <div class="col-lg-12">
			<form name=frmMenu id=frmMenu>
				<input type=hidden name=p id=p value="<?php echo($_GET["p"]); ?>">

					<div class="col-sm-3">
					  <input name=xname id=xname class="form-control" maxlength=50
						placeholder="School Name" value="<?php echo($name);?>">
					</div>
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
<?php
   } else { 
?>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th class="col-sm-2">Image</th>
										<th class="col-sm-6">School</th>
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
				     if ($row["image"] == null || $row["image"] == "")
					 	$image = $g_webRoot . "images/school.jpg";
					 else
					 	$image = $g_webRoot . "schools/files/" . $row["image"];
				 ?>
                  <tr>
				    <td>
						<img src="<?php echo($image); ?>"
						   class="img img-responsive">
					</td>
					<td>
							<?php echo($row["name"] . "<br><small>" . $row["address"] . "<br>" . $row["city"] . "</small>"); ?>
					</td>
                     <td class="center">
						<a class="btn btn-sm btn-default" href="edit-school.php?id=<?php echo($row["ID"]);?>">
							<i class="fa fa-edit"></i>
						</a>&nbsp;
						<a class="btn btn-sm btn-default" href="#" onclick="doPrint('<?php echo($row["ID"]);?>'); return false;">
							<i class="fa fa-print"></i>
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
<?php
 } 
?>
			</div> <!--panel-body-->
		</div> <!--panel-->	
	</div> <!--row-->	
	</div> <!--page wrapper-->


	</div> <!--wrapper-->

  <!-- print  Modal -->
  <div id="print-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>School Menu Printing</h4>
			 <div class="col-sm-12 text-center">
			 	<a href="#" id="lnkPrint" target=_blank">Click To Download/Print</a>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>
	
  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	<?php
		echo("<script> var webRoot = '" . $g_webRoot . "'; </script>");
	?>
	<script src="../includes/admin-schools.js"></script>
	

</body>

</html>
