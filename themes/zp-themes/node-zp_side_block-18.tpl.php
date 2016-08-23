<!-- <div class="test">side menu my</div> -->
<?php

	drupal_add_js('sites/all/modules/_ZP_redefined/_Menu/exp_menu/exp-menu.js'); 
	//drupal_add_css('sites/all/modules/_ZP_redefined/_Menu/exp_menu/exp-menu.css');

	drupal_add_js("$(document).ready(function() {
	initExpMenu('ul#exp-side-menu ul')
	});", 'inline');

?>
	
<div id="side_menu"><?php print $zp_side_menu_01 ?></div>
