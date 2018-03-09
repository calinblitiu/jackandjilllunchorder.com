<div class="col-sm-12 bg-black">
<?php
  if ($_SESSION["montly_subs_id"] == null) { ?>
    
	<b><center>FREE ACCOUNT</center></b><br>
	<hr>
	<b>LIST MAKER</b><br>
	Slides Per List: <span class="fg-gray"><?php echo($_SESSION["slides_per_list"]); ?></span><br>
	Max Lists: <span class="fg-gray">
		<?php if ($_SESSION["max_slides"] == "UN") echo("Unlimited"); ?></span>
	</br>

  <?php } else { ?>

	<b>PAID ACCOUNT</b><br>
	Expires On: <?php echo(getNiceDate($_SESSION["monthly_subs_ends"])); ?>
  <?php } ?>
 
 </div>

