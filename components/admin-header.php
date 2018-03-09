  <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="dashboard.php"><b>JACK & JILL CATERING</b></a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
               <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

		     <div class="navbar-default sidebar" role="navigation" <?php if ($_SESSION["admin_id"] < 1) echo(" style=\"display:none;\""); ?>>
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                         <li>
                            <a <?php if ($pageName == "dashboard") echo(" class='active'");?> href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
   			<li>
                            <a <?php if ($pageName == "settings") echo(" class='active'");?> href="settings.php"><i class="fa fa-gears fa-fw"></i> Settings</a>
                        </li>

			<li>
                            <a <?php if ($pageName == "menu" || $pageName == "edit-product") echo(" class='active'");?> href="menu.php"><i class="fa fa-coffee fa-fw"></i> Setup Menu</a>
                        </li>
			<li>
                            <a <?php if ($pageName == "categories" || $pageName == "edit-category") echo(" class='active'");?> href="categories.php"><i class="fa fa-files-o fa-fw"></i> Menu Categories</a>
                        </li>
			<li>
                            <a <?php if ($pageName == "meal-deal") echo(" class='active'");?> href="meal-deal.php"><i class="fa fa-star fa-fw"></i> Meal Deal</a>
                        </li>
						
			<li>
                            <a <?php if ($pageName == "allergies" || $pageName == "edit-allergy") echo(" class='active'");?> href="allergy-master.php"><i class="fa fa-warning fa-fw"></i> Allergies List</a>
                        </li>
						
			<li>
                            <a <?php if ($pageName == "schools" || $pageName == "edit-school") echo(" class='active'");?> href="schools.php"><i class="fa fa-child fa-fw"></i> Schools</a>
                        </li>
			<li>
                            <a <?php if ($pageName == "users") echo(" class='active'");?> href="users.php"><i class="fa fa-user fa-fw"></i> Users</a>
                        </li>
						
			<li>
                            <a <?php if ($pageName == "orders") echo(" class='active'");?> href="orders.php"><i class="fa fa-files-o fa-fw"></i> Orders</a>
			</li>
			<li>
                            <a <?php if ($pageName == "subscriptions") echo(" class='active'");?> href="subscriptions.php"><i class="fa fa-calendar fa-fw"></i> Subscriptions</a>
			</li>
				
			<li>
                            <a <?php if ($pageName == "containers") echo(" class='active'");?> href="containers.php"><i class="fa fa-truck fa-fw"></i> Containers</a>
			</li>

			<?php if (false) { ?>
			<li>
                            <a <?php if ($pageName == "coupons") echo(" class='active'");?> href="coupons.php"><i class="fa fa-sticky-note-o fa-fw"></i> Coupons</a>
			</li>


                        <li>
                            <a href="#"><i class="fa fa-binoculars fa-fw"></i> Reports<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a  href=#>Report 1</a>
                                </li>
				<li>
				    <a href=#>Report 2</a>		
				</li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>

  			<li>
                            <a href="#"><i class="fa fa-area-chart fa-fw"></i> Data Analysis<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a  href=#>Report 1</a>
                                </li>
				<li>
				    <a href=#>Report 2</a>		
				</li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
			<?php } ?>
 		    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>


