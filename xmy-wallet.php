<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "my-wallet";
	$pageTitle = "Jack & Jill - My Wallet";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/orders.php");
	require_once($g_docRoot . "classes/settings.php");
	require_once($g_docRoot . "classes/subscription-wallet-payments.php");
	
	$userId = $_SESSION["user_id"];
	if ($userId == null) {
		header("Location: sign-in");
		exit;
	}
		
	$members = new Members($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$orders = new Orders($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$spayments = new SubsWalletPayments($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$settings = new Settings($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);


	$srow = $settings->getRowById("ID", 1);
	// check if user has already got eway token
	$mrow = $members->getRowById("ID", $userId);
	$tokenId = $mrow["eway_token_id"];
	
	$totalRow = $credits->getTotalCreditsForMember($userId);
	$totalCredits = 0;
	if ($totalRow)
		$totalCredits = $totalRow["total"];
		
	// get total payments
	$totalDebits = 0;
	$ordersRow = $orders->getTotalPurchasesForMember($userId);
	if ($ordersRow)
		$totalDebits = $ordersRow["total"];

	// get total subscription payments
	$totalSDebits = 0;
	$subsRow = $spayments->getTotalForMember($userId);
	if ($subsRow)
		$totalSDebits = $subsRow["total"];


	$_SESSION["wallet_balance"] = $totalCredits - ($totalDebits + $totalSDebits);

	// get all rows for member
	$rowCount = $credits->getCountForMember($userId);
	$rows = $credits->getRowsForMember($userId, 0, $rowCount);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>

<style>

.green_button {
	color: #fff;
	background-color: #5EBE00;
	border: solid 2px transparent;
	width: 166px;
	height: 46px;
	font-size: 18px;
	line-height: 46px;
	font-family: 'Bubblegum Sans', cursive;
	border-radius: 35px;
	margin:35px 0 10px 0;
	transition: all 0.4s ease;
	padding: 0;
}
.green_button:hover {
    background: none;
    border-color: #5EBE00;
    color: #5EBE00;
}
</style>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
   
    <section class="my_profilepg">
        <div class="container">
        			<div id="horizontalTab">
					<?php require_once($g_docRoot . "components/account-menu.php"); ?>
                            <div class="resp-tabs-container">
                                    
               
                                     <div>
                                    				<div class="tab_tittle walet_blanc">
                                                                    <h2>My Wallet</h2>
                                                                    <span>Your Wallet balance is <a href="#"> $<?php echo(number_format($totalCredits-($totalDebits + $totalSDebits),2));?></a></span> </div>
            
                                    				
                                                    <div class="addstu_form my_wallet">
                                                    <h4>Recharge your wallet</h4>
                                            			<form>
                                                                
                                                                   <div class="form-group">
                                                                    <!--<input  class="form-control" id="" placeholder="Enter amount to be added in the wallet">-->
                                                                  </div>
                                                              
                                                                  <button type="button" id="btnStart" class="btn btn-default">Add Amount</button>
                                                        </form>
                                          
        											  </div>	<!--mywallet_form-->
                                                      
                                     </div>
                                    
									<div class="clearfix"></div><br><br>
									<div class="col-sm-2"></div>
									<div class="well col-sm-8">

									<div class="col-sm-3"></div>
									<div class="col-sm-6 text-center">
							          Update my wallet automatically by $
									</div>
									  <div class="clearfix"></div><br>
								  	<div class="col-sm-4"></div>

								    <div class="col-sm-3">
									   <input class="form-control" maxlength=5
											type="number" id="auto_recharge" name="auto_recharge"
											value="<?php echo(str_replace(".00", "", $mrow["auto_charge_amount"]));?>"> 
									</div>
										<div class="clearfix"></div><br>
									<div class="col-sm-3"></div>
									<div class="col-sm-6 text-center">
										when my balance falls below $5.00
									</div>
                                    <div class="clearfix"></div><br>
									<div class="col-sm-12 text-center">
										 <button type="button" id="btnAuto" class="green_button btn btn-default">Update</button>

									</div>
									</div> <!--well-->
									<div class="col-sm-2"></div>
                            </div>
                    </div>

					<div class="clearfix"></div><Br>
					<?php
					  if ($rowCount > 0) { ?>
						  <h4>My Wallet Credits</h4>
						  
							  <div class="clearfix"></div><br>

							  <table class="table table-striped">
								  <tr>
									  <td><b>Date</b></td>
									  <td><b>Txn.Id</b></td>
									  <td><b>Details</b></td>
									  <td class="text-right"><b>Amount</b></td>
							  	   </tr>

									<?php foreach($rows as $row) { ?>

  									  <tr>
									   <td><?php echo(getNiceDate($row["date"], DATE_NOTIME));?></td>
									   <td><?php echo($row["txn_id"]);?></td>
									   <td><?php echo($row["details"]);?></td>
									   <td class="text-right">
									      <?php echo("$" . number_format($row["amount"],2));?>
									   </td>
							  	   </tr>
									
									<?php }
									?>
									<tr>
										<td></td>
										<td></td>
										<td class="text-right"><b>Total Credits</b></td>
										<td class="text-right">
										<b><?php echo("$" . number_format($totalCredits,2)); ?></b>
										</td>
									</tr>
							  </table>

					<?php } 

					?>
         </div><!--container-->
    </section>


	<!-- dummy gateway POPUP -->
	 <div class="modal bounceIn animated in" id="dummy-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title" id="gatewayLabel">
			<?php if ($srow["eway_sandbox_flag"] == 1)  echo("Dummy Payment Gateway"); else echo("Our Secure Eway Payment Gateway");?></div>
		  </div>
		  <div class="modal-body">
			
				<div class="panel-body">
						<form name=frmCredit id=frmCredit onsubmit="return cvalidate(this);" 
						>
							<br>
							<div class="col-sm-12 text-center" id="divError" style="color:red;">
							</div>
							<br>
							 <div class="col-sm-4">Amount*</div>
                            <div class="col-sm-5">
                                    <input class="form-control" 
										maxlength=4 type="number" id="amount" name="amount"
										placeholder="Enter Amount"/>
				            </div>
							
							<br>
    	                     <div class="clearfix"></div>
							<br>
                            <div class="col-sm-4">Name On Card*</div>
                            <div class="col-sm-8">
                                    <input class="form-control" 
										maxlength=60 type="text" id="namec" name="namec"
										value="<?php echo($_SESSION["name"]);?>"
										placeholder="Name on the card"/>
				            </div>

                            <br>
							<div class="clearfix"></div>
							
							<br>
                            <div class="clearfix"></div>
							<br>
                            <div class="col-sm-4">Credit Card Number*</div>
                            <div class="col-sm-8">
                                    <input class="form-control" 
										maxlength=16 type="text" id="card_number" name="card_number"
										value="<?php if ($srow["eway_sandbox_flag"] == 1)
										   					echo ("4444333322221111");
										   			  else 
													       echo("");
											    ?>"
										placeholder="Card number without hyphens and spaces"/>
				            </div>

                            <br>
							<div class="clearfix"></div>
							<br>
                            <div class="col-sm-4">Card Expiry (MM/YYYY)*</div>
                            <div class="col-sm-4">
                                    <input class="form-control" maxlength=2 id="mm" name="mm"
									value=" <?php if ($srow["eway_sandbox_flag"] == 1) echo("09"); ?>"
									type="text" placeholder="MM" >&nbsp;&nbsp;
                            </div>
                            <div class="col-sm-4">
                                    <input class="form-control" maxlength=4 type="text" id="yyyy" 
									name="yyyy"
									value="<?php if ($srow["eway_sandbox_flag"] == 1) echo("2018");?>"
									placeholder="YYYY" />&nbsp;&nbsp;
                            </div>
							<br>
                            <div class="clearfix"></div>
                            <div class="col-sm-4">CVC*</div>
                            <div class="col-sm-3">
                                    <input class="form-control"  value="<?php if ($srow["eway_sandbox_flag"] == 1) echo("123");?>"
									id="card_cvn" name="card_cvn" maxlength=5 type="text" />&nbsp;&nbsp;
                            </div>
							<br>
                            <div class="clearfix"></div>
                            <div class="col-sm-7 ">
                                <div id="divError" class="color-red  col-sm-12">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                     <button class="btn btn-default btn-lg pull-right" id="btnPay">Make Payment</button>
                             </div>
							 <br>
                            <div class="clearfix"></div><br>
                            <div id="imgLoader" class="progress progress-striped active" style="display:none;">
                                 <div class="progress-bar progress-bar-red animate-progress-bar" role="progressbar" data-percentage="100%" style="width: 100%"></div>
                            </div>

                    </div> <!--panel-body-->
					</form>
					<?php if ($srow["eway_sandbox_flag"] != 1) { ?>
						<script src="https://secure.ewaypayments.com/scripts/eCrypt.min.js"></script>
					<?php } ?>
		  	
			<div class="clearfix"></div>
			<div class="col-sm-2"></div>
			<div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" id="btnModalClose" class="btn btn-sm" data-dismiss="modal">Close</button>
		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>


	<!-- token usage POPUP -->
	 <div class="modal bounceIn animated in" id="token-popup" tabindex="-1" role="dialog" aria-labelledby="catalogLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<div class="modal-title" id="errorLabel">Automatic Card Charge</div>
		  </div>
		  <div class="modal-body">
			
				<div class="panel-body">
						<form name=frmToken id=frmToken onsubmit="return tvalidate(this);">
							<br>
							<div class="col-sm-12 text-center" id="divError2" style="color:red;">
							</div>
							<br>
							 <div class="col-sm-4">Amount*</div>
                            <div class="col-sm-5">
                                    <input class="form-control" 
										maxlength=4 type="number" id="amount2" name="amount2"
										placeholder="Enter Amount"/>
				            </div>
							<?php if (false) { ?>
							 <div class="clearfix"></div><br>
                            <div class="col-sm-4">
							Card CVC*
							<br><small>(Reqd.for security)</small>
							</div>
                            <div class="col-sm-3">
                                    <input class="form-control"  value="123"
									id="cvv2" name="cvv2" maxlength=5 type="text"  />&nbsp;&nbsp;
                            </div>
							<br>
                            <div class="clearfix"></div>
							
							<br>
							<?php } ?>
    	                     <div class="clearfix"></div>
							<br><br>
                            <div class="col-sm-7 ">
							    <small>Your card details are stored in our Secure Payment Gateway. It will be automatically charged the amount you enter here. OR </small><br>
								<a href="#" onclick="useCard(); return false;">Pay via Manual Card Payment</a>
                            </div>
                            <div class="col-sm-5">
                                     <button class="btn btn-default btn-lg pull-right" id="btnPay2">Charge Card</button>
                             </div>
                            <div class="clearfix"></div><br>
                            <div id="imgLoader2" class="progress progress-striped active" style="display:none;">
                                 <div class="progress-bar progress-bar-red animate-progress-bar" role="progressbar" data-percentage="100%" style="width: 100%"></div>
                            </div>

                    </div> <!--panel-body-->
					</form>

		  	
			<div class="clearfix"></div>
			<div class="col-sm-2"></div>
			<div class="clearfix"></div>

		  </div>
		  <div class="modal-footer">
			<button type="button" id="btnModalClose2" class="btn btn-sm" data-dismiss="modal">Close</button>
		  </div>
		</div><!-- modal-content -->
	  </div><!-- modal-dialog -->
	</div>


 <!-- success  Modal -->
<div id="success-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="window.location='my-wallet';">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Wallet Updated</h4>
			 <div class="col-sm-12 ">
			 	<b>Your wallet has been successfully updated</b>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>


  <!-- error Modal -->
<div id="error-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"  onclick="window.location='my-wallet';"	>&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Wallet Error </h4>
			 <div class="col-sm-12 bg-danger" id="errorMessage">
			 	
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>

 <!-- success  auto Modal -->
<div id="success-auto-modal" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="window.location='my-wallet';">&times;</button>
      
      </div>
      <div class="modal-body">
             <h4>Wallet Settings</h4>
			 <div class="col-sm-12 ">
			 	<b>Your wallet auto-charge amount has been successfully updated</b>
			 </div>
             <div class="clearfix"></div><br>        
      </div>
      
    </div>

  </div>
</div>


<script>
<?php 
	if ($error != "") 
		echo("var error_message=\"" . $error . "\";"); 
	else
		echo("var error_message=\"" . "" . "\"; "); 
	
	if ($success != "") 
		echo(" var success_message=\"" . $success . "\"; "); 
	else
		echo(" var success_message=\"" . "" . "\"; "); 
	
	if ($checkCart && $checkCart[0]["user_id"] == $userId) 
		echo(" var orderInProcess = 1;");
	else
		echo(" var orderInProcess = 0;");

	if ($tokenId == null || $tokenId == "") 
		echo("var tokenId=null;");
	else
		echo("var tokenId = '" . $tokenId . "';");

?>
</script>

<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
<?php
  if ($srow["eway_sandbox_flag"] != 1)
  	echo("<script> var sandbox = 0; </script>");
  else
    echo("<script> var sandbox = 1; </script>");
?>
<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/my-wallet.js"></script>

</body>
</html>
