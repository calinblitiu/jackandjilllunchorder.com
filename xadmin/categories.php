<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	$pageName = "categories";
	$pageTitle = "Jack & Jill Admin - Menu Items";

	require_once("../includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/categories.php");

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	if ($_SESSION["admin_id"] != "1") {
		header("Location: index.php");
		exit();
	}

	define("MAXROWSPERPAGE", 20);
	define("MAXPAGELINKS", 10);
	
		// check for deletion
	$delId = $_GET["del"];
	if (is_numeric($delId)) {
		$cats->delete($delId);
		header("Location: categories.php");
		exit;
		
	}

	// get params

	$rowCount = $cats->getCount();
	if ($sort == null || $sort == "")
		$sort = "name asc";
        	
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

	$rows = $cats->getList( $nStartRec, MAXROWSPERPAGE, $sort);
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
                    <h3 class="page-header">Menu Categories (<?php echo($rowCount);?>)</h3>
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
                                        <!--<th class="col-sm-1">
						<div class="checkbox">
							<label>
							<input id="ckAll" name="ckAll" type="checkbox"> 
							</label>
						</div>
					
					</th>-->
                                        <th class="col-sm-3">Name</th>
                                        <th class="col-sm-3">Items</th>
                                        <th class="col-sm-3">
					  	<a class="btn btn-default" id="lnkAdd">
							<i class="fa fa-plus"></i>
						</a>
						&nbsp;
						<!--<a id="lnkDelete" class="btn btn-default pull-right">
							<i class="fa fa-trash"></i> </a>&nbsp;-->
					</th>
                                    </tr>
                                </thead>
                                <tbody>
				<?php 
				   foreach($rows as $row) {
				     $menuIds = $row["items"];
					 if ($menuIds == null || $menuIds == "")
					   $arrIds = null;
					 else {
						 $arrIds = explode(",", $menuIds);
						 $arr2 = [];
						 // validate that the menu ids are still in db
						 foreach($arrIds as $a) {
							 $checkRow = $products->getRowById("ID", $a);
							 if ($checkRow && $checkRow["ID"] == $a) {
							    $arr2[] = $a;
							 }
						 }
						 $arrIds = $arr2;
				     }
				 ?>
                                    <tr>
				      <?php if (false) { ?>
                                        <td>
						<div class="checkbox">
							<label>
							<input id="ck<?php echo($row["ID"]);?>" class="checkall" type="checkbox" value=1> 
							</label>
						</div>

					</td>
				     <?php } ?>
                       <td>
						<?php echo($row["name"]);?>
					</td>
                      <td class="text-right"><?php echo(count($arrIds) . " items"); ?></td>
                      <td class="center">
						<a class="btn btn-sm btn-default" href="edit-category.php?id=<?php echo($row["ID"]);?>">
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


	</div> <!--wrapper-->
  
	<?php require_once($g_docRoot . "components/error-popup.php"); ?>
	<?php require_once($g_docRoot . "components/admin-scripts.php"); ?>

	<script src="../includes/admin-categories.js"></script>
	

</body>

</html>
