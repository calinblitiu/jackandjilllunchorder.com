<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();

	$pageName = "home";
	$pageTitle = "Jack & Jill - My Wallet";
	require_once("includes/globals.php");

	$userId = $_SESSION["user_id"];
	if ($userId == null)
		$userId = 0;
		


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo($pageTitle);?></title>

<?php require_once($g_docRoot . "components/styles.php"); ?>
</head>
<body>
<?php require_once($g_docRoot . "components/header.php"); ?>
    
    
  
    <section class="my_profilepg">
        <div class="container">
        			<div id="horizontalTab">
                            <ul class="resp-tabs-list">
                              <li>Dashboard</li>
                             <li>Students</li>
                           <li>Orders</li>
                             <li>Address</li>
                            <li>My Wallet</li>
                            <li>My Profile</li>
                            </ul>
                            <div class="resp-tabs-container">
                                    
                                      <div>
                                              <div class="tab_tittle">
                                                                <h2>Dashboard</h2>                                          
                                               </div>
                                               
                                               <div class="dashbrd_items">
                                               			<ul>
                                                        		<li>
                                                                		<div class="dashbrd_img">
                                                                                <div class="dashbrd_info">100 Students</div>
                                                                        </div>
                                                                </li>
                                                                <li>
                                                                		<div class="dashbrd_img">
                                                                                <div class="dashbrd_info">50 Orders</div>
                                                                        </div>
                                                                </li>
                                                                <li>
                                                                		<div class="dashbrd_img">
                                                                                <div class="dashbrd_info">$280 Wallet</div>
                                                                        </div>
                                                                </li>                                                       
                                                        </ul>
                                               </div>
                                    </div><!--dashboard-->
                                    
                                    <div>
          <div class="tab_tittle">
            <h2>Students Listing</h2>
            <span><a href="#">Add Student</a></span> </div>
            
            <div class="student_search">
            		<form>
                    			<input type="text" placeholder="search">
                                <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>                    
                    </form>
            </div>
          <div class="students_list">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Student Name</th>
                    <th>School Name</th>
                    <th>Class</th>
                    <th>Allergies</th>
                    <th>&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>John</td>
                    <td>Ancaster Meadow Elementary School</td>
                    <td>1</td>
                    <td>Egg</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>Jammie</td>
                    <td>L.M.O  Elementary School</td>
                    <td>3</td>
                    <td>Egg, Cake</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>John</td>
                    <td>Ancaster Meadow Elementary School</td>
                    <td>2</td>
                    <td>Dark Choclate</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>Jolly</td>
                    <td>L.M.O  Elementary School</td>
                    <td>2</td>
                    <td>Meat</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>Jolly 0.98</td>
                    <td>Ancaster Meadow Elementary School</td>
                    <td>2</td>
                    <td>Egg</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>Jammie</td>
                    <td>L.M.O  Elementary School</td>
                    <td>1</td>
                    <td>Egg, Cake</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>Liza</td>
                    <td>Ancaster Meadow Elementary School</td>
                    <td>1</td>
                    <td>Dark Choclate</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                  <tr>
                    <td>Niya</td>
                    <td>L.M.O  Elementary School</td>
                    <td>1</td>
                    <td>Meat</td>
                    <td><a href="#">View Past  Orders</a>, <a href="#">Order Now</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="prod-pagination">
                      <div class="row">
                      <div class="col-md-12">
                      <nav aria-label="Page navigation">
  <ul class="pagination">
    <li>
      <a href="#" aria-label="Previous">
        <span aria-hidden="true">Prev</span>
      </a>
    </li>
    <li class="active"><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li>
      <a href="#" aria-label="Next">
        <span aria-hidden="true">Next</span>
      </a>
    </li>
  </ul>
</nav>
                      
                      
                      </div>
                      </div>
                      </div>
          <!--students_list--> 
          
        </div><!--students-->
                                    
                                                <div>
                                            				     <div class="tab_tittle">
                                                                			<h2>My Orders</h2>                                          
                                               						</div>
                                                                    
                                                                    <div class="orders_info">
                                                                    			<ul>
                                                                                		<li class="progrs_dtls">
                                                                                        		
                                                                                                <div class="ordr_img"><img src="images/pro5.jpg"></div>     
                                                                                                <div class="orders-dtls">
                                                                                                		<div class="ordr-status"><h4>In Progress</h4></div>
                                                                                                        <h3>Order No. 1442381<small> (Will be delivered on <span>tuesday, Nov 5th</span>)</small></h3>
                                                                                                        <h4>Cheese Sticks, <small>Ordered For Tinku</small></h4>
                                                                                                        <h5>For Recess</h5>
                                                                                                        
                                                                                                        <div class="price_iem">
                                                                                                        		<span>Rs.: <b>1,720/-</b></span>    <span>Items: <b>5</b></span>                                                                                                        
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="View_dtls"><a href="#ordr_vdtels" data-toggle="modal">View Details</a></div>
                                                                                                
                                                                                                </div>
                                                                                        
                                                                                        </li> <!--dtlinfo-->
                                                                                        
                                                                                        <li class="canceld_dtls">
                                                                                        		
                                                                                                <div class="ordr_img"><img src="images/pro3.jpg"></div>     
                                                                                                <div class="orders-dtls">
                                                                                                		<div class="ordr-status"><h4>Canceled</h4></div>
                                                                                                        <h3>Order No. 1442381<small> (Will be delivered on <span>tuesday, Nov 5th</span>)</small></h3>
                                                                                                        <h4>Cheese Sticks, <small>Ordered For Tinku</small></h4>
                                                                                                        <h5>For Recess</h5>
                                                                                                        
                                                                                                        <div class="price_iem">
                                                                                                        		<span>Rs.: <b>1,720/-</b></span>    <span>Items: <b>5</b></span>                                                                                                        
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="View_dtls"><a href="#ordr_vdtels" data-toggle="modal">View Details</a></div>
                                                                                                
                                                                                                </div>
                                                                                        
                                                                                        </li> <!--dtlinfo-->
                                                                                        
                                                                                        
                                                                                        <li class="deliver_dtls">
                                                                                        		
                                                                                                <div class="ordr_img"><img src="images/pro7.jpg"></div>     
                                                                                                <div class="orders-dtls">
                                                                                                		<div class="ordr-status"><h4>Delivered</h4></div>
                                                                                                        <h3>Order No. 1442381<small> (Will be delivered on <span>tuesday, Nov 5th</span>)</small></h3>
                                                                                                        <h4>Cheese Sticks, <small>Ordered For Tinku</small></h4>
                                                                                                        <h5>For Recess</h5>
                                                                                                        
                                                                                                        <div class="price_iem">
                                                                                                        		<span>Rs.: <b>1,720/-</b></span>    <span>Items: <b>5</b></span>                                                                                                        
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="View_dtls"><a href="#ordr_vdtels" data-toggle="modal">View Details</a></div>
                                                                                                
                                                                                                </div>
                                                                                        
                                                                                        </li> <!--dtlinfo-->
                                                                                
                                                                                
                                                                                </ul>
                                                                    </div>
                                                                    
                                                                   
                                                </div> <!--order-->
                                    
                                    <div>
                                    <p>Suspendisse blandit velit Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis Integer laoreet placerat suscipit. Sed sodales scelerisque commodo. Nam porta cursus lectus. Proin nunc erat, gravida a facilisis quis, ornare id lectus. Proin consectetur nibh quis urna gravid urna gravid eget erat suscipit in malesuada odio venenatis.</p>
                                    </div>
                                     <div>
                                    				<div class="tab_tittle walet_blanc">
                                                                    <h2>My Wallet</h2>
                                                                    <span>Your Wallet balance is <a href="#"> $170</a></span> </div>
            
                                    				
                                                    <div class="addstu_form my_wallet">
                                                    		
                                                            <div class="recharge_sucss"><h3>Your wallet has been recharge successfully with $150</h3></div>
                                                    
                                                    <h4>Recharge your wallet</h4>
                                            			<form>
                                                                
                                                                   <div class="form-group">
                                                                    <input type="password" class="form-control" id="" placeholder="Enter amount to be added in the wallet">
                                                                  </div>
                                                              
                                                                  <button type="submit" class="btn btn-default">Add Amount</button>
                                                        </form>
                                          
        											  </div>	<!--mywallet_form-->
                                                      
                                     </div>
                                    
                                     <div>
                                    			
                                                <div class="tab_tittle">
                                                		<h2>My Profile</h2> <span><a href="#">Add Student</a></span>                                                
                                                </div>
                                                
                                                <div class="profile_links">
                                                		     <ul><li class="active"><a href="#">My Profile</a></li>
                                                                 <li><a href="#">Security</a></li>
                                                                  <li><a href="#">Notification</a></li>  
                                                                  </ul>                                              
                                                </div>
                                                
                                                
                                                <div class="profile_info">
                                                	<form>
                                                
                                                												<div class="box">
                                                                    <input type="file" name="file-5[]" id="file-5" class="inputfile inputfile-4" data-multiple-caption="{count} files selected" multiple />
                                                                    <label for="file-5"><img src="images/up_img.png"> <span>Browse Image</span></label>
                                                                         
                                                                </div>
                                                                
                                                                <div class="personal_info">
                                                                <h4>Personal Information</h4>
                                                                		
                                                                		<div class="row">
                                                                                     <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="email" placeholder="First Name*">
                                                                                      </div>
                                                                                       <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="email" placeholder="Last Name*">
                                                                                      </div>
                                                                                      <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="email" placeholder="Mobile No.*">
                                                                                      </div>
                                                                                      <div class="form-group col-sm-6">
                                                                                          <input type="text" class="form-control" id="email" placeholder="Email ID*">
                                                                                      </div>
                                                                           </div>
                                                                              
                                                                              <div class="sav_btn"><button type="submit">Save</button></div>
                                                                              
                                                                </div>
                                                                
                                                         </form>
                                                </div><!--profile_info-->
                                                
                                    </div><!--profile-->
                                    
                            </div>
                    </div>
         </div><!--container-->
    </section>

   
<?php require_once($g_docRoot . "components/footer.php"); ?>
<?php require_once($g_docRoot . "components/scripts.php"); ?>

</body>
</html>
