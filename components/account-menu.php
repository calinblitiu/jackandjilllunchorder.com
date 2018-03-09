
<style>

</style>
 <ul class="resp-tabs-list">
        <li <?php if ($pageName == "dashboard") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>dashboard">Dashboard</a></li>
       <li <?php if ($pageName == "student-listing" || $pageName == "add-student") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>student-listing">Students</a></li>
       <li <?php if ($pageName == "orders") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>orders">Orders</a></li>
	   <li <?php if ($pageName == "subscriptions") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>subscriptions">Subscriptions</a></li>
	   
       <?php if (false) { ?>
	   <li <?php if ($pageName == "my-address") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>my-address">Address</a></li>
	   <?php } ?>
      <li <?php if ($pageName == "my-wallet") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>my-wallet">My Wallet</a></li>
      <li <?php if ($pageName == "my-profile") echo("class=\"resp-tab-active\"");?>><a href="<?php echo($g_webRoot);?>my-profile">My Profile</a></li>
   </ul>

