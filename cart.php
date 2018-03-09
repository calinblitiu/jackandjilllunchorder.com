<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - View Your Cart";
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/products.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/categories.php");
	require_once($g_docRoot . "classes/students.php");
	require_once($g_docRoot . "classes/allergies-master.php");
	require_once($g_docRoot . "classes/allergies.php");
	require_once($g_docRoot . "classes/nutrition.php");
	require_once($g_docRoot . "classes/schools.php");
	require_once($g_docRoot . "classes/classes.php");
	require_once($g_docRoot . "classes/cart.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/meal-deal.php");

	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
	if ($userId == 0) {
		header("Location: " . $g_webRoot . "products-list");
		exit;
	}

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$students = new Students($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$amaster = new AllergiesMaster($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$allergies = new Allergies($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$nutrition = new Nutrition($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$schools = new Schools($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$classes = new Classes($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cart = new Cart($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$credits = new Credits($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$mealdeal = new MealDeal($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);	


 	// check for deletion
	if ($_GET["del"] != null && $_GET["del"] != "" && is_numeric($_GET["del"])) {
	
		$cart->delete($_GET["del"]);

		$itemsCount = $cart->getCountForAUser($userId);
		$_SESSION["cart_count"] = $itemsCount;

		header("Location: cart");
		exit;
	}

	// get meal deal
	$mealDealRow = $mealdeal->getRowById("ID", "1");

	// get cart items
	$rowCount = $cart->getCountForAUser($userId);
	$rows = $cart->getGroupedListForAUser($userId, 0, $rowCount);

	// get details of order
	$checkCart = $cart->getListForAUser($userId, 0, 1, "date_desc");
	if ($checkCart && $checkCart[0]["user_id"] == $userId) {
			$studentRow = $students->getRowById("ID" , $checkCart[0]["student_id"]);

			$schoolRow = $schools->getRowbyId("ID", $studentRow["school_id"]);
			$classRow = $classes->getRowById("ID", $studentRow["class_id"]);
		
			$orderMessage = "Order for <b>" . $studentRow["name"] . "</b> Class: " . 
					$classRow["name"] . " of "  . $schoolRow["name"];

	}

	
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>
<style>
  .cart-img img { width:188px; height:124px;}
  #divTable { max-height:600px; overflow-y:auto;}
</style>
<?php require_once($g_docRoot . "components/styles.php"); ?>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
<section class="my_profilepg">
  <div class="container">
    <div class="site-breadcrum">
    <ol class="breadcrumb">
  <li><a href="#" class="active">Cart</a></li>
  <li><a href="#">Delivery</a></li>
  <li><a href="#">Payment</a></li>
</ol>
    </div>
    
   <div class="cart-col">
   <div class="tab_tittle">
            <h2><?php echo($orderMessage); ?></h2>
            <span></span>
			
	</div>
            
            
   <div class="row">
   <div class="col-md-9">
   <div class="cart-table" id="divTable">
   <div class="table-responsive" >
   <table class="table table-striped">
  <thead>
    <tr>
      <th width="220">&nbsp;</th>
      <th>Product</th>
      <th>Price</th>
      <th>Quantity</th>
      <th>Total</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>

  <?php
     if ($rowCount == 0) { ?>
		  <tr><td colspan=6 class="text-center"><h4>No items in cart</h4></td></tr>		 
  <?php }
	?>
   <?php 
	  $currMT = "";
      $cartTotal = 0;
	  $counter = 1;
      foreach($rows as $row) { 
	    $cartTotal += $row["qty"] * $row["price"];

		if ($row["product_id"] == MEAL_DEAL_ITEM_DISPLAY_ID) {
		  $row["image"] = $mealDealRow["image"];
		  $row["productname"] = $mealDealRow["name"];
		}

		if ($row["meal_type"] != $currMT) { 
			$currMT = $row["meal_type"];
		 ?>
	<tr>
	   	<td colspan="6" style="background:#D1C304; color:#fff; text-align:center;" 
				class="cart_tbltitl">
				<h1>
				<?php 
					if ($row["meal_type"] == "R")
						echo("Morning Recess Order 11 am");
					else if ($row["meal_type"] == "L")
						echo("Lunch Time Order");
					else if ($row["meal_type"] == "RL")
						echo("Recess+Lunch");
				?>
				</h1>
		</td>
    </tr>
		 
	<?php
		} ?>

    <tr>
      <td><span class="cart-img"><img src="<?php echo($g_webRoot);?>items/files/<?php echo($row["image"]);?>"></span></td>
      <td><?php echo($row["productname"]);?></td>
      <td class="price" id="price<?php echo($row["ID"]);?>">$<?php echo(number_format($row["price"],2));?></td>
      <td>
      <div class="center">
     
      <div class="input-group">
          <span class="input-group-btn">

		  	<?php
		       if ($row["qty"] == 1)
			     $dis = "disabled=disabled";
			   else
			     $dis = "";
			?>
              <button type="button" class="btn btn-default btn-number" <?php echo($dis);?> data-type="minus" data-field="quant[<?php echo($counter);?>]">
                  <span class="glyphicon glyphicon-minus"></span>
              </button>
          </span>
          <input type="text" name="quant[<?php echo($counter);?>]" id="qty<?php echo($row["ID"]);?>" class="form-control input-number" value="<?php echo($row["qty"]);?>" min="1" max="100">
          <span class="input-group-btn">
              <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="quant[<?php echo($counter);?>]">
                  <span class="glyphicon glyphicon-plus"></span>
              </button>
          </span>
      </div>
      </div>
      
      </td>
      <td><span class="blue-text divItemTotal" id="divItemTotal<?php echo($row["ID"]);?>">$<?php echo(number_format($row["qty"] * $row["price"],2));?></span></td>
      <td><a href="#" onclick="doDel(<?php echo($row["ID"]);?>); return false;" class="closebtn"><i class="fa fa-times-circle" aria-hidden="true"></i>
</a></td>
    </tr>
	<?php
	  $counter ++;
	 }
	?>
    
      
  </tbody>
</table>

   
   </div>
   </div>
   
   </div>
   
   
   <div class="col-md-3">
   <div class="cart-box">
   <div class="cart-icon">
   <img src="<?php echo($g_webRoot);?>images/security-icon.jpg">
   </div>
   
   <div class="cart-total">
   <h1>Cart Total</h1>
   <div class="carttotal-inner">
   <div class="cart-totalcol">
   <div class="cart-col-6">
   Cart Subtotal
   </div>
   
   <div class="cart-col-6">
   <span class="cart-price" id="divSubTotal">$<?php echo(number_format($cartTotal,2));?></span>
   </div>
   
   
   
   
   </div>
   
   
   <div class="cart-subtotal">
   <div class="cart-totalcol">
   <div class="cart-col-6">
  Total
   </div>
   
   <div class="cart-col-6">
   <span class="cart-price" id="divTotal">$<?php echo(number_format($cartTotal,2));?></span>
   </div>
   
     
   </div>
   </div>
   
   <div class="checkout-col">
   <div class="sav_cotinu">
    <?php 
	  if ($rowCount > 0) { ?>
		<button type="button" onclick="doProcess(); return false;">Checkout</button> </div>
		<div class="clearfix"></div><Br>
		<div class="text-center">
			<a href="#" id="lnkNotes">Notes or Instructions</a>
		</div>
	  <?php } ?>
   </div>
   
   
   </div>
   </div>
   
   </div>
   </div>
   
   </div>         
            
            
            
            
   </div> 
    
    
    
    
  </div>
  <!--container--> 
</section> 

<!-- subscription Modal -->
<div id="subscription_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="sub_planinfo">
                    		<h4>Subscription Plan</h4>
                           <p> Pre book orders with our <b>‘Set & Forget’</b> plan.
Each week you will automatically receive the order you select for the
nominated student. You will be charged each Friday before the order is
delivered and you can change or update an order at anytime before the
weekly order cutoff time, ordering made easy!</p>
						<div class="yes_no">
                        		<button type="button" 
									class="no_subs" id="btnNoSubs">No</button>
                                <button onclick="window.location='<?php echo($g_webRoot);?>cart-to-subscription';" type="button"  class="yes_subs">Yes</button>
                        </div>
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>
</div>

 <!-- subscription Modal -->



 <!-- noBalance Modal -->
<div id="nobalance_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="sub_planinfo">
                    		<h4>Wallet Balance</h4>
                           <p id="pmessage"> 
						   </p>
						<div class="yes_no">
                        		<button data-dismiss="modal" 
								   type="button" class="no_subs">Ok</button>
                                <button onclick="window.location='<?php echo($g_webRoot);?>my-wallet';" type="button"  class="yes_subs">Add Funds</button>
                        </div>
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>
</div>

 <!-- ceckout Modal -->
<div id="checkout_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
      
      </div>
      <div class="modal-body">
      				<div class="sub_planinfo">
                    		<h4>Processing Checkout</h4>

						  <div id="imgLoader" class="progress progress-striped active">
                                 <div class="progress-bar progress-bar-red animate-progress-bar" role="progressbar" data-percentage="100%" style="width: 100%"></div>
                            </div>
                    </div>
					<div class="clearfix"></div><Br>
      
                 
      </div>
      
    </div>

  </div>
</div>



 <!-- checkoutsuccess Modal -->
<div id="checkoutsuccess_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="sub_planinfo">
                    		<h4>Checkout Successfull</h4>
                           <p> 
						   	  <b>Congratulations.</b><br><br>
							  Your Order has been confirmed.<br><br>
						   </p>
						<div class="yes_no">
                        	  <button onclick="window.location='<?php echo($g_webRoot);?>orders';" type="button"  class="yes_subs">View Orders</button>
                        </div>
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>
</div>

 <!-- notes Modal -->
<div id="notes_popup" class="modal fade" role="dialog">
  <div class="modal-dialog subspopup">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      
      </div>
      <div class="modal-body">
      				<div class="sub_planinfo">
                    		<h4>Notes Or Special Instructions</h4>
							<form name=frmNotes id="frmNotes">
							   <div class="col-sm-12 text-center">
							     <textarea id="notes" name="notes" rows="5" style="width:100%"></textarea>
							   </div>
							</form>
						<div class="yes_no">
                        	  <button onclick="saveNotes();return false;" type="button"  class="yes_subs">Save</button>
                        </div>
					
                    </div>
      
                 
      </div>
      
    </div>

  </div>


<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>


<script src="<?php echo($g_webRoot);?>includes/jquery.formError.js"></script>
<script src="<?php echo($g_webRoot);?>includes/cart.js"></script> 


<script>
//plugin bootstrap minus and plus
//http://jsfiddle.net/laelitenetwork/puJ6G/
$('.btn-number').click(function(e){
    e.preventDefault();
    
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            } else
			  $(this).attr('disabled', false);


        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            } else
			   $(this).attr('disabled', false);


        }
    } else {
        input.val(0);
    }
});
$('.input-number').focusin(function(){
   $(this).data('oldValue', $(this).val());
});
$('.input-number').change(function() {
    
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    
    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }

	recalc();
    
    
});
$(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

</script>

</body>
</html>
