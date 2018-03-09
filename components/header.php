
<header>
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-sm-4">
        <div class="logo"> <a href="#"><img src="<?php echo($g_webRoot);?>images/logo.jpg"></a> </div>
      </div>
      <!--col-lg-4-->
      
      <div class="col-lg-8 col-md-8 col-sm-8	">
        <div class="head-right">
          <div class="register-links"> 
            <!--<a href="#"><img src="images/user-icon.png"> Register</a>
                                                        <a href="#">Sign in</a>     --> 
           <!-- <a href="#"><span><img src="images/my-profile_img.jpg" ></span>Welcome John</a> <a href="#" class="nav_wallt">Wallet <span>($280 )</span></a> -->
            
            <div id="cssmenu">
            <ul>
			<?php if ($_SESSION["user_id"]  < 1) { ?>
	            <li><a href="<?php echo($g_webRoot);?>sign-up"><img src="<?php echo($g_webRoot);?>images/user-icon.png"> Register</a></li> 
            	<li><a href="<?php echo($g_webRoot);?>sign-in"><img src="<?php echo($g_webRoot);?>images/user-icon.png"> Sign in</a></li>   
			<?php } else { ?>
			 	<li> <a href="<?php echo($g_webRoot);?>cart"><img src="<?php echo($g_webRoot);?>images/top_carticon.png"> Cart <span id="hcartcount">(<?php echo($_SESSION["cart_count"]);?>)</span></a></li>
	 		     <li> <a href="<?php echo($g_webRoot. "dashboard");?>"><img src="<?php echo($g_webRoot);?>images/user-icon.png"> Dashboard</a></li>
    			  <li> <a href="<?php echo($g_webRoot. "my-wallet");?>"><img src="<?php echo($g_webRoot);?>images/user-icon.png"> Wallet <span> ($ <?php echo(number_format($_SESSION["wallet_balance"],2));?>)</span></a></li>

	   			 <li> <a href="<?php echo($g_webRoot. "logout");?>"><img src="<?php echo($g_webRoot);?>images/user-icon.png"> Logout</a></li>
			</ul>
			<?php } ?>
	<!--<ul>
		<li><a href="#"><img src="images/dashboard_icon.png"></a></li>        
		<li class="has-sub"><span class="submenu-button"></span>
			<a href="#"><span><img src="images/my-profile_img.jpg" ></span>Welcome John</a>
			<ul>
				<li><a href="#">My Profile</a></li>
				<li><a href="#">Dashboard</a></li>
				<li><a href="#">Logout</a></li>
			</ul>
		</li>
		<li> <a href="#"><img src="images/top_carticon.png"> Cart <span>(8)</span></a></li>
		<li><a href="#" class="nav_wallt">Wallet <span>($280 )</span></a> </li>
	</ul>-->
</div>
            
            </div>
          <div class="menu">
            <nav class="navbar navbar-default"> 
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                <!--    <a class="navbar-brand" href="#">Brand</a>--> 
              </div>
              
              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                  <li <?php if ($pageName == "home") echo("class=\"active\""); ?>><a href="<?php echo($g_webRoot);?>">Home <span class="sr-only">(current)</span></a></li>
                  <li <?php if ($pageName == "products-list") echo("class=\"active\""); ?>><a href="<?php echo($g_webRoot);?>products-list">Menu/Orders</a></li>
                  <li <?php if ($pageName == "subscription-plan") echo("class=\"active\""); ?>><a href="<?php echo($g_webRoot);?>subscription-plan">Subscription Plan</a></li>
                  <li <?php if ($pageName == "offline-ordering") echo("class=\"active\""); ?>><a href="<?php echo($g_webRoot);?>offline-ordering">Offline Order</a></li>
                  <li <?php if ($pageName == "about-us") echo("class=\"active\""); ?>><a href="<?php echo($g_webRoot);?>about-us">About Us</a></li>
                </ul>
              </div>
              <!-- /.navbar-collapse --> 
            </nav>
          </div>
        </div>
        <!--head-right--> 
      </div>
      <!--col-lg-4--> 
    </div>
    <!--row--> 
  </div>
  <!--container--> 
</header>
    
