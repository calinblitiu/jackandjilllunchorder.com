	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
	    <br>	
		<div class="col-sm-12 text-center">
		</div>
		<div class="clearfix"></div><br>
			
		<ul class="nav menu">
			<li><a href="logout.php"><i class="fa fa-power-off"></i>&nbsp;<b>LOGOUT</b></a></li>

			<li <?php if ($pageName == "view-profile") echo("class=\"active\"");?>><a href="view-profile.php"><i class="fa fa-user"></i>&nbsp;View Profile</a></li>
			<li <?php if ($pageName == "edit-profile") echo("class=\"active\"");?>><a href="edit-profile.php"><i class="fa fa-edit"></i>&nbsp;Edit Profile</a></li>			
			<li <?php if ($pageName == "social-setup") echo("class=\"active\"");?>><a href="social-setup.php"><i class="fa fa-facebook"></i>&nbsp;Social Media Setup</a></li>		
			<li role="presentation" class="divider"></li>

			<li <?php if ($pageName == "reviews") echo("class=\"active\"");?>><a href="reviews.php"><i class="fa fa-thumbs-o-up"></i>&nbsp;Testimonials Received</a></li>
			<li><a target=_blank href="review.php?id=<?php echo($_SESSION["user_id"]);?>&request=0"><i class="fa fa-comment-o"></i>&nbsp;Submit Test Testimonial</a></li>
			<li <?php if ($pageName == "sendrequest") echo("class=\"active\"");?>><a href="sendrequest.php"><i class="fa fa-send"></i>&nbsp;Request Testimonial</a></li>

			<li role="presentation" class="divider"></li>

			<li <?php if ($pageName == "templates") echo("class=\"active\"");?>><a href="templates.php"><i class="fa fa-files-o"></i>&nbsp;Templates</a></li>

			<li <?php if ($pageName == "requests") echo("class=\"active\"");?>><a href="requests.php"><i class="fa fa-user"></i>&nbsp;View Requests Sent</a></li>


			
		</ul>
	</div><!--/.sidebar-->

