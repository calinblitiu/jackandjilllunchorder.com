<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - Home";
        
	require_once("includes/globals.php");
        require_once($g_docRoot."classes/products.php");
        require_once($g_docRoot . "classes/categories.php");

	$products = new Products($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	$cats = new Categories($g_docRoot, $g_connServer, $g_connUserid, $g_connPwd, $g_connDBName);
	
	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		
	//get categories
	$catCount = $cats->getCount();
	$catRows = $cats->getList( 0, $catCount, "name asc");

	// get menu items
	$rowCount = $products->getCount($name, null, null, null, null, null);
	$rows = $products->getList($name, null, null, null, null, null, 0, $rowCount, $sort);

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
 .meals_img img {max-width:100%; max-height:213px; min-width:100%; min-height:213px;}
</style>

</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
    <section class="main_slider">
    			<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
  </ol>

  <!-- Wrapper for slides -->
  
  <div class="carousel-inner">
    <div class="item active">
      <img src="images/slide1.jpg" alt="">
              <div class="container">

 
                  <div class="carousel-caption"> <!--<h1>Will rotate the term specials. </h1>-->  <h2>Order yummylicious food for your champs in a matter of seconds!</h2>
                  </div>
              </div>
    </div>

    <div class="item">
     <img src="images/slide2.jpg" alt="">
           <div class="container"><div class="carousel-caption">   
           <h2>Tantalising taste buds of your little ones with fresh and delicious food!</h2>
          </div>
      </div>
    </div>

    <div class="item">
      <img src="images/slide3.jpg" alt="">
          <div class="container">
               <div class="carousel-caption">  
               <h2>Satisfying your kids’ appetite with rich, tasty preparations!</h2>
              </div>
      </div>
  </div>
 

  <!-- Left and right controls -->
 <!-- <a class="left carousel-control" href="#myCarousel" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
  </a>-->
</div>
    </section>
    
    
    <section class="wlcm">
		<div class="container">
        		<div class="main_title"><h2>Welcome to Jack-Jill</h2></div>
                
                <div class="welcm-txt">
                		<p></p>
                </div>
                <div class="clearfix"></div>
                <div class="srch-meals">
                		<form name=frmSearch id=frmSearch onsubmit="return xvalidate(this);">
                        		<input type="text" placeholder="Search Your Meal" maxlength=50
										id=search name=search>
                                <button type="submit"  class="gobtn">Go</button>
                        </form>
                </div>
        
        </div>    
    </section>
    
    <section class="our_srvcs">
            <div class="container">
                		<div class="lft_titlmarg">
                        <div class="row">
                        			<div class="col-lg-3 col-md-3 col-sm-4">
                                    			<div class="lft_title">
                                                		<h2>Our Services -</h2>
                                                </div>                                    
                                    </div>
                                    
                                    <div class="col-lg-9 col-md-9 col-sm-8">
                                    			<div class="srvc_info">
                                                		<p>Priced between $1-$5 so everyone can afford to have a lunch order.</p>
                                                </div>                                    
                                    </div>
                        
                        </div><!--row-->
                        
                        </div>
                        
                        			<div class="srvc-crosl">
                                    			<div id="normal-imglist" class="util-carousel normal-imglist">
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc1.jpg">
                                                                        <div class="srvc_dtlcontt"><h4>Book Recess Online</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc2.jpg">
                                                                         <div class="srvc_dtlcontt"> <h4>Book Lunch Online</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc3.jpg">
                                                                        <div class="srvc_dtlcontt">  <h4>Pre book weekly orders for the term</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc4.jpg">
                                                                         <div class="srvc_dtlcontt"> <h4>Pay with e-Wallet, Eway or Cash</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                               <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc1.jpg">
                                                                        <div class="srvc_dtlcontt"><h4>Book Recess Online</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc2.jpg">
                                                                         <div class="srvc_dtlcontt"> <h4>Book Lunch Online</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc3.jpg">
                                                                        <div class="srvc_dtlcontt">  <h4>Pre book weekly orders for the term</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                                <div class="item">
                                                    <a href="#">
                                                                   <div class="srvc_dtl">
                                                                            <img src="images/srvc4.jpg">
                                                                         <div class="srvc_dtlcontt"> <h4>Pay with e-Wallet, Eway or Cash</h4></div>
                                                                </div>
                                                    </a>
                                                </div><!--item-->
                                                
                                        </div>
                                    
                                    </div>
                                        
                        <div class="all_srvc"><a href="#">All Services</a></div>
                    
             </div>  
    </section>
    
     
    <section class="hmcount-bg">
     	<div class="container">    
                    
                    	<div class="wrapper">
   <!-- <div class="counter col_fifth">
      <h2 class="timer count-title count-number" data-to="1000" data-speed="1500"></h2>
       <p class="count-text ">Satisfied customers</p>
    </div>

    <div class="counter col_fifth">
      <h2 class="timer count-title count-number" data-to="988" data-speed="1500"></h2>
      <p class="count-text ">Orders</p>
    </div>

    <div class="counter col_fifth">
      <h2 class="timer count-title count-number" data-to="300" data-speed="1500"></h2>
      <p class="count-text ">Food recipes</p>
    </div>
    
       <div class="counter col_fifth">
      <h2 class="timer count-title count-number" data-to="1800" data-speed="1500"></h2>
      <p class="count-text ">Happy students</p>
    </div>

    <div class="counter col_fifth end">
      <h2 class="timer count-title count-number" data-to="250" data-speed="1500"></h2>
      <p class="count-text ">Subscriptions</p>
    </div>-->
</div>
        
        
    	 </div><!--container-->   
   
    </section>
    
    <section class="our_meals">
            <div class="container">
                		<div class="lft_titlmarg">
                            <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-4">
                                                    <div class="lft_title">
                                                            <h2>Our Meals -</h2>
                                                    </div>                                    
                                        </div>
                                        
                                        <div class="col-lg-9 col-md-9 col-sm-8">
                                                    <div class="srvc_info">
                                                            <p>With the menu prices ranging between $1 to $5, everyone can afford to have a recess and lunch order. Items are updated each
                               School Term so keep and eye out for new fun and yummy items for children and staff !</p>
                                                    </div>                                    
                                        </div>
                            
                            </div><!--row-->
                        </div>
                        
                        			
                        <div class="row">
						      <?php
									   foreach($rows as $row) { 
									      $catIdRows = $cats->getCatsForItem($row["ID"], 0, 1000);
										  $catIds = "";
										  foreach($catIdRows as $catIdRow) {
										    if ($catIds != "")
												$catIds .= ", ";
											$catIds .= $catIdRow["ID"];
										  }
										  $link = "products-list/search/" . makeTextURLSafe($row["name"]) . "/sort/name_asc";
									   ?>
						
                       				 <div class="col-lg-3 col-md-3 col-sm-4">
                        						<div class="meals_info">
                                                			<a href="<?php echo($link);?>"><div class="meals_img">
                                                            		<img src="<?php echo($g_webRoot . "items/files/" . $row["image"]);?>">
                                                                    <h4><?php echo($row["name"]);?></h4>
                                                            </div></a>
                                                            <div class="meals_dtl text-right">
                                                            	<?php if ($userId > 0) { ?>
																	<h5>$ <?php echo(number_format($row["price"],2)); ?></h5>																	
																<?php } ?>

															</div>                                                
                                                </div>
                        			</div><!--col-lg-3 col-md-3 col-sm-4-->
                                    
								<?php } ?>
                                                                       
                        </div>    <!--row-->            
                       
                    
             </div>  
    </section>
    
    
    <section class="client-says">
		<div class="container"> 
    					<div class="hm_title">
                        		<h2>Our Clients’ Words</h2>
                        </div>
                        
                        <div class="row">
      <div class="carousel slide" data-ride="carousel" id="quote-carousel"> 
        <!-- Bottom Carousel Indicators -->
        <ol class="carousel-indicators">
          <li data-target="#quote-carousel" data-slide-to="0" class="active"></li>
          <li data-target="#quote-carousel" data-slide-to="1"></li>
          <li data-target="#quote-carousel" data-slide-to="2"></li>
        </ol>
        
        <!-- Carousel Slides / Quotes -->
        <div class="carousel-inner"> 
          
          <!-- Quote 1 -->
          <div class="item active">
      
              <div class="row">
               
             <div class="col-sm-12">
                  <p></p>
                  <small>Alizeh, Corsica</small> </div>
              </div>
            
          </div>
          <div class="item">
            
              <div class="row">
               
              <div class="col-sm-12">
                  <p> </p>
                  <small>Alizeh, Corsica</small> </div>
              </div>
            
          </div>
          <div class="item">
            
              <div class="row">
             
          <div class="col-sm-12">
                  <p> </p>
                  <small>Alizeh, Corsica</small> </div>
              </div>
            
          </div>
          
          <div class="item">
            
              <div class="row">
                         <div class="col-sm-12">
                  <p> </p>
                  <small>Alizeh, Corsica</small> </div>
              </div>
            
          </div>
          
        </div>
        
        <!-- Carousel Buttons Next/Prev --> 
       <!-- <a data-slide="prev" href="#quote-carousel" class="left carousel-control"><i class="fa fa-chevron-left"></i></a>
        <a data-slide="next" href="#quote-carousel" class="right carousel-control"><i class="fa fa-chevron-right"></i></a>-->
      </div>
    </div>
                        
                        
        </div>    
    </section>
    
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>
 <script src="<?php echo($g_webRoot);?>includes/index.js"></script> 



</body>
</html>
