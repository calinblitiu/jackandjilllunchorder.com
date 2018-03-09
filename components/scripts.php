<?php 
	echo("<script> var webRoot = '" . $g_webRoot . "' </script>");
?>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<script src="<?php echo($g_webRoot);?>js/jquery.min.js"></script> 
<!-- Include all compiled plugins (below), or include individual files as needed --> 

<script src="<?php echo($g_webRoot);?>js/jquery.utilcarousel.min.js"></script> 
<script src="<?php echo($g_webRoot);?>js/bootstrap.min.js"></script> 


<script src="<?php echo($g_webRoot);?>js/menumaker.js"></script>
<script type="text/javascript">
	$("#cssmenu").menumaker({
		title: "",
		format: "multitoggle"
	});
</script>


 <script src="<?php echo($g_webRoot);?>js/bootstrap-datepicker.min.js"></script>
    
    
   
      <script src="<?php echo($g_webRoot);?>js/jquery.nice-select.min.js"></script>
      
         <script>
      		$(document).ready(function() {
  $('select').niceSelect();
});
      
      </script>
    
    
<script>
			$(function() {
				
				$('#normal-imglist').utilCarousel({
					pagination : false,
					navigationText : ['<i class="icon-left-open-big"></i>', '<i class=" icon-right-open-big"></i>'],
						breakPoints : [[1920, 4], [1200, 4], [992, 2], [860, 2], [767, 1], [480, 1]],
					navigation : true,
					rewind : false,
					autoPlay : true 
				});
				
			});
		</script> 
        
        <script src="<?php echo($g_webRoot);?>js/easy-responsive-tabs.js"></script> 
<script>
$(document).ready(function () {
$('#xhorizontalTab').easyResponsiveTabs({
type: 'default', //Types: default, vertical, accordion           
width: 'auto', //auto or any width like 600px
fit: true,   // 100% fit in a container
closed: 'accordion', // Start closed if in accordion view
activate: function(event) { // Callback function if tab is switched
var $tab = $(this);
var $info = $('#tabInfo');
var $name = $('span', $info);
$name.text($tab.text());
$info.show();
}
});
});

</script> 

<script src="<?php echo($g_webRoot);?>js/count.js"></script> 
<script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script> 
<script src="<?php echo($g_webRoot);?>js/custom-file-input.js"></script>

